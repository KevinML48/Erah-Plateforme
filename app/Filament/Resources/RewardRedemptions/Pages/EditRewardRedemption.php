<?php

namespace App\Filament\Resources\RewardRedemptions\Pages;

use App\Filament\Resources\RewardRedemptions\RewardRedemptionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditRewardRedemption extends EditRecord
{
    protected static string $resource = RewardRedemptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
