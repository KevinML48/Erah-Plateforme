<?php

declare(strict_types=1);

namespace App\Filament\Resources\RewardRedemptions;

use App\Filament\Resources\RewardRedemptions\Pages\ListRewardRedemptions;
use App\Filament\Resources\RewardRedemptions\Pages\ViewRewardRedemption;
use App\Filament\Resources\RewardRedemptions\Schemas\RewardRedemptionInfolist;
use App\Filament\Resources\RewardRedemptions\Tables\RewardRedemptionsTable;
use App\Models\RewardRedemption;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RewardRedemptionResource extends Resource
{
    protected static ?string $model = RewardRedemption::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static ?string $recordTitleAttribute = 'reward_name_snapshot';

    protected static string|\UnitEnum|null $navigationGroup = 'Rewards';

    protected static ?int $navigationSort = 40;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('redemptions.manage') || auth()->user()?->isAdmin() || false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return RewardRedemptionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RewardRedemptionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRewardRedemptions::route('/'),
            'view' => ViewRewardRedemption::route('/{record}'),
        ];
    }
}
