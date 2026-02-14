<?php

declare(strict_types=1);

namespace App\Filament\Resources\EsportMatches;

use App\Filament\Resources\EsportMatches\Pages\CreateEsportMatch;
use App\Filament\Resources\EsportMatches\Pages\EditEsportMatch;
use App\Filament\Resources\EsportMatches\Pages\ListEsportMatches;
use App\Filament\Resources\EsportMatches\Pages\ViewEsportMatch;
use App\Filament\Resources\EsportMatches\Schemas\EsportMatchForm;
use App\Filament\Resources\EsportMatches\Schemas\EsportMatchInfolist;
use App\Filament\Resources\EsportMatches\Tables\EsportMatchesTable;
use App\Models\EsportMatch;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EsportMatchResource extends Resource
{
    protected static ?string $model = EsportMatch::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|\UnitEnum|null $navigationGroup = 'Matches';

    protected static ?int $navigationSort = 50;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('matches.manage') || auth()->user()?->can('settlements.manage') || auth()->user()?->isAdmin() || false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('matches.manage') || auth()->user()?->isAdmin() || false;
    }

    public static function canEdit($record): bool
    {
        return self::canCreate();
    }

    public static function canDelete($record): bool
    {
        return self::canCreate();
    }

    public static function form(Schema $schema): Schema
    {
        return EsportMatchForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EsportMatchInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EsportMatchesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEsportMatches::route('/'),
            'create' => CreateEsportMatch::route('/create'),
            'view' => ViewEsportMatch::route('/{record}'),
            'edit' => EditEsportMatch::route('/{record}/edit'),
        ];
    }
}
