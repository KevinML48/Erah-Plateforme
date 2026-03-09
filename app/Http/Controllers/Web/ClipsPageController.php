<?php

namespace App\Http\Controllers\Web;

use App\Application\Actions\Clips\AddClipCommentAction;
use App\Application\Actions\Clips\DeleteClipCommentAction;
use App\Application\Actions\Clips\ShareClipAction;
use App\Application\Actions\Clips\ToggleClipFavoriteAction;
use App\Application\Actions\Clips\ToggleClipLikeAction;
use App\Http\Controllers\Controller;
use App\Models\Clip;
use App\Models\ClipComment;
use App\Models\ClipFavorite;
use App\Models\ClipLike;
use App\Models\ClipVoteCampaign;
use App\Services\ClipRewardService;
use App\Services\PrioritizeClipComments;
use App\Services\SupporterAccessResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class ClipsPageController extends Controller
{
    public function index(Request $request): View
    {
        $sort = $request->query('sort', 'recent');
        $query = Clip::query()
            ->published()
            ->with('createdBy:id,name');

        if ($sort === 'popular') {
            $query
                ->orderByDesc('likes_count')
                ->orderByDesc('favorites_count')
                ->orderByDesc('comments_count')
                ->orderByDesc('published_at');
        } else {
            $query->orderByDesc('published_at');
        }

        $clips = $query->paginate(12)->withQueryString();
        $clipIds = $clips->getCollection()->pluck('id')->all();
        $userId = auth()->id();

        $likedIds = [];
        $favoriteIds = [];

        if ($userId !== null && count($clipIds) > 0) {
            $likedIds = ClipLike::query()
                ->where('user_id', $userId)
                ->whereIn('clip_id', $clipIds)
                ->pluck('clip_id')
                ->all();

            $favoriteIds = ClipFavorite::query()
                ->where('user_id', $userId)
                ->whereIn('clip_id', $clipIds)
                ->pluck('clip_id')
                ->all();
        }

        return view('pages.clips.index', [
            'clips' => $clips,
            'sort' => $sort,
            'likedIds' => $likedIds,
            'favoriteIds' => $favoriteIds,
        ]);
    }

    public function show(
        Request $request,
        string $slug,
        ClipRewardService $clipRewardService,
        PrioritizeClipComments $prioritizeClipComments,
        SupporterAccessResolver $supporterAccessResolver
    ): View
    {
        $clip = Clip::query()
            ->published()
            ->with('createdBy:id,name')
            ->where('slug', $slug)
            ->firstOrFail();

        $actualCommentsCount = (int) $clip->comments()->count();
        if ((int) $clip->comments_count !== $actualCommentsCount) {
            $clip->comments_count = $actualCommentsCount;
            $clip->save();
        }

        $userId = auth()->id();
        $requestedCommentsPage = max(1, (int) $request->integer('comments_page', 1));
        $comments = $prioritizeClipComments->execute($clip, 10, $requestedCommentsPage);
        $clipRewardService->recordView(
            clip: $clip,
            user: auth()->user(),
            sessionId: $request->session()->getId(),
            ipHash: hash('sha256', (string) $request->ip()),
        );

        if ($comments->isEmpty() && $actualCommentsCount > 0 && $requestedCommentsPage > 1) {
            $comments = $prioritizeClipComments->execute($clip, 10, 1);
        }

        if ($comments->isEmpty() && $actualCommentsCount > 0) {
            $comments = $clip->comments()
                ->whereNull('parent_id')
                ->where('status', ClipComment::STATUS_PUBLISHED)
                ->with([
                    'user:id,name,avatar_path',
                    'replies' => fn ($query) => $query
                        ->where('status', ClipComment::STATUS_PUBLISHED)
                        ->with('user:id,name,avatar_path')
                        ->orderBy('id'),
                ])
                ->orderByDesc('id')
                ->paginate(10, ['*'], 'comments_page', 1);

            $comments->getCollection()->transform(function (ClipComment $comment): ClipComment {
                $comment->supporter_priority = 0;

                return $comment;
            });
        }

        $isSupporterActive = $supporterAccessResolver->hasActiveSupport(auth()->user());
        $campaigns = ClipVoteCampaign::query()
            ->active()
            ->whereHas('entries', fn ($query) => $query->where('clip_id', $clip->id))
            ->withCount('votes')
            ->with([
                'entries.clip',
                'votes' => fn ($query) => $query->where('user_id', $userId),
            ])
            ->orderBy('ends_at')
            ->get();
        $reactionCounts = $clip->supporterReactions()
            ->selectRaw('reaction_key, COUNT(*) as total')
            ->groupBy('reaction_key')
            ->pluck('total', 'reaction_key')
            ->all();
        $userReactionKeys = $userId
            ? $clip->supporterReactions()->where('user_id', $userId)->pluck('reaction_key')->all()
            : [];

        return view('pages.clips.show', [
            'clip' => $clip,
            'comments' => $comments,
            'isLiked' => ClipLike::query()->where('clip_id', $clip->id)->where('user_id', $userId)->exists(),
            'isFavorited' => ClipFavorite::query()->where('clip_id', $clip->id)->where('user_id', $userId)->exists(),
            'isSupporterActive' => $isSupporterActive,
            'supporterCampaigns' => $campaigns,
            'supporterReactionOptions' => ClipSupporterController::reactionOptions(),
            'supporterReactionCounts' => $reactionCounts,
            'userSupporterReactionKeys' => $userReactionKeys,
        ]);
    }

    public function favorites(): View
    {
        $userId = auth()->id();
        $clips = Clip::query()
            ->published()
            ->with('createdBy:id,name')
            ->whereIn('id', function ($query) use ($userId) {
                $query->select('clip_id')
                    ->from('clip_favorites')
                    ->where('user_id', $userId);
            })
            ->orderByDesc('published_at')
            ->paginate(12)
            ->withQueryString();

        return view('pages.clips.favorites', [
            'clips' => $clips,
        ]);
    }

    public function like(int $clipId, ToggleClipLikeAction $toggleClipLikeAction): RedirectResponse
    {
        $clip = Clip::query()->published()->whereKey($clipId)->firstOrFail();

        try {
            $toggleClipLikeAction->like(auth()->user(), $clip);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Clip like.');
    }

    public function unlike(int $clipId, ToggleClipLikeAction $toggleClipLikeAction): RedirectResponse
    {
        $clip = Clip::query()->published()->whereKey($clipId)->firstOrFail();

        try {
            $toggleClipLikeAction->unlike(auth()->user(), $clip);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Like retire.');
    }

    public function favorite(int $clipId, ToggleClipFavoriteAction $toggleClipFavoriteAction): RedirectResponse
    {
        $clip = Clip::query()->published()->whereKey($clipId)->firstOrFail();

        try {
            $toggleClipFavoriteAction->favorite(auth()->user(), $clip);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Clip ajoute aux favoris.');
    }

    public function unfavorite(int $clipId, ToggleClipFavoriteAction $toggleClipFavoriteAction): RedirectResponse
    {
        $clip = Clip::query()->published()->whereKey($clipId)->firstOrFail();

        try {
            $toggleClipFavoriteAction->unfavorite(auth()->user(), $clip);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Favori retire.');
    }

    public function comment(
        Request $request,
        int $clipId,
        AddClipCommentAction $addClipCommentAction
    ): RedirectResponse {
        $validated = $request->validate([
            'body' => ['required', 'string', 'min:1', 'max:2000'],
            'parent_id' => ['nullable', 'integer'],
        ]);

        $clip = Clip::query()->published()->whereKey($clipId)->firstOrFail();

        try {
            $addClipCommentAction->execute(
                auth()->user(),
                $clip,
                $validated['body'],
                $validated['parent_id'] ?? null,
            );
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Commentaire ajoute.');
    }

    public function deleteComment(
        int $clipId,
        int $commentId,
        DeleteClipCommentAction $deleteClipCommentAction
    ): RedirectResponse {
        $comment = ClipComment::query()
            ->where('clip_id', $clipId)
            ->whereKey($commentId)
            ->firstOrFail();

        $deleteClipCommentAction->execute(auth()->user(), $comment);

        return back()->with('success', 'Commentaire supprime.');
    }

    public function share(
        Request $request,
        int $clipId,
        ShareClipAction $shareClipAction
    ): RedirectResponse {
        $validated = $request->validate([
            'channel' => ['nullable', 'string', 'max:30'],
        ]);
        $clip = Clip::query()->published()->whereKey($clipId)->firstOrFail();

        $share = $shareClipAction->execute(auth()->user(), $clip, $validated['channel'] ?? 'link');

        return back()->with('success', 'Lien partage: '.$share->shared_url);
    }
}
