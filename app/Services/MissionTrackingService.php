<?php

namespace App\Services;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Application\Actions\Notifications\NotifyAction;
use App\Application\Actions\Rewards\EnsureCurrentMissionInstancesAction;
use App\Domain\Notifications\Enums\NotificationCategory;
use App\Models\MissionCompletion;
use App\Models\MissionEventRecord;
use App\Models\MissionTemplate;
use App\Models\User;
use App\Models\UserMission;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class MissionTrackingService
{
    public function __construct(
        private readonly EnsureCurrentMissionInstancesAction $ensureCurrentMissionInstancesAction,
        private readonly MissionConstraintEvaluator $missionConstraintEvaluator,
        private readonly RewardGrantService $rewardGrantService,
        private readonly NotifyAction $notifyAction,
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    /**
     * @param array<string, mixed> $context
     * @return Collection<int, UserMission>
     */
    public function record(
        User $user,
        string $eventType,
        int $amount = 1,
        array $context = [],
        ?string $eventKey = null,
        ?string $subjectType = null,
        ?string $subjectId = null
    ): Collection {
        if (! $this->missionFoundationReady()) {
            return collect();
        }

        $normalizedEventType = MissionTemplate::normalizeEventType($eventType);
        $eventKey = $eventKey ?: $this->defaultEventKey($user, $normalizedEventType, $subjectType, $subjectId, $context);

        $this->ensureCurrentMissionInstancesAction->execute($user);

        return DB::transaction(function () use ($user, $normalizedEventType, $amount, $context, $eventKey, $subjectType, $subjectId) {
            $event = MissionEventRecord::query()
                ->where('user_id', $user->id)
                ->where('event_key', $eventKey)
                ->lockForUpdate()
                ->first();

            if ($event) {
                return collect();
            }

            $event = MissionEventRecord::query()->create([
                'user_id' => $user->id,
                'event_key' => $eventKey,
                'event_type' => $normalizedEventType,
                'subject_type' => $subjectType,
                'subject_id' => $subjectId,
                'amount' => max(1, $amount),
                'context' => $context,
                'occurred_at' => now(),
                'processed_at' => now(),
            ]);

            $missions = UserMission::query()
                ->where('user_id', $user->id)
                ->whereNull('completed_at')
                ->where(function (Builder $query): void {
                    $query->whereNull('expired_at')->orWhere('expired_at', '>', now());
                })
                ->whereHas('instance', function (Builder $query): void {
                    $query->where('period_start', '<=', now())
                        ->where('period_end', '>=', now());
                })
                ->whereHas('instance.template', fn (Builder $query) => $query->where('event_type', $normalizedEventType))
                ->with(['instance.template', 'completion'])
                ->lockForUpdate()
                ->get();

            $completed = collect();

            foreach ($missions as $mission) {
                $template = $mission->instance?->template;
                if (! $template || ! $this->missionConstraintEvaluator->passes($user, $template, $context)) {
                    continue;
                }

                $target = max(1, (int) $template->target_count);
                $mission->progress_count = min($target, (int) $mission->progress_count + max(1, $amount));
                $mission->last_tracked_at = now();

                if ($mission->progress_count >= $target && $mission->completed_at === null) {
                    $mission->completed_at = now();
                }

                $mission->save();

                if ($mission->completed_at === null) {
                    continue;
                }

                MissionCompletion::query()->firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'user_mission_id' => $mission->id,
                    ],
                    [
                        'completed_at' => $mission->completed_at,
                        'created_at' => now(),
                    ],
                );

                $this->grantMissionRewards($user, $mission, $normalizedEventType, $context);
                $completed->push($mission->fresh(['instance.template', 'completion']));
            }

            $this->grantDailyCompletionBonus($user);

            $this->storeAuditLogAction->execute(
                action: 'missions.progress.recorded',
                actor: $user,
                target: $event,
                context: [
                    'event_type' => $normalizedEventType,
                    'event_key' => $eventKey,
                    'amount' => $amount,
                    'missions_completed' => $completed->pluck('id')->all(),
                ],
            );

            return $completed;
        });
    }

    /**
     * @param array<string, mixed> $context
     */
    private function grantMissionRewards(User $user, UserMission $mission, string $eventType, array $context): void
    {
        $template = $mission->instance?->template;
        if (! $template || $mission->claimed_at !== null) {
            return;
        }

        if ($template->requires_claim) {
            $mission->rewarded_at = null;
            $mission->claimed_at = null;
            $mission->save();

            $this->notifyAction->execute(
                user: $user,
                category: NotificationCategory::MISSION->value,
                title: 'Mission terminee',
                message: 'Mission "'.$template->title.'" terminee. La recompense est prete a etre reclamee.',
                data: [
                    'mission_id' => $mission->id,
                    'mission_template_key' => $template->key,
                    'requires_claim' => true,
                ],
            );

            return;
        }

        if ($mission->rewarded_at !== null) {
            return;
        }

        $rewards = $template->normalizedRewards();

        $this->rewardGrantService->grant(
            user: $user,
            domain: 'missions',
            action: 'completion',
            dedupeKey: 'mission.completion.'.$mission->id,
            rewards: $rewards,
            subjectType: UserMission::class,
            subjectId: (string) $mission->id,
            meta: $context + [
                'event_type' => $eventType,
                'mission_template_key' => $template->key,
            ],
        );

        $mission->rewarded_at = now();
        $mission->claimed_at = $template->requires_claim ? null : $mission->rewarded_at;
        $mission->save();

        $this->notifyAction->execute(
            user: $user,
            category: NotificationCategory::MISSION->value,
            title: 'Mission terminee',
            message: 'Mission "'.$template->title.'" terminee.',
            data: [
                'mission_id' => $mission->id,
                'mission_template_key' => $template->key,
            ],
        );
    }

    private function grantDailyCompletionBonus(User $user): void
    {
        $today = now()->startOfDay();
        $dailyMissions = UserMission::query()
            ->where('user_id', $user->id)
            ->whereHas('instance.template', fn (Builder $query) => $query->where('scope', MissionTemplate::SCOPE_DAILY))
            ->whereHas('instance', fn (Builder $query) => $query->whereDate('period_start', $today->toDateString()))
            ->get();

        if ($dailyMissions->isEmpty() || $dailyMissions->contains(fn (UserMission $mission): bool => $mission->completed_at === null)) {
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

    /**
     * @param array<string, mixed> $context
     */
    private function defaultEventKey(
        User $user,
        string $eventType,
        ?string $subjectType,
        ?string $subjectId,
        array $context
    ): string {
        if ($subjectType && $subjectId) {
            return $eventType.'.'.$user->id.'.'.$subjectType.'.'.$subjectId;
        }

        if (isset($context['date'])) {
            return $eventType.'.'.$user->id.'.'.$context['date'];
        }

        return $eventType.'.'.$user->id.'.'.md5(json_encode(Arr::sortRecursive($context)));
    }

    private function missionFoundationReady(): bool
    {
        $ready = Schema::hasTable('mission_event_records')
            && Schema::hasTable('user_mission_focuses');

        if (! $ready) {
            Log::warning('Mission foundation migration is missing. Mission tracking skipped.', [
                'missing_mission_event_records' => ! Schema::hasTable('mission_event_records'),
                'missing_user_mission_focuses' => ! Schema::hasTable('user_mission_focuses'),
            ]);
        }

        return $ready;
    }
}
