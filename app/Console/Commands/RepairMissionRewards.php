<?php

namespace App\Console\Commands;

use App\Services\MissionRepairService;
use Illuminate\Console\Command;

class RepairMissionRewards extends Command
{
    protected $signature = 'erah:repair-mission-rewards
        {--user-id= : Restrict repair to one user id}
        {--chunk=100 : Chunk size for scans}
        {--dry-run : Preview repairs without writing}';

    protected $description = 'Repair completed mission rewards, claimable states, and missing completion rows without duplicating grants.';

    public function handle(MissionRepairService $missionRepairService): int
    {
        $stats = $missionRepairService->repair(
            userId: $this->option('user-id') !== null ? (int) $this->option('user-id') : null,
            dryRun: (bool) $this->option('dry-run'),
            chunk: max(1, (int) $this->option('chunk')),
        );

        $this->table(array_keys($stats), [array_values($stats)]);

        return self::SUCCESS;
    }
}