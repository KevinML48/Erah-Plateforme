<?php
declare(strict_types=1);

namespace App\Jobs;

use App\Services\LeaderboardService;
use App\Services\LeaderboardStatsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RebuildLeaderboardStatsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(
        LeaderboardStatsService $leaderboardStatsService,
        LeaderboardService $leaderboardService
    ): void {
        $leaderboardStatsService->recalculateAll();
        $leaderboardService->invalidateCache();
    }
}

