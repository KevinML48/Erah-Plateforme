<?php

namespace App\Filament\Resources\EsportMatches\Pages;

use App\Filament\Resources\EsportMatches\EsportMatchResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEsportMatch extends ViewRecord
{
    protected static string $resource = EsportMatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
