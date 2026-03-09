<?php

namespace App\Services;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Application\Actions\Ranking\AddPointsAction;
use App\Models\CommunityRewardGrant;
use App\Models\PointsTransaction;
use App\Models\User;
use App\Models\UserProgress;
use Illuminate\Support\Facades\DB;

class RewardGrantService
{
    public function __construct(
        private readonly AddPointsAction $addPointsAction,
        private readonly WalletService $walletService,
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    /**
     * @param array<string, int> $rewards
     * @param array<string, mixed> $meta
     */
    public function grant(
        User $user,
        string $domain,
        string $action,
        string $dedupeKey,
        array $rewards,
        ?User $actor = null,
        ?string $subjectType = null,
        ?string $subjectId = null,
        array $meta = [],
        bool $allowPartialRewardDebit = false,
        bool $allowPartialBetDebit = false
    ): CommunityRewardGrant {
        $normalizedRewards = $this->normalizeRewards($rewards);

        return DB::transaction(function () use (
            $user,
            $domain,
            $action,
            $dedupeKey,
            $normalizedRewards,
            $actor,
            $subjectType,
            $subjectId,
            $meta,
            $allowPartialRewardDebit,
            $allowPartialBetDebit
        ) {
            $existing = CommunityRewardGrant::query()
                ->where('dedupe_key', $dedupeKey)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                return $existing;
            }

            $xp = (int) ($normalizedRewards['xp'] ?? 0);
            $rankPoints = (int) ($normalizedRewards['rank_points'] ?? 0);
            $rewardPoints = (int) ($normalizedRewards['points'] ?? 0);
            $betPoints = (int) ($normalizedRewards['bet_points'] ?? 0);
            $duelScore = (int) ($normalizedRewards['duel_score'] ?? 0);

            if ($xp > 0) {
                $this->addPointsAction->execute(
                    user: $user,
                    kind: PointsTransaction::KIND_XP,
                    points: $xp,
                    sourceType: 'community.'.$domain.'.'.$action,
                    sourceId: $dedupeKey,
                    actor: $actor,
                    meta: $meta,
                );
            }

            if ($rankPoints > 0) {
                $this->addPointsAction->execute(
                    user: $user,
                    kind: PointsTransaction::KIND_RANK,
                    points: $rankPoints,
                    sourceType: 'community.'.$domain.'.'.$action.'.rank',
                    sourceId: $dedupeKey,
                    actor: $actor,
                    meta: $meta,
                );
            }

            if ($rewardPoints !== 0) {
                $walletResult = $this->walletService->adjustPoints(
                    user: $user,
                    amount: $rewardPoints,
                    uniqueKey: 'community.reward.'.$dedupeKey,
                    meta: $meta + ['domain' => $domain, 'action' => $action],
                    allowPartialDebit: $allowPartialRewardDebit,
                );
                $rewardPoints = (int) $walletResult['actual_amount'];
            }

            if ($betPoints !== 0) {
                $walletResult = $this->walletService->adjustBetPoints(
                    user: $user,
                    amount: $betPoints,
                    uniqueKey: 'community.bet.'.$dedupeKey,
                    meta: $meta + ['domain' => $domain, 'action' => $action],
                    allowPartialDebit: $allowPartialBetDebit,
                );
                $betPoints = (int) $walletResult['actual_amount'];
            }

            if ($duelScore !== 0) {
                $progress = UserProgress::query()
                    ->where('user_id', $user->id)
                    ->lockForUpdate()
                    ->first();

                if ($progress) {
                    $progress->duel_score += $duelScore;
                    if ($domain === 'duels' && $action === 'win') {
                        $progress->duel_wins += 1;
                        $progress->duel_current_streak += 1;
                        $progress->duel_best_streak = max(
                            (int) $progress->duel_best_streak,
                            (int) $progress->duel_current_streak
                        );
                    }
                    if ($domain === 'duels' && $action === 'loss') {
                        $progress->duel_losses += 1;
                        $progress->duel_current_streak = 0;
                    }
                    $progress->save();
                }
            }

            $grant = CommunityRewardGrant::query()->create([
                'user_id' => $user->id,
                'domain' => $domain,
                'action' => $action,
                'dedupe_key' => $dedupeKey,
                'subject_type' => $subjectType,
                'subject_id' => $subjectId,
                'xp_amount' => $xp,
                'rank_points_amount' => $rankPoints,
                'reward_points_amount' => $rewardPoints,
                'bet_points_amount' => $betPoints,
                'duel_score_amount' => $duelScore,
                'meta' => $meta,
                'granted_on' => now()->toDateString(),
                'granted_at' => now(),
            ]);

            $this->storeAuditLogAction->execute(
                action: 'community.reward.granted',
                actor: $actor ?? $user,
                target: $grant,
                context: [
                    'domain' => $domain,
                    'action' => $action,
                    'dedupe_key' => $dedupeKey,
                    'subject_type' => $subjectType,
                    'subject_id' => $subjectId,
                    'rewards' => [
                        'xp' => $xp,
                        'rank_points' => $rankPoints,
                        'reward_points' => $rewardPoints,
                        'bet_points' => $betPoints,
                        'duel_score' => $duelScore,
                    ],
                ],
            );

            return $grant;
        });
    }

    public function wasGranted(string $dedupeKey): bool
    {
        return CommunityRewardGrant::query()->where('dedupe_key', $dedupeKey)->exists();
    }

    public function countForDay(User $user, string $domain, string $action): int
    {
        return CommunityRewardGrant::query()
            ->where('user_id', $user->id)
            ->where('domain', $domain)
            ->where('action', $action)
            ->whereDate('granted_on', now()->toDateString())
            ->count();
    }

    /**
     * @param array<string, int> $rewards
     * @return array{xp: int, rank_points: int, points: int, bet_points: int, duel_score: int}
     */
    private function normalizeRewards(array $rewards): array
    {
        return [
            'xp' => (int) ($rewards['xp'] ?? 0),
            'rank_points' => (int) ($rewards['rank_points'] ?? 0),
            'points' => (int) ($rewards['points'] ?? $rewards['reward_points'] ?? 0),
            'bet_points' => (int) ($rewards['bet_points'] ?? 0),
            'duel_score' => (int) ($rewards['duel_score'] ?? 0),
        ];
    }
}
