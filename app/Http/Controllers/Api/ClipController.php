<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Clip;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClipController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sort' => ['nullable', 'string', Rule::in(['recent', 'popular'])],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $sort = $validated['sort'] ?? 'recent';
        $limit = (int) ($validated['limit'] ?? 20);

        $query = Clip::query()
            ->published()
            ->select([
                'id',
                'title',
                'slug',
                'description',
                'video_url',
                'thumbnail_url',
                'published_at',
                'likes_count',
                'favorites_count',
                'comments_count',
            ]);

        if ($sort === 'popular') {
            $query
                ->orderByDesc('likes_count')
                ->orderByDesc('favorites_count')
                ->orderByDesc('comments_count')
                ->orderByDesc('published_at')
                ->orderByDesc('id');
        } else {
            $query->orderByDesc('published_at')->orderByDesc('id');
        }

        return response()->json([
            'sort' => $sort,
            'data' => $query->limit($limit)->get(),
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        try {
            $clip = Clip::query()
                ->published()
                ->where('slug', $slug)
                ->with([
                    'comments' => fn ($query) => $query
                        ->orderByDesc('id')
                        ->limit(50)
                        ->with('user:id,name'),
                    'createdBy:id,name',
                ])
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            return response()->json([
                'message' => 'Clip not found.',
            ], 404);
        }

        return response()->json([
            'data' => [
                'id' => $clip->id,
                'title' => $clip->title,
                'slug' => $clip->slug,
                'description' => $clip->description,
                'video_url' => $clip->video_url,
                'thumbnail_url' => $clip->thumbnail_url,
                'published_at' => $clip->published_at,
                'likes_count' => $clip->likes_count,
                'favorites_count' => $clip->favorites_count,
                'comments_count' => $clip->comments_count,
                'created_by' => [
                    'id' => $clip->createdBy?->id,
                    'name' => $clip->createdBy?->name,
                ],
                'comments' => $clip->comments->map(function ($comment) {
                    return [
                        'id' => $comment->id,
                        'body' => $comment->body,
                        'user' => [
                            'id' => $comment->user?->id,
                            'name' => $comment->user?->name,
                        ],
                        'created_at' => $comment->created_at,
                        'updated_at' => $comment->updated_at,
                    ];
                })->values(),
            ],
        ]);
    }
}
