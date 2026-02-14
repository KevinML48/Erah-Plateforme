<?php
declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\MatchStatus;
use App\Enums\RewardRedemptionStatus;
use App\Models\EsportMatch;
use App\Models\PointLog;
use App\Models\RewardRedemption;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminOverviewWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $since = now()->subDays(7);

        $totalUsers = User::query()->count();
        $activeUsers = User::query()
            ->where('updated_at', '>=', $since)
            ->count();

        $pointsDistributed = (int) PointLog::query()
            ->where('created_at', '>=', $since)
            ->where('amount', '>', 0)
            ->sum('amount');

        $pendingRedemptions = RewardRedemption::query()
            ->where('status', RewardRedemptionStatus::Pending)
            ->count();

        $upcomingMatches = EsportMatch::query()
            ->whereIn('status', [MatchStatus::Open, MatchStatus::Locked, MatchStatus::Live])
            ->where('starts_at', '>=', now())
            ->count();

        return [
            Stat::make('Total users', number_format($totalUsers)),
            Stat::make('Active users (7j)', number_format($activeUsers)),
            Stat::make('Points distributed (7j)', number_format($pointsDistributed)),
            Stat::make('Pending redemptions', number_format($pendingRedemptions)),
            Stat::make('Upcoming matches', number_format($upcomingMatches)),
        ];
    }
}

