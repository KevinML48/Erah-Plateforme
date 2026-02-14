<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\SettingResource;
use App\Services\AdminAuditService;
use Filament\Resources\Pages\CreateRecord;

class CreateSetting extends CreateRecord
{
    protected static string $resource = SettingResource::class;

    protected function afterCreate(): void
    {
        app(AdminAuditService::class)->log(
            actor: auth()->user(),
            action: 'setting.create',
            entityType: 'setting',
            entityId: (int) $this->record->id,
            metadata: ['after' => $this->record->toArray()]
        );
    }
}
