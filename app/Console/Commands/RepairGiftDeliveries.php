<?php

namespace App\Console\Commands;

use App\Services\Gifts\GiftDeliveryRepairService;
use Illuminate\Console\Command;

class RepairGiftDeliveries extends Command
{
    protected $signature = 'erah:repair-gift-deliveries
        {--user-id= : Restrict repair to one user id}
        {--chunk=100 : Chunk size for scans}
        {--dry-run : Preview repairs without writing}';

    protected $description = 'Repair profile-digital gift redemptions that should have been auto-delivered on the user profile.';

    public function handle(GiftDeliveryRepairService $giftDeliveryRepairService): int
    {
        $stats = $giftDeliveryRepairService->repair(
            userId: $this->option('user-id') !== null ? (int) $this->option('user-id') : null,
            dryRun: (bool) $this->option('dry-run'),
            chunk: max(1, (int) $this->option('chunk')),
        );

        $this->table(array_keys($stats), [array_values($stats)]);

        return self::SUCCESS;
    }
}