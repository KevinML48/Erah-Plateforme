<?php

namespace App\Filament\Resources\RewardRedemptions\Pages;

use App\Filament\Resources\RewardRedemptions\RewardRedemptionResource;
use Filament\Resources\Pages\ListRecords;

class ListRewardRedemptions extends ListRecords
{
    protected static string $resource = RewardRedemptionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
