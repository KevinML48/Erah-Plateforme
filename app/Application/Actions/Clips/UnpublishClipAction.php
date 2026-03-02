<?php

namespace App\Application\Actions\Clips;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\Clip;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UnpublishClipAction
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

            if (! $lockedClip->is_published) {
                return [
                    'clip' => $lockedClip->fresh(),
                    'idempotent' => true,
                ];
            }

            $lockedClip->is_published = false;
            $lockedClip->published_at = null;
            $lockedClip->updated_by = $actor->id;
            $lockedClip->save();

            $this->storeAuditLogAction->execute(
                action: 'clips.unpublished',
                actor: $actor,
                target: $lockedClip,
                context: [
                    'clip_id' => $lockedClip->id,
                    'slug' => $lockedClip->slug,
                ],
            );

            return [
                'clip' => $lockedClip->fresh(),
                'idempotent' => false,
            ];
        });
    }
}
