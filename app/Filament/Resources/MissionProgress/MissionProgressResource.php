<?php

declare(strict_types=1);

namespace App\Filament\Resources\MissionProgress;

use App\Filament\Resources\MissionProgress\Pages\ListMissionProgress;
use App\Filament\Resources\MissionProgress\Tables\MissionProgressTable;
use App\Models\MissionProgress;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MissionProgressResource extends Resource
{
    protected static ?string $model = MissionProgress::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    protected static string|\UnitEnum|null $navigationGroup = 'Gamification';

    protected static ?int $navigationSort = 37;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('missions.view_progress')
            || auth()->user()?->can('missions.manage')
            || auth()->user()?->isAdmin()
            || false;
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

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return MissionProgressTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMissionProgress::route('/'),
        ];
    }
}
