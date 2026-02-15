<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Mission;
use App\Models\MissionProgress;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MissionService
{
    public function listActiveMissionsForUser(User $user, ?Carbon $now = null, int $perPage = 20): LengthAwarePaginator
    {
        $now ??= now();

        $missions = Mission::query()
            ->activeNow($now)
            ->with([
                'steps:id,mission_id,step_key,step_value,label,order',
                'progresses' => fn ($query) => $query
                    ->where('user_id', $user->id)
                    ->whereIn('period_key', $this->currentPeriodKeys($now)),
            ])
            ->orderByDesc('id')
            ->paginate($perPage);

        return $this->attachProgressPayload($missions, $user, $now);
    }

    public function listInProgressMissionsForUser(User $user, ?Carbon $now = null, int $perPage = 20): LengthAwarePaginator
    {
        $now ??= now();

        $missions = Mission::query()
            ->activeNow($now)
            ->whereHas('progresses', function ($query) use ($user, $now): void {
                $query
                    ->where('user_id', $user->id)
                    ->whereIn('period_key', $this->currentPeriodKeys($now))
                    ->whereNull('completed_at');
            })
            ->with([
                'steps:id,mission_id,step_key,step_value,label,order',
                'progresses' => fn ($query) => $query
                    ->where('user_id', $user->id)
                    ->whereIn('period_key', $this->currentPeriodKeys($now)),
            ])
            ->orderByDesc('id')
            ->paginate($perPage);

        return $this->attachProgressPayload($missions, $user, $now);
    }

    public function listCompletedMissionsForUser(User $user, int $perPage = 20): LengthAwarePaginator
    {
        return MissionProgress::query()
            ->with(['mission:id,title,slug,points_reward,recurrence'])
            ->where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->orderByDesc('completed_at')
            ->paginate($perPage);
    }

    public function getMissionBySlugForUser(string $slug, User $user, ?Carbon $now = null): Mission
    {
        $now ??= now();

        $mission = Mission::query()
            ->with([
                'steps:id,mission_id,step_key,step_value,label,order',
                'progresses' => fn ($query) => $query
                    ->where('user_id', $user->id)
                    ->whereIn('period_key', $this->currentPeriodKeys($now)),
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        $mission->setAttribute('user_progress', $this->buildUserProgress($mission, $user, $now));

        return $mission;
    }

    public function buildUserProgress(Mission $mission, User $user, ?Carbon $now = null): array
    {
        $now ??= now();
        $periodKey = $mission->getPeriodKey($now);

        $progress = $mission->relationLoaded('progresses')
            ? $mission->progresses->firstWhere('period_key', $periodKey)
            : MissionProgress::query()
                ->where('mission_id', $mission->id)
                ->where('user_id', $user->id)
                ->where('period_key', $periodKey)
                ->first();

        $payload = $progress?->progress_json ?? [];
        $completedSteps = (int) ($payload['completed_steps'] ?? 0);
        $targetSteps = (int) ($payload['target_steps'] ?? max(1, $mission->getTargetStepsCount()));
        $percent = (int) ($payload['progress_percent'] ?? min(100, round(($completedSteps / max(1, $targetSteps)) * 100)));

        return [
            'period_key' => $periodKey,
            'is_started' => $progress !== null,
            'status_label' => $progress === null ? 'Non demarree' : ($progress->completed_at ? 'Terminee' : 'En cours'),
            'accepted_at' => $progress?->accepted_at,
            'completed_steps' => $completedSteps,
            'target_steps' => $targetSteps,
            'progress_percent' => $percent,
            'completed_step_ids' => $payload['completed_step_ids'] ?? [],
            'is_completed' => (bool) $progress?->completed_at,
            'awarded_points' => (bool) $progress?->awarded_points,
            'awarded_at' => $progress?->awarded_at,
            'completed_at' => $progress?->completed_at,
        ];
    }

    private function attachProgressPayload(LengthAwarePaginator $missions, User $user, Carbon $now): LengthAwarePaginator
    {
        $missions->getCollection()->transform(function (Mission $mission) use ($user, $now): Mission {
            $mission->setAttribute('user_progress', $this->buildUserProgress($mission, $user, $now));

            return $mission;
        });

        return $missions;
    }

    private function currentPeriodKeys(Carbon $now): array
    {
        return [
            'ALL',
            $now->format('Y-m-d'),
            $now->format('o-\\WW'),
            $now->format('Y-m'),
        ];
    }
}
