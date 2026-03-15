<?php

namespace App\Application\Actions\Rewards;

use App\Models\MissionInstance;
use App\Models\MissionTemplate;
use App\Models\User;
use App\Models\UserMission;
use App\Support\MySqlTimestampRange;
use App\Services\SupporterAccessResolver;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class EnsureCurrentMissionInstancesAction
{
    public function __construct(
        private readonly SupporterAccessResolver $supporterAccessResolver
    ) {
    }

    /**
     * @return array{daily: int, weekly: int, monthly: int, once: int, event_window: int}
     */
    public function execute(User $user): array
    {
        if (! $this->missionFoundationReady()) {
            return [
                'daily' => 0,
                'weekly' => 0,
                'monthly' => 0,
                'once' => 0,
                'event_window' => 0,
            ];
        }

        return DB::transaction(function () use ($user) {
            $todayStart = now()->copy()->startOfDay();
            $todayEnd = now()->copy()->endOfDay();
            $weekStart = now()->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
            $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();
            $monthStart = now()->copy()->startOfMonth()->startOfDay();
            $monthEnd = $monthStart->copy()->endOfMonth()->endOfDay();
            $isSupporter = $this->supporterAccessResolver->hasActiveSupport($user);

            $counters = [
                'daily' => 0,
                'weekly' => 0,
                'monthly' => 0,
                'once' => 0,
                'event_window' => 0,
            ];

            $activeTemplates = MissionTemplate::query()
                ->where('is_active', true)
                ->orderByDesc('is_discovery')
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $eligibleTemplates = $activeTemplates
                ->filter(function (MissionTemplate $template) use ($isSupporter): bool {
                    $constraints = is_array($template->constraints) ? $template->constraints : [];

                    if (! $template->isAvailableAt()) {
                        return false;
                    }

                    return ! (($constraints['supporter_only'] ?? false) && ! $isSupporter);
                })
                ->values();

            $selectedTemplates = $eligibleTemplates
                ->reject(fn (MissionTemplate $template): bool => $template->scope === MissionTemplate::SCOPE_DAILY)
                ->concat($this->selectDailyTemplates($eligibleTemplates->where('scope', MissionTemplate::SCOPE_DAILY), $user))
                ->values();

            foreach ($selectedTemplates as $template) {
                $constraints = is_array($template->constraints) ? $template->constraints : [];
                [$periodStart, $periodEnd] = $this->resolvePeriod(
                    $template,
                    $todayStart,
                    $todayEnd,
                    $weekStart,
                    $weekEnd,
                    $monthStart,
                    $monthEnd
                );

                if (! $periodStart || ! $periodEnd) {
                    continue;
                }

                $instance = MissionInstance::query()->firstOrCreate([
                    'mission_template_id' => $template->id,
                    'period_start' => $periodStart,
                    'period_end' => $periodEnd,
                ]);

                UserMission::query()->firstOrCreate([
                    'user_id' => $user->id,
                    'mission_instance_id' => $instance->id,
                ], [
                    'progress_count' => 0,
                    'completed_at' => null,
                ]);

                if (array_key_exists($template->scope, $counters)) {
                    $counters[$template->scope]++;
                }
            }

            return $counters;
        });
    }

    private function missionFoundationReady(): bool
    {
        $ready = Schema::hasTable('mission_event_records')
            && Schema::hasTable('user_mission_focuses');

        if (! $ready) {
            Log::warning('Mission foundation migration is missing. Mission generation skipped.', [
                'missing_mission_event_records' => ! Schema::hasTable('mission_event_records'),
                'missing_user_mission_focuses' => ! Schema::hasTable('user_mission_focuses'),
            ]);
        }

        return $ready;
    }

    /**
     * @param \Illuminate\Support\Collection<int, MissionTemplate> $dailyTemplates
     * @return \Illuminate\Support\Collection<int, MissionTemplate>
     */
    private function selectDailyTemplates($dailyTemplates, User $user)
    {
        $mix = (array) config('community.missions.daily_mix', []);
        $ordered = $dailyTemplates
            ->sortBy(fn (MissionTemplate $template): string => $this->dailySelectionKey($template, $user))
            ->values();

        $selected = collect();
        $remaining = $ordered;

        foreach (['simple', 'medium', 'special'] as $difficulty) {
            $target = max(0, (int) ($mix[$difficulty] ?? 0));
            if ($target === 0) {
                continue;
            }

            $bucket = $remaining
                ->filter(fn (MissionTemplate $template): bool => $this->templateDifficulty($template) === $difficulty)
                ->take($target);

            $selected = $selected->concat($bucket);
            $remaining = $remaining->reject(
                fn (MissionTemplate $template): bool => $bucket->contains('id', $template->id)
            )->values();
        }

        $targetTotal = array_sum(array_map('intval', $mix));
        if ($selected->count() < $targetTotal) {
            $selected = $selected->concat($remaining->take($targetTotal - $selected->count()));
        }

        return $selected->unique('id')->values();
    }

    /**
     * @return array{0: ?Carbon, 1: ?Carbon}
     */
    private function resolvePeriod(
        MissionTemplate $template,
        Carbon $todayStart,
        Carbon $todayEnd,
        Carbon $weekStart,
        Carbon $weekEnd,
        Carbon $monthStart,
        Carbon $monthEnd
    ): array {
        [$periodStart, $periodEnd] = match ($template->scope) {
            MissionTemplate::SCOPE_DAILY => [$todayStart, $todayEnd],
            MissionTemplate::SCOPE_WEEKLY => [$weekStart, $weekEnd],
            MissionTemplate::SCOPE_MONTHLY => [$monthStart, $monthEnd],
            MissionTemplate::SCOPE_ONCE => [
                $template->start_at?->copy()->startOfDay() ?? Carbon::create(2020, 1, 1, 0, 0, 0),
                $template->end_at?->copy()->endOfDay() ?? MySqlTimestampRange::max(),
            ],
            MissionTemplate::SCOPE_EVENT_WINDOW => $this->eventWindowPeriod($template),
            default => [null, null],
        };

        $periodStart = MySqlTimestampRange::clamp($periodStart);
        $periodEnd = MySqlTimestampRange::clamp($periodEnd);

        if (! $periodStart || ! $periodEnd || $periodStart->gt($periodEnd)) {
            return [null, null];
        }

        return [$periodStart, $periodEnd];
    }

    /**
     * @return array{0: ?Carbon, 1: ?Carbon}
     */
    private function eventWindowPeriod(MissionTemplate $template): array
    {
        if (! $template->start_at || ! $template->end_at) {
            return [null, null];
        }

        if (now()->lt($template->start_at) || now()->gt($template->end_at)) {
            return [null, null];
        }

        return [$template->start_at->copy(), $template->end_at->copy()];
    }

    private function templateDifficulty(MissionTemplate $template): string
    {
        $constraints = is_array($template->constraints) ? $template->constraints : [];
        $difficulty = (string) ($template->difficulty ?? $constraints['difficulty'] ?? $constraints['daily_slot'] ?? 'simple');

        return in_array($difficulty, ['simple', 'medium', 'special'], true) ? $difficulty : 'simple';
    }

    private function dailySelectionKey(MissionTemplate $template, User $user): string
    {
        return sprintf(
            '%020u',
            abs(crc32($user->id.'|'.now()->toDateString().'|'.$template->key.'|'.$template->id))
        );
    }
}
