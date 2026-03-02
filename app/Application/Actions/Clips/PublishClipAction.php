<?php

namespace App\Application\Actions\Clips;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\Clip;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PublishClipAction
{
    public function __construct(
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    /**
     * @return array{clip: Clip, idempotent: bool}
     */
    public function execute(User $actor, Clip $clip): array
    {
        return DB::transaction(function () use ($actor, $clip) {
            $lockedClip = Clip::query()
                ->whereKey($clip->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedClip->is_published && $lockedClip->published_at !== null) {
                return [
                    'clip' => $lockedClip->fresh(),
                    'idempotent' => true,
                ];
            }

            $lockedClip->is_published = true;
            $lockedClip->published_at = $lockedClip->published_at ?: now();
            $lockedClip->updated_by = $actor->id;
            $lockedClip->save();

            $this->storeAuditLogAction->execute(
                action: 'clips.published',
                actor: $actor,
                target: $lockedClip,
                context: [
                    'clip_id' => $lockedClip->id,
                    'slug' => $lockedClip->slug,
                    'published_at' => $lockedClip->published_at,
                ],
            );

            return [
                'clip' => $lockedClip->fresh(),
                'idempotent' => false,
            ];
        });
    }
}
