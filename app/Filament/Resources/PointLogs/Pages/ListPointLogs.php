<?php

namespace App\Filament\Resources\PointLogs\Pages;

use App\Filament\Resources\PointLogs\PointLogResource;
use Filament\Resources\Pages\ListRecords;

class ListPointLogs extends ListRecords
{
    protected static string $resource = PointLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
