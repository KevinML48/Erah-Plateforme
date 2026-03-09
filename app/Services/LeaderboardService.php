<?php

namespace App\Services;

use App\Models\UserProgress;
use Illuminate\Support\Collection;

class LeaderboardService
{
    public function __construct(
        private readonly RankService $rankService
    ) {
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function xp(int $limit = 50): Collection
    {
        return UserProgress::query()
            ->with(['user:id,name,avatar_path'])
            ->orderByDesc('total_xp')
            ->orderByDesc('total_rank_points')
            ->limit(max(1, min($limit, 100)))
            ->get()
            ->values()
            ->map(function (UserProgress $progress, int $index): array {
                $league = $this->rankService->resolveLeague((int) $progress->total_xp);

                return [
                    'position' => $index + 1,
                    'user_id' => $progress->user_id,
                    'name' => $progress->user?->name,
                    'avatar_url' => $progress->user?->avatar_url,
                    'total_xp' => (int) $progress->total_xp,
                    'total_rank_points' => (int) $progress->total_rank_points,
                    'community_rank' => $league['name'],
                    'is_supporter' => $progress->user?->isSupporterActive() ?? false,
                ];
            });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function byRank(int $limit = 50): Collection
    {
        return UserProgress::query()
            ->with(['user:id,name,avatar_path', 'league:id,name'])
            ->orderByDesc('total_rank_points')
            ->orderByDesc('total_xp')
            ->limit(max(1, min($limit, 100)))
            ->get()
            ->values()
            ->map(function (UserProgress $progress, int $index): array {
                return [
                    'position' => $index + 1,
                    'user_id' => $progress->user_id,
                    'name' => $progress->user?->name,
                    'avatar_url' => $progress->user?->avatar_url,
                    'total_rank_points' => (int) $progress->total_rank_points,
                    'total_xp' => (int) $progress->total_xp,
                    'league' => $progress->league?->name,
                    'is_supporter' => $progress->user?->isSupporterActive() ?? false,
                ];
            });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function duel(int $limit = 50): Collection
    {
        return UserProgress::query()
            ->with(['user:id,name,avatar_path'])
            ->orderByDesc('duel_score')
            ->orderByDesc('duel_wins')
            ->limit(max(1, min($limit, 100)))
            ->get()
            ->values()
            ->map(function (UserProgress $progress, int $index): array {
                return [
                    'position' => $index + 1,
                    'user_id' => $progress->user_id,
                    'name' => $progress->user?->name,
                    'avatar_url' => $progress->user?->avatar_url,
                    'duel_score' => (int) $progress->duel_score,
                    'duel_wins' => (int) $progress->duel_wins,
                    'duel_losses' => (int) $progress->duel_losses,
                    'is_supporter' => $progress->user?->isSupporterActive() ?? false,
                ];
            });
    }
}
