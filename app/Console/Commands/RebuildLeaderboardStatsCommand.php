<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\LeaderboardService;
use App\Services\LeaderboardStatsService;
use Illuminate\Console\Command;

class RebuildLeaderboardStatsCommand extends Command
{
    protected $signature = 'leaderboard:rebuild-stats';
    protected $description = 'Rebuild aggregated leaderboard stats for weekly and monthly periods.';

    public function handle(
        LeaderboardStatsService $leaderboardStatsService,
        LeaderboardService $leaderboardService
    ): int {
        $leaderboardStatsService->recalculateAll();
        $leaderboardService->invalidateCache();

        $this->info('Leaderboard stats rebuilt successfully.');

        return self::SUCCESS;
    }
}

