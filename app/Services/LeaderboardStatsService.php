<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\LeaderboardStat;
use App\Models\PointLog;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class LeaderboardStatsService
{
    public const PERIOD_WEEKLY = 'weekly';
    public const PERIOD_MONTHLY = 'monthly';

    public function recalculateForUser(User|int $user): void
    {
        $userId = $user instanceof User ? (int) $user->id : (int) $user;
        $now = CarbonImmutable::now();
        $weeklySince = $now->subDays(7);
        $monthlySince = $now->subDays(30);

        $weeklyPoints = (int) PointLog::query()
            ->where('user_id', $userId)
            ->where('amount', '>', 0)
            ->where('created_at', '>=', $weeklySince)
            ->sum('amount');

        $monthlyPoints = (int) PointLog::query()
            ->where('user_id', $userId)
            ->where('amount', '>', 0)
            ->where('created_at', '>=', $monthlySince)
            ->sum('amount');

        $this->upsertStat($userId, self::PERIOD_WEEKLY, $weeklyPoints, $now);
        $this->upsertStat($userId, self::PERIOD_MONTHLY, $monthlyPoints, $now);
    }

    public function recalculateAll(): void
    {
        $now = CarbonImmutable::now();
        $weeklySince = $now->subDays(7);
        $monthlySince = $now->subDays(30);

        $weekly = PointLog::query()
            ->selectRaw('user_id, SUM(amount) as points_total')
            ->where('amount', '>', 0)
            ->where('created_at', '>=', $weeklySince)
            ->groupBy('user_id')
            ->pluck('points_total', 'user_id');

        $monthly = PointLog::query()
            ->selectRaw('user_id, SUM(amount) as points_total')
            ->where('amount', '>', 0)
            ->where('created_at', '>=', $monthlySince)
            ->groupBy('user_id')
            ->pluck('points_total', 'user_id');

        $rows = [];

        User::query()->select('id')->chunkById(1000, function ($users) use (&$rows, $weekly, $monthly, $now): void {
            foreach ($users as $user) {
                $userId = (int) $user->id;
                $rows[] = [
                    'user_id' => $userId,
                    'period' => self::PERIOD_WEEKLY,
                    'points_total' => (int) ($weekly[$userId] ?? 0),
                    'calculated_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $rows[] = [
                    'user_id' => $userId,
                    'period' => self::PERIOD_MONTHLY,
                    'points_total' => (int) ($monthly[$userId] ?? 0),
                    'calculated_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        });

        DB::transaction(function () use ($rows): void {
            if ($rows === []) {
                return;
            }

            LeaderboardStat::query()->upsert(
                $rows,
                ['user_id', 'period'],
                ['points_total', 'calculated_at', 'updated_at']
            );
        });
    }

    private function upsertStat(int $userId, string $period, int $pointsTotal, CarbonImmutable $now): void
    {
        LeaderboardStat::query()->upsert(
            [[
                'user_id' => $userId,
                'period' => $period,
                'points_total' => max(0, $pointsTotal),
                'calculated_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]],
            ['user_id', 'period'],
            ['points_total', 'calculated_at', 'updated_at']
        );
    }
}

