<?php

namespace App\Application\Actions\Clips;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\Clip;
use App\Models\ClipLike;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ToggleClipLikeAction
{
    public function __construct(
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    /**
     * @return array{clip: Clip, idempotent: bool, liked: bool}
     */
    public function like(User $user, Clip $clip): array
    {
        return DB::transaction(function () use ($user, $clip) {
            $lockedClip = Clip::query()->whereKey($clip->id)->lockForUpdate()->firstOrFail();
            $this->assertClipPublished($lockedClip);

            $existing = ClipLike::query()
                ->where('clip_id', $lockedClip->id)
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                return [
                    'clip' => $lockedClip->fresh(),
                    'idempotent' => true,
                    'liked' => true,
                ];
            }

            ClipLike::query()->create([
                'clip_id' => $lockedClip->id,
                'user_id' => $user->id,
            ]);

            $lockedClip->increment('likes_count');

            $this->storeAuditLogAction->execute(
                action: 'clips.like.added',
                actor: $user,
                target: $lockedClip,
                context: [
                    'clip_id' => $lockedClip->id,
                    'user_id' => $user->id,
                ],
            );

            return [
                'clip' => $lockedClip->fresh(),
                'idempotent' => false,
                'liked' => true,
            ];
        });
    }

    /**
     * @return array{clip: Clip, idempotent: bool, liked: bool}
     */
    public function unlike(User $user, Clip $clip): array
    {
        return DB::transaction(function () use ($user, $clip) {
            $lockedClip = Clip::query()->whereKey($clip->id)->lockForUpdate()->firstOrFail();
            $this->assertClipPublished($lockedClip);

            $existing = ClipLike::query()
                ->where('clip_id', $lockedClip->id)
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (! $existing) {
                return [
                    'clip' => $lockedClip->fresh(),
                    'idempotent' => true,
                    'liked' => false,
                ];
            }

            $existing->delete();

            Clip::query()
                ->whereKey($lockedClip->id)
                ->update([
                    'likes_count' => DB::raw('CASE WHEN likes_count > 0 THEN likes_count - 1 ELSE 0 END'),
                    'updated_at' => now(),
                ]);

            $this->storeAuditLogAction->execute(
                action: 'clips.like.removed',
                actor: $user,
                target: $lockedClip,
                context: [
                    'clip_id' => $lockedClip->id,
                    'user_id' => $user->id,
                ],
            );

            return [
                'clip' => $lockedClip->fresh(),
                'idempotent' => false,
                'liked' => false,
            ];
        });
    }

    private function assertClipPublished(Clip $clip): void
    {
        if (! $clip->is_published || $clip->published_at === null) {
            throw new RuntimeException('Clip is not published.');
        }
    }
}
