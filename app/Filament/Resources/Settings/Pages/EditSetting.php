<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\SettingResource;
use App\Services\AdminAuditService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

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
            action: 'setting.update',
            entityType: 'setting',
            entityId: (int) $this->record->id,
            metadata: ['before' => $this->before, 'after' => $this->record->fresh()?->toArray()]
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()->after(function (): void {
                app(AdminAuditService::class)->log(
                    actor: auth()->user(),
                    action: 'setting.delete',
                    entityType: 'setting',
                    entityId: (int) $this->record->id,
                    metadata: []
                );
            }),
        ];
    }
}
