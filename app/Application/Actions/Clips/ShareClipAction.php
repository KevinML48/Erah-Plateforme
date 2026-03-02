<?php

namespace App\Application\Actions\Clips;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\Clip;
use App\Models\ClipShare;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ShareClipAction
{
    public function __construct(
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    public function execute(User $user, Clip $clip, string $channel = 'link'): ClipShare
    {
        return DB::transaction(function () use ($user, $clip, $channel) {
            $lockedClip = Clip::query()->whereKey($clip->id)->lockForUpdate()->firstOrFail();
            if (! $lockedClip->is_published || $lockedClip->published_at === null) {
                throw new RuntimeException('Clip is not published.');
            }

            $publicUrl = url('/api/clips/'.$lockedClip->slug);

            $share = ClipShare::query()->create([
                'clip_id' => $lockedClip->id,
                'user_id' => $user->id,
                'channel' => $channel,
                'shared_url' => $publicUrl,
            ]);

            $this->storeAuditLogAction->execute(
                action: 'clips.shared',
                actor: $user,
                target: $share,
                context: [
                    'clip_id' => $lockedClip->id,
                    'channel' => $channel,
                ],
            );

            return $share->fresh();
        });
    }
}
