<?php

declare(strict_types=1);

namespace App\Filament\Resources\Missions;

use App\Filament\Resources\Missions\Pages\CreateMission;
use App\Filament\Resources\Missions\Pages\EditMission;
use App\Filament\Resources\Missions\Pages\ListMissions;
use App\Filament\Resources\Missions\RelationManagers\StepsRelationManager;
use App\Filament\Resources\Missions\Schemas\MissionForm;
use App\Filament\Resources\Missions\Tables\MissionsTable;
use App\Models\Mission;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MissionResource extends Resource
{
    protected static ?string $model = Mission::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;

    protected static string|\UnitEnum|null $navigationGroup = 'Gamification';

    protected static ?int $navigationSort = 35;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('missions.manage') || auth()->user()?->isAdmin() || false;
    }

    public static function form(Schema $schema): Schema
    {
        return MissionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MissionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            StepsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMissions::route('/'),
            'create' => CreateMission::route('/create'),
            'edit' => EditMission::route('/{record}/edit'),
        ];
    }
}
