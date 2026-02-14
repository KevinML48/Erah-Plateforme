<?php

namespace App\Filament\Resources\RewardRedemptions\Pages;

use App\Filament\Resources\RewardRedemptions\RewardRedemptionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRewardRedemption extends ViewRecord
{
    protected static string $resource = RewardRedemptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
