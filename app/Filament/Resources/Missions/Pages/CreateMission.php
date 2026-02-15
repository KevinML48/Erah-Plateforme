<?php

namespace App\Filament\Resources\Missions\Pages;

use App\Filament\Resources\Missions\MissionResource;
use App\Services\AdminAuditService;
use Filament\Resources\Pages\CreateRecord;

class CreateMission extends CreateRecord
{
    protected static string $resource = MissionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        app(AdminAuditService::class)->log(
            actor: auth()->user(),
            action: 'mission.create',
            entityType: 'mission',
            entityId: (int) $this->record->id,
            metadata: ['after' => $this->record->toArray()]
        );
    }
}
