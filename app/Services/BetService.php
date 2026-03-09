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

        $this->missionEngine->recordEvent($bet->user, 'bet.placed');
        if ($bet->status === Bet::STATUS_WON) {
            $this->missionEngine->recordEvent($bet->user, 'bet.won');
        }

        $this->achievementService->sync($bet->user);
    }
}
