<?php

namespace App\Application\Actions\Ranking;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\League;
use App\Models\User;
use App\Models\UserProgress;
use Illuminate\Support\Facades\DB;

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
                return $this->syncLeagueFromXp($progress)->load('league');
            }

            $defaultLeague = $this->resolveLeagueFromXp(0);

            $progress = UserProgress::query()->create([
                'user_id' => $user->id,
                'current_league_id' => $defaultLeague?->id,
                'total_xp' => 0,
                'total_rank_points' => 0,
                'last_points_at' => null,
            ]);

            $this->storeAuditLogAction->execute(
                action: 'ranking.progress.initialized',
                actor: $user,
                target: $progress,
                context: [
                    'league_key' => $defaultLeague?->key,
                ],
            );

            return $progress->load('league');
        });
    }

    private function syncLeagueFromXp(UserProgress $progress): UserProgress
    {
        $targetLeague = $this->resolveLeagueFromXp((int) $progress->total_xp);
        if (! $targetLeague) {
            return $progress;
        }

        if ((int) $progress->current_league_id !== (int) $targetLeague->id) {
            $progress->current_league_id = $targetLeague->id;
            $progress->save();
        }

        return $progress;
    }

    private function resolveLeagueFromXp(int $xp): ?League
    {
        $definition = collect((array) config('community.xp_leagues', []))
            ->sortBy('xp_threshold')
            ->filter(fn (array $definition): bool => $xp >= (int) ($definition['xp_threshold'] ?? 0))
            ->last();
        $leagueKey = is_array($definition) ? (string) ($definition['key'] ?? '') : '';

        if ($leagueKey !== '') {
            $league = League::query()
                ->active()
                ->where('key', $leagueKey)
                ->first();

            if ($league) {
                return $league;
            }
        }

        return League::query()
            ->active()
            ->orderBy('sort_order')
            ->first();
    }
}
