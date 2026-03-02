<?php

namespace App\Http\Controllers\Api\Admin;

use App\Application\Actions\Clips\CreateClipAction;
use App\Application\Actions\Clips\DeleteClipAction;
use App\Application\Actions\Clips\PublishClipAction;
use App\Application\Actions\Clips\UpdateClipAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\StoreClipRequest;
use App\Http\Requests\Api\Admin\UpdateClipRequest;
use App\Models\Clip;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ClipAdminController extends Controller
{
    public function store(StoreClipRequest $request, CreateClipAction $createClipAction): JsonResponse
    {
        Gate::authorize('create', Clip::class);

        $clip = $createClipAction->execute(
            actor: $request->user(),
            payload: $request->validated(),
        );

        return response()->json([
            'data' => $this->mapClip($clip),
        ], 201);
    }

    public function update(
        UpdateClipRequest $request,
        int $id,
        UpdateClipAction $updateClipAction
    ): JsonResponse {
        try {
            $clip = Clip::query()->findOrFail($id);
        } catch (ModelNotFoundException) {
            return response()->json([
                'message' => 'Clip not found.',
            ], 404);
        }

        Gate::authorize('update', $clip);

        $clip = $updateClipAction->execute(
            actor: $request->user(),
            clip: $clip,
            payload: $request->validated(),
        );

        return response()->json([
            'data' => $this->mapClip($clip),
        ]);
    }

    public function destroy(Request $request, int $id, DeleteClipAction $deleteClipAction): JsonResponse
    {
        try {
            $clip = Clip::query()->findOrFail($id);
        } catch (ModelNotFoundException) {
            return response()->json([
                'message' => 'Clip not found.',
            ], 404);
        }

        Gate::authorize('delete', $clip);

        $deleteClipAction->execute(
            actor: $request->user(),
            clip: $clip,
        );

        return response()->json([
            'message' => 'Clip deleted.',
        ]);
    }

    public function publish(Request $request, int $id, PublishClipAction $publishClipAction): JsonResponse
    {
        try {
            $clip = Clip::query()->findOrFail($id);
        } catch (ModelNotFoundException) {
            return response()->json([
                'message' => 'Clip not found.',
            ], 404);
        }

        Gate::authorize('publish', $clip);

        $result = $publishClipAction->execute(
            actor: $request->user(),
            clip: $clip,
        );

        return response()->json([
            'idempotent' => $result['idempotent'],
            'data' => $this->mapClip($result['clip']),
        ]);
    }

    private function mapClip(Clip $clip): array
    {
        return [
            'id' => $clip->id,
            'title' => $clip->title,
            'slug' => $clip->slug,
            'description' => $clip->description,
            'video_url' => $clip->video_url,
            'thumbnail_url' => $clip->thumbnail_url,
            'is_published' => $clip->is_published,
            'published_at' => $clip->published_at,
            'likes_count' => $clip->likes_count,
            'favorites_count' => $clip->favorites_count,
            'comments_count' => $clip->comments_count,
            'created_by' => $clip->created_by,
            'updated_by' => $clip->updated_by,
            'created_at' => $clip->created_at,
            'updated_at' => $clip->updated_at,
        ];
    }
}
