<?php

namespace App\Filament\Resources\PointLogs\Pages;

use App\Filament\Resources\PointLogs\PointLogResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPointLog extends ViewRecord
{
    protected static string $resource = PointLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
