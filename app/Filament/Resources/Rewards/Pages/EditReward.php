<?php

declare(strict_types=1);

namespace App\Filament\Resources\Rewards\Pages;

use App\Filament\Resources\Rewards\RewardResource;
use App\Services\AdminAuditService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditReward extends EditRecord
{
    protected static string $resource = RewardResource::class;

    private array $before = [];

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->before = $this->record->toArray();

        return $data;
    }

    protected function afterSave(): void
    {
        /** @var AdminAuditService $audit */
        $audit = app(AdminAuditService::class);

        $audit->log(
            actor: auth()->user(),
            action: 'reward.update',
            entityType: 'reward',
            entityId: (int) $this->record->id,
            metadata: [
                'before' => $this->before,
                'after' => $this->record->fresh()?->toArray(),
            ]
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->after(function (): void {
                    /** @var AdminAuditService $audit */
                    $audit = app(AdminAuditService::class);

                    $audit->log(
                        actor: auth()->user(),
                        action: 'reward.delete',
                        entityType: 'reward',
                        entityId: (int) $this->record->id,
                        metadata: []
                    );
                }),
        ];
    }
}
