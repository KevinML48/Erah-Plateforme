<?php
declare(strict_types=1);

namespace App\Services;

use App\Enums\MissionCompletionRule;
use App\Enums\PointTransactionType;
use App\Exceptions\DailyMissionCapExceededException;
use App\Models\Mission;
use App\Models\MissionProgress;
use App\Models\MissionStep;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserEvent;
use Illuminate\Support\Facades\DB;

class MissionEngineService
{
    public function __construct(
        private readonly PointService $pointService
    ) {
    }

    public function onEvent(User $user, UserEvent $event): void
    {
        $missions = Mission::query()
            ->activeNow($event->occurred_at)
            ->whereHas('steps', fn ($query) => $query->where('step_key', $event->event_key))
            ->with(['steps' => fn ($query) => $query->orderBy('order')])
            ->get();

        foreach ($missions as $mission) {
            $this->processMissionEvent($user, $mission, $event);
        }
    }

    private function processMissionEvent(User $user, Mission $mission, UserEvent $event): void
    {
        $emitMissionCompleted = false;

        DB::transaction(function () use ($user, $mission, $event, &$emitMissionCompleted): void {
            /** @var Mission $lockedMission */
            $lockedMission = Mission::query()
                ->whereKey($mission->id)
                ->with(['steps' => fn ($query) => $query->orderBy('order')])
                ->lockForUpdate()
                ->firstOrFail();

            if (!$lockedMission->isCurrentlyActive($event->occurred_at)) {
                return;
            }

            $periodKey = $lockedMission->getPeriodKey($event->occurred_at);

            /** @var MissionProgress $progress */
            $progress = MissionProgress::query()
                ->where('mission_id', $lockedMission->id)
                ->where('user_id', $user->id)
                ->where('period_key', $periodKey)
                ->lockForUpdate()
                ->first() ?? MissionProgress::query()->create([
                    'mission_id' => $lockedMission->id,
                    'user_id' => $user->id,
                    'period_key' => $periodKey,
                    'accepted_at' => now(),
                    'progress_json' => [
                        'completed_step_ids' => [],
                        'step_counters' => [],
                        'completed_steps' => 0,
                        'total_steps' => $lockedMission->steps->count(),
                        'progress_percent' => 0,
                    ],
                ]);

            $payload = $progress->progress_json ?? [];
            $eventValue = $event->decodedEventValue();
            $stepCounters = collect($payload['step_counters'] ?? [])->mapWithKeys(fn ($value, $key) => [(string) $key => (int) $value])->all();

            foreach ($lockedMission->steps as $step) {
                if (!$this->doesStepMatchEvent($step, $event->event_key, $eventValue)) {
                    continue;
                }

                $key = (string) $step->id;
                $stepCounters[$key] = (int) ($stepCounters[$key] ?? 0) + 1;
            }

            $completedStepIds = array_keys(array_filter($stepCounters, fn (int $count): bool => $count > 0));
            $completedStepIds = array_map('intval', $completedStepIds);

            $totalSteps = max(1, $lockedMission->steps->count());
            $targetSteps = $lockedMission->completion_rule === MissionCompletionRule::AnyN
                ? max(1, (int) ($lockedMission->any_n ?? 1))
                : $totalSteps;

            $completedCount = $lockedMission->completion_rule === MissionCompletionRule::AnyN
                ? (int) array_sum($stepCounters)
                : count($completedStepIds);

            $isCompleted = $completedCount >= $targetSteps;
            $justCompleted = $isCompleted && $progress->completed_at === null;

            $progressPercent = (int) min(100, round(($completedCount / max(1, $targetSteps)) * 100));

            $payload['step_counters'] = $stepCounters;
            $payload['completed_step_ids'] = array_values(array_unique($completedStepIds));
            $payload['completed_steps'] = $completedCount;
            $payload['total_steps'] = $totalSteps;
            $payload['target_steps'] = $targetSteps;
            $payload['progress_percent'] = $progressPercent;
            $payload['last_event_key'] = $event->event_key;
            $payload['last_event_at'] = $event->occurred_at?->toDateTimeString();

            $progress->progress_json = $payload;

            if ($justCompleted) {
                $progress->completed_at = now();
                $emitMissionCompleted = true;
            }

            if ($isCompleted && !$progress->awarded_points) {
                $this->ensureDailyCap($user, (int) $lockedMission->points_reward);

                $this->pointService->addPoints(
                    user: $user,
                    amount: (int) $lockedMission->points_reward,
                    type: PointTransactionType::MissionComplete->value,
                    description: 'Mission completee: '.$lockedMission->title,
                    referenceId: (int) $progress->id,
                    referenceType: 'mission_progress',
                    idempotencyKey: 'mission-progress-award:'.$progress->id
                );

                $progress->awarded_points = true;
                $progress->awarded_at = now();
            }

            $progress->save();
        });

        if ($emitMissionCompleted) {
            // Trigger chained missions like COMPLETE_3_MISSIONS.
            app(EventTrackingService::class)->trackAction($user, 'mission_completed', [
                'mission_id' => $mission->id,
                'mission_slug' => $mission->slug,
            ]);
        }
    }

    private function doesStepMatchEvent(MissionStep $step, string $eventKey, mixed $eventValue): bool
    {
        if ($step->step_key !== $eventKey) {
            return false;
        }

        if ($step->step_value === null || $step->step_value === '') {
            return true;
        }

        $stepValue = $this->decodeStepValue($step->step_value);

        if (is_array($stepValue) && is_array($eventValue)) {
            foreach ($stepValue as $needle) {
                if (!in_array($needle, $eventValue, true)) {
                    return false;
                }
            }

            return true;
        }

        if (is_array($stepValue)) {
            return in_array((string) $eventValue, array_map('strval', $stepValue), true);
        }

        if (is_array($eventValue)) {
            return in_array((string) $stepValue, array_map('strval', $eventValue), true);
        }

        return (string) $stepValue === (string) $eventValue;
    }

    private function decodeStepValue(?string $stepValue): mixed
    {
        if ($stepValue === null || $stepValue === '') {
            return null;
        }

        $decoded = json_decode($stepValue, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        return $stepValue;
    }

    private function ensureDailyCap(User $user, int $reward): void
    {
        $cap = Setting::getValue('missions.daily_points_cap', config('missions.daily_points_cap', 500));

        if ($cap === null) {
            return;
        }

        $capValue = null;

        if (is_numeric($cap)) {
            $capValue = (int) $cap;
        }

        if (is_array($cap) && isset($cap['cap']) && is_numeric($cap['cap'])) {
            $capValue = (int) $cap['cap'];
        }

        if ($capValue === null || $capValue <= 0) {
            return;
        }

        $todayAwarded = (int) MissionProgress::query()
            ->join('missions', 'missions.id', '=', 'mission_progress.mission_id')
            ->where('mission_progress.user_id', $user->id)
            ->whereDate('mission_progress.awarded_at', now()->toDateString())
            ->where('mission_progress.awarded_points', true)
            ->sum('missions.points_reward');

        if (($todayAwarded + $reward) > $capValue) {
            throw new DailyMissionCapExceededException();
        }
    }
}
