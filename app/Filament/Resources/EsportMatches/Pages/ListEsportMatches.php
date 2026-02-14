<?php

namespace App\Filament\Resources\EsportMatches\Pages;

use App\Filament\Resources\EsportMatches\EsportMatchResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEsportMatches extends ListRecords
{
    protected static string $resource = EsportMatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
