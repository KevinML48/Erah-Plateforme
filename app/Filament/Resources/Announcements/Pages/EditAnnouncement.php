<?php

namespace App\Filament\Resources\Announcements\Pages;

use App\Filament\Resources\Announcements\AnnouncementResource;
use App\Services\AdminAuditService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAnnouncement extends EditRecord
{
    protected static string $resource = AnnouncementResource::class;

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
            action: 'announcement.update',
            entityType: 'announcement',
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
                    action: 'announcement.delete',
                    entityType: 'announcement',
                    entityId: (int) $this->record->id,
                    metadata: []
                );
            }),
        ];
    }
}
