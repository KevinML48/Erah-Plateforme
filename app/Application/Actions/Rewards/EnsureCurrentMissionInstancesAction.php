<?php

namespace App\Application\Actions\Rewards;

use App\Models\MissionInstance;
use App\Models\MissionTemplate;
use App\Models\User;
use App\Models\UserMission;
use App\Services\SupporterAccessResolver;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
                ->lockForUpdate()
                ->get();

            foreach ($activeTemplates as $template) {
                $constraints = is_array($template->constraints) ? $template->constraints : [];
                if (($constraints['supporter_only'] ?? false) && ! $isSupporter) {
                    continue;
                }

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
        return match ($template->scope) {
            MissionTemplate::SCOPE_DAILY => [$todayStart, $todayEnd],
            MissionTemplate::SCOPE_WEEKLY => [$weekStart, $weekEnd],
            MissionTemplate::SCOPE_MONTHLY => [$monthStart, $monthEnd],
            MissionTemplate::SCOPE_ONCE => [
                $template->start_at?->copy()->startOfDay() ?? Carbon::create(2020, 1, 1, 0, 0, 0),
                $template->end_at?->copy()->endOfDay() ?? Carbon::create(2099, 12, 31, 23, 59, 59),
            ],
            MissionTemplate::SCOPE_EVENT_WINDOW => $this->eventWindowPeriod($template),
            default => [null, null],
        };
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
}
