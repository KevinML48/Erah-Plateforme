<?php

declare(strict_types=1);

namespace App\Filament\Resources\PointLogs;

use App\Filament\Resources\PointLogs\Pages\ListPointLogs;
use App\Filament\Resources\PointLogs\Pages\ViewPointLog;
use App\Filament\Resources\PointLogs\Schemas\PointLogInfolist;
use App\Filament\Resources\PointLogs\Tables\PointLogsTable;
use App\Models\PointLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PointLogResource extends Resource
{
    protected static ?string $model = PointLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $recordTitleAttribute = 'type';

    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 20;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('users.view') || auth()->user()?->can('points.adjust') || auth()->user()?->isAdmin() || false;
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
        return PointLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PointLogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPointLogs::route('/'),
            'view' => ViewPointLog::route('/{record}'),
        ];
    }
}
