<?php
declare(strict_types=1);

namespace App\Jobs;

use App\Services\LeaderboardStatsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefreshLeaderboardStatsForUserJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly int $userId
    ) {
    }

    public function handle(LeaderboardStatsService $leaderboardStatsService): void
    {
        $leaderboardStatsService->recalculateForUser($this->userId);
    }
}

