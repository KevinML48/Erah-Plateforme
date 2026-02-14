<?php

namespace App\Filament\Resources\ContentPages\Pages;

use App\Filament\Resources\ContentPages\ContentPageResource;
use App\Services\AdminAuditService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditContentPage extends EditRecord
{
    protected static string $resource = ContentPageResource::class;

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
            action: 'page.update',
            entityType: 'page',
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
                    action: 'page.delete',
                    entityType: 'page',
                    entityId: (int) $this->record->id,
                    metadata: []
                );
            }),
        ];
    }
}
