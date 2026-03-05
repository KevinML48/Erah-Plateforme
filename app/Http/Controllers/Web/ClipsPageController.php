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

    public function show(string $slug): View
    {
        $clip = Clip::query()
            ->published()
            ->with('createdBy:id,name')
            ->where('slug', $slug)
            ->firstOrFail();

        $comments = ClipComment::query()
            ->where('clip_id', $clip->id)
            ->with('user:id,name,avatar_path')
            ->orderByDesc('id')
            ->paginate(10);

        $userId = auth()->id();

        return view('pages.clips.show', [
            'clip' => $clip,
            'comments' => $comments,
            'isLiked' => ClipLike::query()->where('clip_id', $clip->id)->where('user_id', $userId)->exists(),
            'isFavorited' => ClipFavorite::query()->where('clip_id', $clip->id)->where('user_id', $userId)->exists(),
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
        ]);

        $clip = Clip::query()->published()->whereKey($clipId)->firstOrFail();

        try {
            $addClipCommentAction->execute(auth()->user(), $clip, $validated['body']);
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
