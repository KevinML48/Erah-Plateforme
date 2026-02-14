<?php

declare(strict_types=1);

namespace App\Filament\Resources\Rewards\Pages;

use App\Filament\Resources\Rewards\RewardResource;
use App\Services\AdminAuditService;
use Filament\Resources\Pages\CreateRecord;

class CreateReward extends CreateRecord
{
    protected static string $resource = RewardResource::class;

    protected function afterCreate(): void
    {
        /** @var AdminAuditService $audit */
        $audit = app(AdminAuditService::class);

        $audit->log(
            actor: auth()->user(),
            action: 'reward.create',
            entityType: 'reward',
            entityId: (int) $this->record->id,
            metadata: ['after' => $this->record->toArray()]
        );
    }
}
