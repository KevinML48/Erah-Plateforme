<?php

namespace App\Services;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Application\Actions\Notifications\NotifyAction;
use App\Domain\Notifications\Enums\NotificationCategory;
use App\Models\Duel;
use App\Models\DuelResult;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DuelService
{
    public function __construct(
        private readonly RewardGrantService $rewardGrantService,
        private readonly EventService $eventService,
        private readonly MissionEngine $missionEngine,
        private readonly AchievementService $achievementService,
        private readonly NotifyAction $notifyAction,
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    public function recordResult(
        User $actor,
        Duel $duel,
        User $winner,
        ?int $challengerScore = null,
        ?int $challengedScore = null,
        ?string $note = null
    ): DuelResult {
        return DB::transaction(function () use ($actor, $duel, $winner, $challengerScore, $challengedScore, $note) {
            $duel = Duel::query()->whereKey($duel->id)->with(['challenger', 'challenged', 'result'])->lockForUpdate()->firstOrFail();

            if ($duel->status !== Duel::STATUS_ACCEPTED && $duel->status !== Duel::STATUS_SETTLED) {
                throw new RuntimeException('Seuls les duels actifs peuvent etre regles.');
            }

            if (! in_array($winner->id, [(int) $duel->challenger_id, (int) $duel->challenged_id], true)) {
                throw new RuntimeException('Le gagnant doit etre un participant du duel.');
            }

            $loser = (int) $winner->id === (int) $duel->challenger_id ? $duel->challenged : $duel->challenger;
            if (! $loser) {
                throw new RuntimeException('Adversaire introuvable.');
            }

            $result = DuelResult::query()->updateOrCreate(
                ['duel_id' => $duel->id],
                [
                    'winner_user_id' => $winner->id,
                    'loser_user_id' => $loser->id,
                    'actor_id' => $actor->id,
                    'challenger_score' => $challengerScore,
                    'challenged_score' => $challengedScore,
                    'note' => $note,
                    'meta' => null,
                    'settled_at' => now(),
                ],
            );

            $duel->status = Duel::STATUS_SETTLED;
            $duel->responded_at = $duel->responded_at ?? now();
            $duel->save();

            $winRewards = $this->eventService->applyModifiers(
                [
                    'xp' => (int) config('community.duels.rewards.win.xp', 120),
                    'points' => (int) config('community.duels.rewards.win.points', config('community.duels.rewards.win.reward_points', 150)),
                    'duel_score' => (int) config('community.duels.score.win', 25),
                ],
                'bonus_duel',
            );

            $lossRewards = $this->eventService->applyModifiers(
                [
                    'xp' => (int) config('community.duels.rewards.loss.xp', 30),
                    'points' => (int) config('community.duels.rewards.loss.points', config('community.duels.rewards.loss.reward_points', -150)),
                    'duel_score' => (int) config('community.duels.score.loss', -10),
                ],
                'bonus_duel',
            );

            $dailyLimit = (int) config('community.duels.daily_limit', 10);
            $sameOpponentLimit = (int) config('community.duels.same_opponent_reward_limit', 3);
            $pairRewardBlocked = $this->isPairRewardBlocked($winner, $loser, $sameOpponentLimit);

            $winnerEligible = ! $pairRewardBlocked && $this->canGrantRewards($winner, $dailyLimit);
            $loserEligible = ! $pairRewardBlocked && $this->canGrantRewards($loser, $dailyLimit);

            if ($winnerEligible) {
                $this->rewardGrantService->grant(
                    user: $winner,
                    domain: 'duels',
                    action: 'win',
                    dedupeKey: 'duel.win.'.$duel->id.'.'.$winner->id,
                    rewards: $winRewards,
                    actor: $actor,
                    subjectType: Duel::class,
                    subjectId: (string) $duel->id,
                );
            }

            if ($loserEligible) {
                $this->rewardGrantService->grant(
                    user: $loser,
                    domain: 'duels',
                    action: 'loss',
                    dedupeKey: 'duel.loss.'.$duel->id.'.'.$loser->id,
                    rewards: $lossRewards,
                    actor: $actor,
                    subjectType: Duel::class,
                    subjectId: (string) $duel->id,
                    allowPartialRewardDebit: true,
                );
            }

            if ($winnerEligible) {
                $this->missionEngine->recordEvent($winner, 'duel.win', 1, [
                    'event_key' => 'duel.win.'.$duel->id.'.'.$winner->id,
                    'subject_type' => Duel::class,
                    'subject_id' => (string) $duel->id,
                ]);
                $this->missionEngine->recordEvent($winner, 'duel.play', 1, [
                    'event_key' => 'duel.play.'.$duel->id.'.'.$winner->id,
                    'subject_type' => Duel::class,
                    'subject_id' => (string) $duel->id,
                ]);
                $this->achievementService->sync($winner);
            }

            if ($loserEligible) {
                $this->missionEngine->recordEvent($loser, 'duel.play', 1, [
                    'event_key' => 'duel.play.'.$duel->id.'.'.$loser->id,
                    'subject_type' => Duel::class,
                    'subject_id' => (string) $duel->id,
                ]);
                $this->achievementService->sync($loser);
            }

            $this->notifyAction->execute(
                user: $winner,
                category: NotificationCategory::DUEL->value,
                title: 'Victoire en duel',
                message: $winnerEligible
                    ? 'Vous remportez le duel #'.$duel->id.'.'
                    : 'Vous remportez le duel #'.$duel->id.', mais cette rencontre ne compte plus pour la progression aujourd hui.',
                data: ['duel_id' => $duel->id, 'result' => 'win', 'reward_eligible' => $winnerEligible],
            );

            $this->notifyAction->execute(
                user: $loser,
                category: NotificationCategory::DUEL->value,
                title: 'Defaite en duel',
                message: $loserEligible
                    ? 'Le duel #'.$duel->id.' est termine.'
                    : 'Le duel #'.$duel->id.' est termine, sans impact supplementaire sur votre progression.',
                data: ['duel_id' => $duel->id, 'result' => 'loss', 'reward_eligible' => $loserEligible],
            );

            $this->storeAuditLogAction->execute(
                action: 'duels.result.recorded',
                actor: $actor,
                target: $result,
                context: [
                    'duel_id' => $duel->id,
                        'winner_user_id' => $winner->id,
                        'loser_user_id' => $loser->id,
                        'pair_reward_blocked' => $pairRewardBlocked,
                        'winner_reward_eligible' => $winnerEligible,
                        'loser_reward_eligible' => $loserEligible,
                    ],
                );

            return $result;
        });
    }

    private function isPairRewardBlocked(User $winner, User $loser, int $sameOpponentLimit): bool
    {
        if ($sameOpponentLimit <= 0) {
            return false;
        }

        $pairCount = DuelResult::query()
            ->whereDate('settled_at', now()->toDateString())
            ->where(function (Builder $query) use ($winner, $loser): void {
                $query
                    ->where(function (Builder $inner) use ($winner, $loser): void {
                        $inner->where('winner_user_id', $winner->id)
                            ->where('loser_user_id', $loser->id);
                    })
                    ->orWhere(function (Builder $inner) use ($winner, $loser): void {
                        $inner->where('winner_user_id', $loser->id)
                            ->where('loser_user_id', $winner->id);
                    });
            })
            ->count();

        return $pairCount > $sameOpponentLimit;
    }

    private function canGrantRewards(User $user, int $dailyLimit): bool
    {
        return $this->rewardGrantService->countForDay($user, 'duels', 'win')
            + $this->rewardGrantService->countForDay($user, 'duels', 'loss') < $dailyLimit;
    }
}
