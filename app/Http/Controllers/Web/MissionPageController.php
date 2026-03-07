<?php

namespace App\Http\Controllers\Web;

use App\Application\Actions\Rewards\EnsureCurrentMissionInstancesAction;
use App\Http\Controllers\Controller;
use App\Models\MissionTemplate;
use App\Models\UserMission;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class MissionPageController extends Controller
{
    public function index(EnsureCurrentMissionInstancesAction $ensureCurrentMissionInstancesAction): View
    {
        $user = auth()->user();
        $ensureCurrentMissionInstancesAction->execute($user);

        $todayStart = now()->startOfDay();
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        $dailyMissions = UserMission::query()
            ->where('user_id', $user->id)
            ->whereHas('instance.template', function ($query): void {
                $query->where('scope', MissionTemplate::SCOPE_DAILY);
            })
            ->whereHas('instance', function ($query) use ($todayStart): void {
                $query->whereDate('period_start', $todayStart->toDateString());
            })
            ->with(['instance.template', 'completion'])
            ->orderByDesc('completed_at')
            ->orderByDesc('updated_at')
            ->get();

        $weeklyMissions = UserMission::query()
            ->where('user_id', $user->id)
            ->whereHas('instance.template', function ($query): void {
                $query->where('scope', MissionTemplate::SCOPE_WEEKLY);
            })
            ->whereHas('instance', function ($query) use ($weekStart, $weekEnd): void {
                $query->whereBetween('period_start', [$weekStart, $weekEnd]);
            })
            ->with(['instance.template', 'completion'])
            ->orderByDesc('completed_at')
            ->orderByDesc('updated_at')
            ->get();

        $specialMissions = UserMission::query()
            ->where('user_id', $user->id)
            ->whereHas('instance.template', function ($query): void {
                $query->whereIn('scope', [
                    MissionTemplate::SCOPE_ONCE,
                    MissionTemplate::SCOPE_MONTHLY,
                    MissionTemplate::SCOPE_EVENT_WINDOW,
                ]);
            })
            ->whereHas('instance', function ($query): void {
                $query->where('period_start', '<=', now())
                    ->where('period_end', '>=', now());
            })
            ->with(['instance.template', 'completion'])
            ->orderByDesc('completed_at')
            ->orderByDesc('updated_at')
            ->get();

        $history = UserMission::query()
            ->where('user_id', $user->id)
            ->with(['instance.template', 'completion'])
            ->latest('updated_at')
            ->paginate(8)
            ->withQueryString();

        $dailyCards = $dailyMissions->map(fn (UserMission $mission): array => $this->mapMission($mission));
        $weeklyCards = $weeklyMissions->map(fn (UserMission $mission): array => $this->mapMission($mission));
        $specialCards = $specialMissions->map(fn (UserMission $mission): array => $this->mapMission($mission));

        $historyCards = $history->through(fn (UserMission $mission): array => $this->mapMission($mission));
        $missionStats = $this->buildMissionStats($dailyCards, $weeklyCards, $specialCards);

        return view('pages.missions.index', [
            'dailyCards' => $dailyCards,
            'weeklyCards' => $weeklyCards,
            'specialCards' => $specialCards,
            'history' => $historyCards,
            'missionStats' => $missionStats,
        ]);
    }

    private function mapMission(UserMission $mission): array
    {
        $template = $mission->instance?->template;
        $targetCount = max(1, (int) ($template->target_count ?? 1));
        $progressCount = min(max(0, (int) $mission->progress_count), $targetCount);
        $isCompleted = $mission->completed_at !== null;
        $rewards = $this->normalizeRewards(is_array($template?->rewards) ? $template->rewards : []);

        return [
            'id' => (int) $mission->id,
            'title' => (string) ($template->title ?? 'Mission'),
            'description' => (string) ($template->description ?? 'Aucune description.'),
            'scope' => (string) ($template->scope ?? ''),
            'scope_label' => $this->scopeLabel((string) ($template->scope ?? '')),
            'event_type' => (string) ($template->event_type ?? ''),
            'event_label' => $this->formatEventType((string) ($template->event_type ?? '')),
            'target_count' => $targetCount,
            'progress_count' => $progressCount,
            'progress_percent' => (int) min(100, round(($progressCount / $targetCount) * 100)),
            'is_completed' => $isCompleted,
            'status_label' => $isCompleted ? 'Completee' : 'En cours',
            'status_class' => $isCompleted ? 'is-completed' : 'is-pending',
            'period_start' => $mission->instance?->period_start,
            'period_end' => $mission->instance?->period_end,
            'updated_at' => $mission->updated_at,
            'completed_at' => $mission->completed_at,
            'rewards' => $rewards,
        ];
    }

    /**
     * @return array{xp: int, rank_points: int, reward_points: int, bet_points: int}
     */
    private function normalizeRewards(array $rewards): array
    {
        return [
            'xp' => max(0, (int) ($rewards['xp'] ?? $rewards['xp_amount'] ?? 0)),
            'rank_points' => max(0, (int) ($rewards['rank_points'] ?? $rewards['rank_points_amount'] ?? 0)),
            'reward_points' => max(0, (int) ($rewards['reward_points'] ?? $rewards['reward_points_amount'] ?? 0)),
            'bet_points' => max(0, (int) ($rewards['bet_points'] ?? $rewards['bet_points_amount'] ?? 0)),
        ];
    }

    private function scopeLabel(string $scope): string
    {
        return match ($scope) {
            MissionTemplate::SCOPE_DAILY => 'Daily',
            MissionTemplate::SCOPE_WEEKLY => 'Weekly',
            MissionTemplate::SCOPE_MONTHLY => 'Monthly',
            MissionTemplate::SCOPE_ONCE => 'One-shot',
            MissionTemplate::SCOPE_EVENT_WINDOW => 'Event',
            default => 'Mission',
        };
    }

    private function formatEventType(string $eventType): string
    {
        if ($eventType === '') {
            return 'Evenement libre';
        }

        return (string) str($eventType)
            ->replace(['.', '_'], ' ')
            ->title();
    }

    /**
     * @param Collection<int, array<string, mixed>> $dailyCards
     * @param Collection<int, array<string, mixed>> $weeklyCards
     * @param Collection<int, array<string, mixed>> $specialCards
     * @return array{
     *     total: int,
     *     completed: int,
     *     pending: int,
     *     completion_rate: int,
     *     xp_potential: int,
     *     rank_potential: int,
     *     reward_potential: int,
     *     bet_potential: int
     * }
     */
    private function buildMissionStats(Collection $dailyCards, Collection $weeklyCards, Collection $specialCards): array
    {
        $allCards = $dailyCards
            ->concat($weeklyCards)
            ->concat($specialCards)
            ->values();

        $total = $allCards->count();
        $completed = $allCards->where('is_completed', true)->count();
        $pending = max(0, $total - $completed);
        $completionRate = $total > 0 ? (int) round(($completed / $total) * 100) : 0;

        return [
            'total' => $total,
            'completed' => $completed,
            'pending' => $pending,
            'completion_rate' => $completionRate,
            'xp_potential' => (int) $allCards->sum(fn (array $card): int => (int) ($card['rewards']['xp'] ?? 0)),
            'rank_potential' => (int) $allCards->sum(fn (array $card): int => (int) ($card['rewards']['rank_points'] ?? 0)),
            'reward_potential' => (int) $allCards->sum(fn (array $card): int => (int) ($card['rewards']['reward_points'] ?? 0)),
            'bet_potential' => (int) $allCards->sum(fn (array $card): int => (int) ($card['rewards']['bet_points'] ?? 0)),
        ];
    }
}
