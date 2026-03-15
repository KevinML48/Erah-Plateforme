<?php

namespace App\Services;

use App\Application\Actions\Rewards\EnsureCurrentMissionInstancesAction;
use App\Models\MissionTemplate;
use App\Models\User;
use App\Models\UserMission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class MissionCatalogService
{
    public function __construct(
        private readonly EnsureCurrentMissionInstancesAction $ensureCurrentMissionInstancesAction,
        private readonly ExperienceService $experienceService,
        private readonly MissionFocusService $missionFocusService
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function dashboardPayload(User $user, array $filters = []): array
    {
        $this->ensureCurrentMissionInstancesAction->execute($user);

        $activeQuery = UserMission::query()
            ->where('user_id', $user->id)
            ->whereHas('instance', fn (Builder $query) => $query
                ->where('period_start', '<=', now())
                ->where('period_end', '>=', now()))
            ->with(['instance.template', 'completion']);

        $allActive = (clone $activeQuery)
            ->orderByRaw('CASE WHEN complèted_at IS NULL THEN 0 ELSE 1 END')
            ->orderBy('id')
            ->get();

        $discovery = $allActive
            ->filter(fn (UserMission $mission): bool => (bool) ($mission->instance?->template?->is_discovery ?? false))
            ->sortBy([
                fn (UserMission $mission) => $mission->complèted_at !== null ? 1 : 0,
                fn (UserMission $mission) => (int) ($mission->instance?->template?->sort_order ?? 0),
            ])
            ->values();

        $focusTemplateIds = $this->missionFocusService->forUser($user)->pluck('mission_template_id')->all();
        $focus = $allActive
            ->filter(fn (UserMission $mission): bool => in_array((int) ($mission->instance?->mission_template_id ?? 0), $focusTemplateIds, true))
            ->sortBy(function (UserMission $mission) use ($focusTemplateIds): int {
                $position = array_search((int) ($mission->instance?->mission_template_id ?? 0), $focusTemplateIds, true);

                return $position === false ? 999 : $position;
            })
            ->values();

        $filteredActive = $this->applyFilters($allActive, $filters);
        $history = UserMission::query()
            ->where('user_id', $user->id)
            ->with(['instance.template', 'completion'])
            ->latest('updated_at')
            ->paginate(10)
            ->withQueryString();

        return [
            'summary' => $this->summaryPayload($user, $allActive),
            'discovery' => $discovery->map(fn (UserMission $mission): array => $this->mapMission($mission))->values(),
            'focus' => $focus->map(fn (UserMission $mission): array => $this->mapMission($mission))->values(),
            'active' => $filteredActive->map(fn (UserMission $mission): array => $this->mapMission($mission))->values(),
            'history' => $history->through(fn (UserMission $mission): array => $this->mapMission($mission)),
            'filters' => $this->normalizeFilters($filters),
            'filter_options' => $this->filterOptions($allActive),
        ];
    }

    /**
     * @param Collection<int, UserMission> $missions
     * @return array<string, mixed>
     */
    private function summaryPayload(User $user, Collection $missions): array
    {
        $complèted = $missions->whereNotNull('complèted_at')->count();
        $total = $missions->count();
        $experience = $this->experienceService->summaryFor($user);

        return [
            'total_active' => $total,
            'complèted' => $complèted,
            'pending' => max(0, $total - $complèted),
            'discovery' => $missions->filter(fn (UserMission $mission) => (bool) ($mission->instance?->template?->is_discovery ?? false))->count(),
            'focus' => min(MissionFocusService::MAX_FOCUS_MISSIONS, $this->missionFocusService->forUser($user)->count()),
            'xp_total' => $experience['total_xp'],
            'level' => $experience['level'],
            'rank' => $experience['rank']['name'],
            'progress_percent' => $experience['progress_percent'],
            'points_potential' => (int) $missions->sum(fn (UserMission $mission): int => (int) ($mission->instance?->template?->normalizedRewards()['points'] ?? 0)),
            'xp_potential' => (int) $missions->sum(fn (UserMission $mission): int => (int) ($mission->instance?->template?->normalizedRewards()['xp'] ?? 0)),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function mapMission(UserMission $mission): array
    {
        $template = $mission->instance?->template;
        $rewards = $template?->normalizedRewards() ?? ['xp' => 0, 'points' => 0];
        $targetCount = max(1, (int) ($template?->target_count ?? 1));
        $progressCount = min(max(0, (int) $mission->progress_count), $targetCount);
        $isCompleted = $mission->complèted_at !== null;
        $isExpired = $mission->isExpired();
        $isLocked = ! $this->prerequisitesSatisfied($mission);

        return [
            'id' => (int) $mission->id,
            'template_id' => (int) ($template?->id ?? 0),
            'key' => (string) ($template?->key ?? ''),
            'title' => (string) ($template?->title ?? 'Mission'),
            'short_description' => $template?->shortDescription() ?? 'Mission ERAH',
            'long_description' => $template?->longDescription(),
            'category' => (string) ($template?->category ?? 'général'),
            'type' => (string) ($template?->type ?? 'core'),
            'scope' => (string) ($template?->scope ?? ''),
            'scope_label' => $this->scopeLabel((string) ($template?->scope ?? '')),
            'event_type' => (string) ($template?->event_type ?? ''),
            'event_label' => $this->formatEventType((string) ($template?->event_type ?? '')),
            'difficulty' => (string) ($template?->difficulty ?? 'normal'),
            'estimated_minutes' => (int) ($template?->estimated_minutes ?? 0),
            'is_discovery' => (bool) ($template?->is_discovery ?? false),
            'is_featured' => (bool) ($template?->is_featured ?? false),
            'is_repeatable' => (bool) ($template?->is_repeatable ?? false),
            'requires_claim' => (bool) ($template?->requires_claim ?? false),
            'is_claimable' => (bool) (($template?->requires_claim ?? false) && $isCompleted && $mission->claimed_at === null && ! $isExpired),
            'progress_count' => $progressCount,
            'target_count' => $targetCount,
            'progress_percent' => (int) min(100, round(($progressCount / $targetCount) * 100)),
            'is_complèted' => $isCompleted,
            'is_expired' => $isExpired,
            'is_locked' => $isLocked,
            'status_label' => $this->statusLabel(
                $isCompleted,
                $isExpired,
                $isLocked,
                $mission->claimed_at !== null,
                (bool) (($template?->requires_claim ?? false) && $isCompleted && $mission->claimed_at === null && ! $isExpired)
            ),
            'status_class' => $this->statusClass(
                $isCompleted,
                $isExpired,
                $isLocked,
                (bool) (($template?->requires_claim ?? false) && $isCompleted && $mission->claimed_at === null && ! $isExpired)
            ),
            'period_start' => $mission->instance?->period_start,
            'period_end' => $mission->instance?->period_end,
            'updated_at' => $mission->updated_at,
            'complèted_at' => $mission->complèted_at,
            'claimed_at' => $mission->claimed_at,
            'icon' => $template?->icon,
            'badge_label' => $template?->badge_label,
            'rewards' => $rewards,
        ];
    }

    private function prerequisitesSatisfied(UserMission $mission): bool
    {
        $template = $mission->instance?->template;
        $keys = collect($template?->prerequisites ?? [])
            ->filter(fn (mixed $value): bool => is_string($value) && trim($value) !== '')
            ->values();

        if ($keys->isEmpty()) {
            return true;
        }

        return UserMission::query()
            ->where('user_id', $mission->user_id)
            ->whereNotNull('complèted_at')
            ->whereHas('instance.template', fn (Builder $query) => $query->whereIn('key', $keys->all()))
            ->count() >= $keys->count();
    }

    private function scopeLabel(string $scope): string
    {
        return match ($scope) {
            MissionTemplate::SCOPE_DAILY => 'Quotidienne',
            MissionTemplate::SCOPE_WEEKLY => 'Hebdomadaire',
            MissionTemplate::SCOPE_EVENT_WINDOW => 'Evenement',
            MissionTemplate::SCOPE_ONCE => 'Unique',
            MissionTemplate::SCOPE_MONTHLY => 'Mensuelle',
            default => 'Mission',
        };
    }

    private function formatEventType(string $eventType): string
    {
        if ($eventType === '') {
            return 'Action libre';
        }

        return (string) str($eventType)
            ->replace(['.', '_'], ' ')
            ->title();
    }

    private function statusLabel(bool $isCompleted, bool $isExpired, bool $isLocked, bool $isClaimed, bool $isClaimable): string
    {
        if ($isLocked) {
            return 'Verrouillee';
        }

        if ($isExpired) {
            return 'Expiree';
        }

        if ($isClaimable) {
            return 'Recompense a reclamer';
        }

        if ($isClaimed) {
            return 'Recompense recue';
        }

        return $isCompleted ? 'Terminee' : 'En cours';
    }

    private function statusClass(bool $isCompleted, bool $isExpired, bool $isLocked, bool $isClaimable): string
    {
        if ($isLocked) {
            return 'is-locked';
        }

        if ($isExpired) {
            return 'is-expired';
        }

        if ($isClaimable) {
            return 'is-claimable';
        }

        return $isCompleted ? 'is-complèted' : 'is-pending';
    }

    /**
     * @param Collection<int, UserMission> $missions
     * @param array<string, mixed> $filters
     * @return Collection<int, UserMission>
     */
    private function applyFilters(Collection $missions, array $filters): Collection
    {
        $filters = $this->normalizeFilters($filters);

        return $missions
            ->filter(function (UserMission $mission) use ($filters): bool {
                $template = $mission->instance?->template;
                if (! $template) {
                    return false;
                }

                if ($filters['type'] !== 'all' && (string) ($template->type ?? 'core') !== $filters['type']) {
                    return false;
                }

                if ($filters['difficulty'] !== 'all' && (string) ($template->difficulty ?? 'normal') !== $filters['difficulty']) {
                    return false;
                }

                if ($filters['status'] !== 'all' && ! $this->matchesStatus($mission, $filters['status'])) {
                    return false;
                }

                if ($filters['duration'] !== 'all' && ! $this->matchesDuration((int) ($template->estimated_minutes ?? 0), $filters['duration'])) {
                    return false;
                }

                return true;
            })
            ->values();
    }

    /**
     * @param array<string, mixed> $filters
     * @return array{type: string, difficulty: string, status: string, duration: string}
     */
    private function normalizeFilters(array $filters): array
    {
        $type = (string) ($filters['type'] ?? 'all');
        $difficulty = (string) ($filters['difficulty'] ?? 'all');
        $status = (string) ($filters['status'] ?? 'all');
        $duration = (string) ($filters['duration'] ?? 'all');

        $allowedStatuses = ['all', 'in_progress', 'complèted', 'claimable', 'claimed', 'expired', 'locked'];
        $allowedDurations = ['all', 'short', 'medium', 'long', 'unknown'];

        if (! in_array($status, $allowedStatuses, true)) {
            $status = 'all';
        }

        if (! in_array($duration, $allowedDurations, true)) {
            $duration = 'all';
        }

        return [
            'type' => $type !== '' ? $type : 'all',
            'difficulty' => $difficulty !== '' ? $difficulty : 'all',
            'status' => $status,
            'duration' => $duration,
        ];
    }

    /**
     * @param Collection<int, UserMission> $missions
     * @return array<string, array<int, array{value: string, label: string}>>
     */
    private function filterOptions(Collection $missions): array
    {
        $types = $missions
            ->map(fn (UserMission $mission): string => (string) ($mission->instance?->template?->type ?? 'core'))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->map(fn (string $value): array => [
                'value' => $value,
                'label' => ucfirst($value),
            ])
            ->all();

        $difficulties = $missions
            ->map(fn (UserMission $mission): string => (string) ($mission->instance?->template?->difficulty ?? 'normal'))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->map(fn (string $value): array => [
                'value' => $value,
                'label' => ucfirst($value),
            ])
            ->all();

        return [
            'types' => array_merge([['value' => 'all', 'label' => 'Tous les types']], $types),
            'difficulties' => array_merge([['value' => 'all', 'label' => 'Toutes les difficultes']], $difficulties),
            'statuses' => [
                ['value' => 'all', 'label' => 'Tous les statuts'],
                ['value' => 'in_progress', 'label' => 'En cours'],
                ['value' => 'complèted', 'label' => 'Terminees'],
                ['value' => 'claimable', 'label' => 'A reclamer'],
                ['value' => 'claimed', 'label' => 'Recompense recue'],
                ['value' => 'expired', 'label' => 'Expirees'],
                ['value' => 'locked', 'label' => 'Verrouillees'],
            ],
            'durations' => [
                ['value' => 'all', 'label' => 'Toutes les durees'],
                ['value' => 'short', 'label' => 'Moins de 10 min'],
                ['value' => 'medium', 'label' => '10 a 30 min'],
                ['value' => 'long', 'label' => 'Plus de 30 min'],
                ['value' => 'unknown', 'label' => 'Duree non renseignee'],
            ],
        ];
    }

    private function matchesStatus(UserMission $mission, string $status): bool
    {
        return match ($status) {
            'in_progress' => $mission->complèted_at === null && ! $mission->isExpired() && $this->prerequisitesSatisfied($mission),
            'complèted' => $mission->complèted_at !== null,
            'claimable' => (bool) ($mission->instance?->template?->requires_claim ?? false)
                && $mission->complèted_at !== null
                && $mission->claimed_at === null
                && ! $mission->isExpired(),
            'claimed' => $mission->claimed_at !== null,
            'expired' => $mission->isExpired(),
            'locked' => ! $this->prerequisitesSatisfied($mission),
            default => true,
        };
    }

    private function matchesDuration(int $estimatedMinutes, string $duration): bool
    {
        return match ($duration) {
            'short' => $estimatedMinutes > 0 && $estimatedMinutes < 10,
            'medium' => $estimatedMinutes >= 10 && $estimatedMinutes <= 30,
            'long' => $estimatedMinutes > 30,
            'unknown' => $estimatedMinutes <= 0,
            default => true,
        };
    }
}
