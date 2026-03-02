<?php

namespace App\Application\Actions\Clips;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\Clip;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateClipAction
{
    public function __construct(
        private readonly BuildUniqueClipSlugAction $buildUniqueClipSlugAction,
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function execute(User $actor, Clip $clip, array $payload): Clip
    {
        return DB::transaction(function () use ($actor, $clip, $payload) {
            $lockedClip = Clip::query()
                ->whereKey($clip->id)
                ->lockForUpdate()
                ->firstOrFail();

            $attributes = [];

            foreach (['title', 'description', 'video_url', 'thumbnail_url'] as $field) {
                if (array_key_exists($field, $payload)) {
                    $attributes[$field] = $payload[$field];
                }
            }

            if (array_key_exists('slug', $payload)) {
                $slugSource = (string) ($payload['slug'] ?: ($payload['title'] ?? $lockedClip->title));
                $attributes['slug'] = $this->buildUniqueClipSlugAction->execute($slugSource, $lockedClip->id);
            }

            $attributes['updated_by'] = $actor->id;
            $lockedClip->fill($attributes);
            $lockedClip->save();

            $this->storeAuditLogAction->execute(
                action: 'clips.updated',
                actor: $actor,
                target: $lockedClip,
                context: [
                    'clip_id' => $lockedClip->id,
                    'slug' => $lockedClip->slug,
                    'fields' => array_keys($attributes),
                ],
            );

            return $lockedClip->fresh();
        });
    }
}
