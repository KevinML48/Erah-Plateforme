<?php

namespace App\Http\Controllers\Api;

use App\Application\Actions\Clips\AddClipCommentAction;
use App\Application\Actions\Clips\DeleteClipCommentAction;
use App\Application\Actions\Clips\ShareClipAction;
use App\Application\Actions\Clips\ToggleClipFavoriteAction;
use App\Application\Actions\Clips\ToggleClipLikeAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreClipCommentRequest;
use App\Http\Requests\Api\StoreClipShareRequest;
use App\Models\Clip;
use App\Models\ClipComment;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use RuntimeException;

class ClipInteractionController extends Controller
{
    public function like(
        Request $request,
        int $id,
        ToggleClipLikeAction $toggleClipLikeAction
    ): JsonResponse {
        try {
            $clip = $this->resolvePublishedClip($id);
            $result = $toggleClipLikeAction->like($request->user(), $clip);
        } catch (ModelNotFoundException) {
            return response()->json(['message' => 'Clip not found.'], 404);
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json([
            'idempotent' => $result['idempotent'],
            'liked' => $result['liked'],
            'counts' => $this->countsPayload($result['clip']),
        ]);
    }

    public function unlike(
        Request $request,
        int $id,
        ToggleClipLikeAction $toggleClipLikeAction
    ): JsonResponse {
        try {
            $clip = $this->resolvePublishedClip($id);
            $result = $toggleClipLikeAction->unlike($request->user(), $clip);
        } catch (ModelNotFoundException) {
            return response()->json(['message' => 'Clip not found.'], 404);
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json([
            'idempotent' => $result['idempotent'],
            'liked' => $result['liked'],
            'counts' => $this->countsPayload($result['clip']),
        ]);
    }

    public function favorite(
        Request $request,
        int $id,
        ToggleClipFavoriteAction $toggleClipFavoriteAction
    ): JsonResponse {
        try {
            $clip = $this->resolvePublishedClip($id);
            $result = $toggleClipFavoriteAction->favorite($request->user(), $clip);
        } catch (ModelNotFoundException) {
            return response()->json(['message' => 'Clip not found.'], 404);
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json([
            'idempotent' => $result['idempotent'],
            'favorited' => $result['favorited'],
            'counts' => $this->countsPayload($result['clip']),
        ]);
    }

    public function unfavorite(
        Request $request,
        int $id,
        ToggleClipFavoriteAction $toggleClipFavoriteAction
    ): JsonResponse {
        try {
            $clip = $this->resolvePublishedClip($id);
            $result = $toggleClipFavoriteAction->unfavorite($request->user(), $clip);
        } catch (ModelNotFoundException) {
            return response()->json(['message' => 'Clip not found.'], 404);
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json([
            'idempotent' => $result['idempotent'],
            'favorited' => $result['favorited'],
            'counts' => $this->countsPayload($result['clip']),
        ]);
    }

    public function comment(
        StoreClipCommentRequest $request,
        int $id,
        AddClipCommentAction $addClipCommentAction
    ): JsonResponse {
        try {
            $clip = $this->resolvePublishedClip($id);
            $comment = $addClipCommentAction->execute(
                $request->user(),
                $clip,
                $request->validated('body'),
                $request->validated('parent_id'),
            );
        } catch (ModelNotFoundException) {
            return response()->json(['message' => 'Clip not found.'], 404);
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json([
            'data' => [
                'id' => $comment->id,
                'clip_id' => $comment->clip_id,
                'parent_id' => $comment->parent_id,
                'body' => $comment->body,
                'user' => [
                    'id' => $comment->user?->id,
                    'name' => $comment->user?->name,
                ],
                'created_at' => $comment->created_at,
            ],
        ], 201);
    }

    public function deleteComment(
        Request $request,
        int $clipId,
        int $commentId,
        DeleteClipCommentAction $deleteClipCommentAction
    ): JsonResponse {
        try {
            $clip = $this->resolvePublishedClip($clipId);
            $comment = ClipComment::query()
                ->where('clip_id', $clip->id)
                ->whereKey($commentId)
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            return response()->json(['message' => 'Comment not found.'], 404);
        }

        Gate::authorize('delete', $comment);

        $updatedClip = $deleteClipCommentAction->execute($request->user(), $comment);

        return response()->json([
            'message' => 'Comment deleted.',
            'counts' => $this->countsPayload($updatedClip),
        ]);
    }

    public function share(
        StoreClipShareRequest $request,
        int $id,
        ShareClipAction $shareClipAction
    ): JsonResponse {
        try {
            $clip = $this->resolvePublishedClip($id);
            $share = $shareClipAction->execute(
                user: $request->user(),
                clip: $clip,
                channel: $request->validated('channel', 'link'),
            );
        } catch (ModelNotFoundException) {
            return response()->json(['message' => 'Clip not found.'], 404);
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json([
            'data' => [
                'share_id' => $share->id,
                'clip_id' => $share->clip_id,
                'channel' => $share->channel,
                'public_url' => $share->shared_url,
                'created_at' => $share->created_at,
            ],
        ], 201);
    }

    private function resolvePublishedClip(int $id): Clip
    {
        return Clip::query()
            ->published()
            ->whereKey($id)
            ->firstOrFail();
    }

    private function countsPayload(Clip $clip): array
    {
        return [
            'likes_count' => $clip->likes_count,
            'favorites_count' => $clip->favorites_count,
            'comments_count' => $clip->comments_count,
        ];
    }
}
