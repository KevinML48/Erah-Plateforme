<?php

namespace App\Filament\Resources\ContentPages\Pages;

use App\Filament\Resources\ContentPages\ContentPageResource;
use App\Services\AdminAuditService;
use Filament\Resources\Pages\CreateRecord;

class CreateContentPage extends CreateRecord
{
    protected static string $resource = ContentPageResource::class;

    protected function afterCreate(): void
    {
        app(AdminAuditService::class)->log(
            actor: auth()->user(),
            action: 'page.create',
            entityType: 'page',
            entityId: (int) $this->record->id,
            metadata: ['after' => $this->record->toArray()]
        );
    }
}
