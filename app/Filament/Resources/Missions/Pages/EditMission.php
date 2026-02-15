<?php

namespace App\Filament\Resources\Missions\Pages;

use App\Filament\Resources\Missions\MissionResource;
use App\Services\AdminAuditService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMission extends EditRecord
{
    protected static string $resource = MissionResource::class;

    private array $before = [];

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->before = $this->record->toArray();

        return $data;
    }

    protected function afterSave(): void
    {
        app(AdminAuditService::class)->log(
            actor: auth()->user(),
            action: 'mission.update',
            entityType: 'mission',
            entityId: (int) $this->record->id,
            metadata: ['before' => $this->before, 'after' => $this->record->fresh()?->toArray()]
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->after(function (): void {
                app(AdminAuditService::class)->log(
                    actor: auth()->user(),
                    action: 'mission.delete',
                    entityType: 'mission',
                    entityId: (int) $this->record->id,
                    metadata: []
                );
            }),
        ];
    }
}
