<?php

namespace App\Services;

use App\Models\Bet;

class BetService
{
    public function __construct(
        private readonly RewardGrantService $rewardGrantService,
        private readonly MissionEngine $missionEngine,
        private readonly AchievementService $achievementService
    ) {
    }

    public function rewardSettlement(Bet $bet): void
    {
        if (! in_array($bet->status, [Bet::STATUS_WON, Bet::STATUS_LOST], true) || ! $bet->user) {
            return;
        }

        $action = $bet->status === Bet::STATUS_WON ? 'win' : 'loss';
        $dailyLimit = (int) config('community.bets.daily_xp_limit', 20);
        $dailyCount = $this->rewardGrantService->countForDay($bet->user, 'bets', 'win')
            + $this->rewardGrantService->countForDay($bet->user, 'bets', 'loss');

        if ($dailyCount < $dailyLimit) {
            $this->rewardGrantService->grant(
                user: $bet->user,
                domain: 'bets',
                action: $action,
                dedupeKey: 'bet.'.$action.'.'.$bet->id,
                rewards: [
                    'xp' => (int) config('community.bets.rewards.'.$action.'.xp', 0),
                ],
                subjectType: Bet::class,
                subjectId: (string) $bet->id,
            );
        }

        $this->missionEngine->recordEvent($bet->user, 'bet.placed', 1, [
            'event_key' => 'bet.placed.'.$bet->id,
            'subject_type' => Bet::class,
            'subject_id' => (string) $bet->id,
            'stake_points' => (int) $bet->stake_points,
        ]);
        if ($bet->status === Bet::STATUS_WON) {
            $this->missionEngine->recordEvent($bet->user, 'bet.won', 1, [
                'event_key' => 'bet.won.'.$bet->id,
                'subject_type' => Bet::class,
                'subject_id' => (string) $bet->id,
                'stake_points' => (int) $bet->stake_points,
                'status' => Bet::STATUS_WON,
            ]);
        }

        $this->achievementService->sync($bet->user);
    }
}
