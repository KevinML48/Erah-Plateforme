<?php

namespace App\Filament\Resources\EsportMatches\Pages;

use App\Filament\Resources\EsportMatches\EsportMatchResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditEsportMatch extends EditRecord
{
    protected static string $resource = EsportMatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
