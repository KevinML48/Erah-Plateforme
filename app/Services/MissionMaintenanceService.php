<?php

namespace App\Services;

use App\Application\Actions\Rewards\EnsureCurrentMissionInstancesAction;
use App\Models\User;
use App\Models\UserMission;
use Illuminate\Support\Collection;

class MissionMaintenanceService
{
    public function __construct(
        private readonly EnsureCurrentMissionInstancesAction $ensureCurrentMissionInstancesAction,
        private readonly MissionFocusService $missionFocusService,
    ) {
    }

    /**
     * @return array{
     *     generated: array{daily: int, weekly: int, monthly: int, once: int, event_window: int},
     *     expired_marked: int,
     *     pruned_focuses: int
     * }
     */
    public function repairForUser(User $user): array
    {
        $generated = $this->ensureCurrentMissionInstancesAction->execute($user);

        $expiredMarked = UserMission::query()
            ->where('user_id', $user->id)
            ->whereNull('expired_at')
            ->whereNull('completed_at')
            ->whereHas('instance', fn ($query) => $query->where('period_end', '<', now()))
            ->update([
                'expired_at' => now(),
            ]);

        $prunedFocuses = $this->missionFocusService->pruneUnavailable($user);

        return [
            'generated' => $generated,
            'expired_marked' => $expiredMarked,
            'pruned_focuses' => $prunedFocuses,
        ];
    }

    /**
     * @param Collection<int, User> $users
     * @return array{users: int, expired_marked: int, pruned_focuses: int}
     */
    public function repairMany(Collection $users): array
    {
        $usersCount = 0;
        $expiredCount = 0;
        $prunedCount = 0;

        foreach ($users as $user) {
            $result = $this->repairForUser($user);
            $usersCount++;
            $expiredCount += (int) $result['expired_marked'];
            $prunedCount += (int) $result['pruned_focuses'];
        }

        return [
            'users' => $usersCount,
            'expired_marked' => $expiredCount,
            'pruned_focuses' => $prunedCount,
        ];
    }
}
