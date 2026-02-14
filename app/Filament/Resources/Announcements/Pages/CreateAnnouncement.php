<?php

namespace App\Filament\Resources\Announcements\Pages;

use App\Filament\Resources\Announcements\AnnouncementResource;
use App\Services\AdminAuditService;
use Filament\Resources\Pages\CreateRecord;

class CreateAnnouncement extends CreateRecord
{
    protected static string $resource = AnnouncementResource::class;

    protected function afterCreate(): void
    {
        app(AdminAuditService::class)->log(
            actor: auth()->user(),
            action: 'announcement.create',
            entityType: 'announcement',
            entityId: (int) $this->record->id,
            metadata: ['after' => $this->record->toArray()]
        );
    }
}
