<?php

namespace App\Application\Actions\Clips;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\Clip;
use App\Models\ClipFavorite;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ToggleClipFavoriteAction
{
    public function __construct(
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    /**
     * @return array{clip: Clip, idempotent: bool, favorited: bool}
     */
    public function favorite(User $user, Clip $clip): array
    {
        return DB::transaction(function () use ($user, $clip) {
            $lockedClip = Clip::query()->whereKey($clip->id)->lockForUpdate()->firstOrFail();
            $this->assertClipPublished($lockedClip);

            $existing = ClipFavorite::query()
                ->where('clip_id', $lockedClip->id)
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                return [
                    'clip' => $lockedClip->fresh(),
                    'idempotent' => true,
                    'favorited' => true,
                ];
            }

            ClipFavorite::query()->create([
                'clip_id' => $lockedClip->id,
                'user_id' => $user->id,
            ]);

            $lockedClip->increment('favorites_count');

            $this->storeAuditLogAction->execute(
                action: 'clips.favorite.added',
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
                'favorited' => true,
            ];
        });
    }

    /**
     * @return array{clip: Clip, idempotent: bool, favorited: bool}
     */
    public function unfavorite(User $user, Clip $clip): array
    {
        return DB::transaction(function () use ($user, $clip) {
            $lockedClip = Clip::query()->whereKey($clip->id)->lockForUpdate()->firstOrFail();
            $this->assertClipPublished($lockedClip);

            $existing = ClipFavorite::query()
                ->where('clip_id', $lockedClip->id)
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (! $existing) {
                return [
                    'clip' => $lockedClip->fresh(),
                    'idempotent' => true,
                    'favorited' => false,
                ];
            }

            $existing->delete();

            Clip::query()
                ->whereKey($lockedClip->id)
                ->update([
                    'favorites_count' => DB::raw('CASE WHEN favorites_count > 0 THEN favorites_count - 1 ELSE 0 END'),
                    'updated_at' => now(),
                ]);

            $this->storeAuditLogAction->execute(
                action: 'clips.favorite.removed',
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
                'favorited' => false,
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
