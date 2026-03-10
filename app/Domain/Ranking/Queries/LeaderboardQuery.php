<?php

namespace App\Domain\Ranking\Queries;

use App\Models\League;
use App\Models\UserProgress;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class LeaderboardQuery
{
    /**
     * @return array{league: array<string, mixed>, entries: Collection<int, array<string, mixed>>}
     */
    public function execute(string $leagueKey, int $limit = 50): array
    {
        $safeLimit = max(1, min($limit, 100));

        return Cache::remember(
            'leaderboards.league.'.$leagueKey.'.'.$safeLimit,
            now()->addSeconds(90),
            function () use ($leagueKey, $safeLimit): array {
                $league = League::query()
                    ->active()
                    ->where('key', $leagueKey)
                    ->first();

                if (! $league) {
                    throw (new ModelNotFoundException())->setModel(League::class, [$leagueKey]);
                }

                $progressRows = UserProgress::query()
                    ->where('current_league_id', $league->id)
                    ->with([
                        'user:id,name,avatar_path',
                        'user.supportSubscriptions' => fn ($query) => $query->active(),
                    ])
                    ->orderByDesc('total_rank_points')
                    ->orderByDesc('total_xp')
                    ->orderBy('user_id')
                    ->limit($safeLimit)
                    ->get();

                return [
                    'league' => [
                        'id' => $league->id,
                        'key' => $league->key,
                        'name' => $league->name,
                        'min_rank_points' => $league->min_rank_points,
                    ],
                    'entries' => $progressRows->values()->map(function (UserProgress $progress, int $index) {
                        return [
                            'position' => $index + 1,
                            'user_id' => $progress->user_id,
                            'name' => $progress->user?->name,
                            'avatar_url' => $progress->user?->avatar_url,
                            'total_rank_points' => $progress->total_rank_points,
                            'total_xp' => $progress->total_xp,
                            'is_supporter' => $progress->user?->isSupporterActive() ?? false,
                        ];
                    })->all(),
                ];
            }
        );
    }
}
