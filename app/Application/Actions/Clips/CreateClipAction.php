<?php

namespace App\Application\Actions\Clips;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\Clip;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateClipAction
{
    public function __construct(
        private readonly BuildUniqueClipSlugAction $buildUniqueClipSlugAction,
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function execute(User $actor, array $payload): Clip
    {
        return DB::transaction(function () use ($actor, $payload) {
            $slugSource = (string) (($payload['slug'] ?? null) ?: $payload['title']);
            $slug = $this->buildUniqueClipSlugAction->execute($slugSource);

            $clip = Clip::query()->create([
                'title' => $payload['title'],
                'slug' => $slug,
                'description' => $payload['description'] ?? null,
                'video_url' => $payload['video_url'],
                'thumbnail_url' => $payload['thumbnail_url'] ?? null,
                'is_published' => false,
                'published_at' => null,
                'likes_count' => 0,
                'favorites_count' => 0,
                'comments_count' => 0,
                'created_by' => $actor->id,
                'updated_by' => null,
            ]);

            $this->storeAuditLogAction->execute(
                action: 'clips.created',
                actor: $actor,
                target: $clip,
                context: [
                    'clip_id' => $clip->id,
                    'slug' => $clip->slug,
                    'title' => $clip->title,
                ],
            );

            return $clip->fresh();
        });
    }
}
