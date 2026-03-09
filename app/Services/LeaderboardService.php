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
            ->with(['user:id,name,avatar_path', 'user.rewardWallet:user_id,balance'])
            ->orderByDesc('total_xp')
            ->orderByDesc('total_rank_points')
            ->orderByDesc('duel_score')
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
                    'points_balance' => (int) ($progress->user?->rewardWallet?->balance ?? 0),
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
                    'total_rank_points' => (int) $progress->total_rank_points,
                    'league_points' => (int) $progress->total_xp,
                    'total_xp' => (int) $progress->total_xp,
                    'league' => $league['name'],
                    'league_threshold' => (int) $league['xp_threshold'],
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
            ->orderByDesc('duel_wins')
            ->orderByDesc('duel_current_streak')
            ->orderByRaw('(CASE WHEN duel_losses = 0 THEN duel_wins ELSE CAST(duel_wins AS REAL) / duel_losses END) DESC')
            ->orderByDesc('duel_score')
            ->limit(max(1, min($limit, 100)))
            ->get()
            ->values()
            ->map(function (UserProgress $progress, int $index): array {
                $wins = (int) $progress->duel_wins;
                $losses = (int) $progress->duel_losses;

                return [
                    'position' => $index + 1,
                    'user_id' => $progress->user_id,
                    'name' => $progress->user?->name,
                    'avatar_url' => $progress->user?->avatar_url,
                    'duel_score' => (int) $progress->duel_score,
                    'duel_wins' => (int) $progress->duel_wins,
                    'duel_losses' => (int) $progress->duel_losses,
                    'duel_current_streak' => (int) $progress->duel_current_streak,
                    'duel_best_streak' => (int) $progress->duel_best_streak,
                    'duel_ratio' => $losses === 0 ? (float) $wins : round($wins / max(1, $losses), 2),
                    'is_supporter' => $progress->user?->isSupporterActive() ?? false,
                ];
            });
    }
}
