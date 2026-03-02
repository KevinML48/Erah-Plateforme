<?php

namespace App\Application\Actions\Clips;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\Clip;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DeleteClipAction
{
    public function __construct(
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    public function execute(User $actor, Clip $clip): void
    {
        DB::transaction(function () use ($actor, $clip) {
            $lockedClip = Clip::query()
                ->whereKey($clip->id)
                ->lockForUpdate()
                ->firstOrFail();

            $context = [
                'clip_id' => $lockedClip->id,
                'slug' => $lockedClip->slug,
                'likes_count' => $lockedClip->likes_count,
                'favorites_count' => $lockedClip->favorites_count,
                'comments_count' => $lockedClip->comments_count,
            ];

            $this->storeAuditLogAction->execute(
                action: 'clips.deleted',
                actor: $actor,
                target: $lockedClip,
                context: $context,
            );

            $lockedClip->delete();
        });
    }
}
