<?php

namespace App\Services;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Application\Actions\Notifications\NotifyAction;
use App\Application\Actions\Rewards\EnsureCurrentMissionInstancesAction;
use App\Domain\Notifications\Enums\NotificationCategory;
use App\Models\MissionCompletion;
use App\Models\MissionTemplate;
use App\Models\User;
use App\Models\UserMission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MissionEngine
{
    public function __construct(
        private readonly EnsureCurrentMissionInstancesAction $ensureCurrentMissionInstancesAction,
        private readonly RewardGrantService $rewardGrantService,
        private readonly NotifyAction $notifyAction,
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    /**
     * @return array{daily: int, weekly: int, monthly: int, once: int, event_window: int}
     */
    public function ensureCurrent(User $user): array
    {
        return $this->ensureCurrentMissionInstancesAction->execute($user);
    }

    /**
     * @return Collection<int, UserMission>
     */
    public function recordEvent(User $user, string $eventType, int $amount = 1, array $context = []): Collection
    {
        $this->ensureCurrent($user);

        return DB::transaction(function () use ($user, $eventType, $amount, $context) {
            $missions = UserMission::query()
                ->where('user_id', $user->id)
                ->whereNull('completed_at')
                ->whereHas('instance', function ($query): void {
                    $query->where('period_start', '<=', now())
                        ->where('period_end', '>=', now());
                })
                ->whereHas('instance.template', fn ($query) => $query->where('event_type', $eventType))
                ->with(['instance.template', 'completion'])
                ->lockForUpdate()
                ->get();

            $completed = collect();

            foreach ($missions as $mission) {
                $template = $mission->instance?->template;
                $target = max(1, (int) ($template?->target_count ?? 1));

                $mission->progress_count = min($target, (int) $mission->progress_count + max(1, $amount));

                if ($mission->progress_count >= $target && $mission->completed_at === null) {
                    $mission->completed_at = now();
                    $mission->save();

                    MissionCompletion::query()->firstOrCreate([
                        'user_id' => $user->id,
                        'user_mission_id' => $mission->id,
                    ], [
                        'completed_at' => $mission->completed_at,
                        'created_at' => now(),
                    ]);

                    $rewards = is_array($template?->rewards) ? $template->rewards : [];
                    $this->rewardGrantService->grant(
                        user: $user,
                        domain: 'missions',
                        action: 'completion',
                        dedupeKey: 'mission.completion.'.$mission->id,
                        rewards: [
                            'xp' => (int) ($rewards['xp'] ?? 0),
                            'rank_points' => (int) ($rewards['rank_points'] ?? 0),
                            'points' => (int) ($rewards['points'] ?? $rewards['reward_points'] ?? 0),
                            'bet_points' => (int) ($rewards['bet_points'] ?? 0),
                        ],
                        subjectType: UserMission::class,
                        subjectId: (string) $mission->id,
                        meta: $context + ['event_type' => $eventType],
                    );

                    $this->notifyAction->execute(
                        user: $user,
                        category: NotificationCategory::MISSION->value,
                        title: 'Mission completee',
                        message: 'Mission "'.($template?->title ?? 'Mission').'" completee.',
                        data: [
                            'mission_id' => $mission->id,
                            'event_type' => $eventType,
                        ],
                    );

                    $completed->push($mission->fresh(['instance.template', 'completion']));
                } else {
                    $mission->save();
                }
            }

            $this->grantDailyCompletionBonus($user);

            if ($missions->isNotEmpty()) {
                $this->storeAuditLogAction->execute(
                    action: 'missions.progress.recorded',
                    actor: $user,
                    target: null,
                    context: [
                        'event_type' => $eventType,
                        'amount' => $amount,
                        'missions_touched' => $missions->pluck('id')->all(),
                        'missions_completed' => $completed->pluck('id')->all(),
                    ],
                );
            }

            return $completed;
        });
    }

    private function grantDailyCompletionBonus(User $user): void
    {
        $today = now()->startOfDay();
        $dailyMissions = UserMission::query()
            ->where('user_id', $user->id)
            ->whereHas('instance.template', fn ($query) => $query->where('scope', MissionTemplate::SCOPE_DAILY))
            ->whereHas('instance', fn ($query) => $query->whereDate('period_start', $today->toDateString()))
            ->get();

        if ($dailyMissions->isEmpty() || $dailyMissions->contains(fn (UserMission $mission) => $mission->completed_at === null)) {
            return;
        }

        $bonus = (array) config('community.missions.daily_completion_bonus', []);

        $this->rewardGrantService->grant(
            user: $user,
            domain: 'missions',
            action: 'daily_bonus',
            dedupeKey: 'mission.daily-bonus.'.$user->id.'.'.$today->toDateString(),
            rewards: [
                'xp' => (int) ($bonus['xp'] ?? 0),
                'points' => (int) ($bonus['points'] ?? $bonus['reward_points'] ?? 0),
            ],
            subjectType: User::class,
            subjectId: (string) $user->id,
            meta: ['date' => $today->toDateString()],
        );
    }
}
