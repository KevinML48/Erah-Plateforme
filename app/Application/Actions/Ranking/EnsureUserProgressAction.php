<?php

namespace App\Application\Actions\Ranking;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\League;
use App\Models\User;
use App\Models\UserProgress;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class EnsureUserProgressAction
{
    public function __construct(
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    public function execute(User $user): UserProgress
    {
        return DB::transaction(function () use ($user) {
            $progress = UserProgress::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if ($progress) {
                return $progress->load('league');
            }

            $defaultLeague = League::query()
                ->active()
                ->orderBy('sort_order')
                ->first();

            if (! $defaultLeague) {
                throw new RuntimeException('No active league configured.');
            }

            $progress = UserProgress::query()->create([
                'user_id' => $user->id,
                'current_league_id' => $defaultLeague->id,
                'total_xp' => 0,
                'total_rank_points' => 0,
                'last_points_at' => null,
            ]);

            $this->storeAuditLogAction->execute(
                action: 'ranking.progress.initialized',
                actor: $user,
                target: $progress,
                context: [
                    'league_key' => $defaultLeague->key,
                ],
            );

            return $progress->load('league');
        });
    }
}
