<?php

namespace App\Services;

use App\Models\MissionTemplate;
use App\Models\PointsTransaction;
use App\Models\User;
use App\Models\UserMission;
use Illuminate\Database\Eloquent\Builder;

class MissionStateSyncService
{
    public function __construct(
        private readonly ExperienceService $experienceService,
    ) {
    }

    /**
     * @return array{level_synced: bool, rank_synced: bool}
     */
    public function sync(User $user): array
    {
        $result = [
            'level_synced' => false,
            'rank_synced' => false,
        ];

        $activeMissions = UserMission::query()
            ->where('user_id', $user->id)
            ->whereNull('completed_at')
            ->where(function (Builder $query): void {
                $query->whereNull('expired_at')->orWhere('expired_at', '>', now());
            })
            ->whereHas('instance', function (Builder $query): void {
                $query->where('period_start', '<=', now())
                    ->where('period_end', '>=', now());
            })
            ->with('instance.template')
            ->get();

        if ($activeMissions->contains(fn (UserMission $mission): bool => $mission->instance?->template?->event_type === 'progress.level.reached')) {
            $summary = $this->experienceService->summaryFor($user);
            $level = max(1, (int) ($summary['level'] ?? 1));

            app(MissionEngine::class)->recordEvent($user, 'progress.level.reached', 1, [
                'event_key' => 'progress.level.reached.sync.'.$user->id.'.'.$level,
                'level' => $level,
                'previous_level' => max(0, $level - 1),
                'total_xp' => (int) ($summary['total_xp'] ?? 0),
                'subject_type' => PointsTransaction::class,
                'subject_id' => 'sync-level-'.$user->id.'-'.$level,
            ]);

            $result['level_synced'] = true;
        }

        if ($activeMissions->contains(fn (UserMission $mission): bool => $mission->instance?->template?->event_type === 'progress.rank.reached')) {
            $summary = $this->experienceService->summaryFor($user);
            $rank = (array) ($summary['rank'] ?? []);
            $rankKey = MissionTemplate::normalizeEventType((string) ($rank['key'] ?? ''));

            if ($rankKey !== '') {
                app(MissionEngine::class)->recordEvent($user, 'progress.rank.reached', 1, [
                    'event_key' => 'progress.rank.reached.sync.'.$user->id.'.'.$rankKey,
                    'rank_key' => $rankKey,
                    'rank_name' => (string) ($rank['name'] ?? $rankKey),
                    'total_xp' => (int) ($summary['total_xp'] ?? 0),
                    'subject_type' => User::class,
                    'subject_id' => (string) $user->id,
                ]);

                $result['rank_synced'] = true;
            }
        }

        return $result;
    }
}