<?php

declare(strict_types=1);

namespace App\Filament\Resources\MissionProgress\Pages;

use App\Filament\Resources\MissionProgress\MissionProgressResource;
use Filament\Resources\Pages\ListRecords;

class ListMissionProgress extends ListRecords
{
    protected static string $resource = MissionProgressResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
