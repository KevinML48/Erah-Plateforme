<?php

declare(strict_types=1);

namespace App\Filament\Resources\AdminAuditLogs;

use App\Filament\Resources\AdminAuditLogs\Pages\ListAdminAuditLogs;
use App\Filament\Resources\AdminAuditLogs\Pages\ViewAdminAuditLog;
use App\Filament\Resources\AdminAuditLogs\Schemas\AdminAuditLogInfolist;
use App\Filament\Resources\AdminAuditLogs\Tables\AdminAuditLogsTable;
use App\Models\AdminAuditLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdminAuditLogResource extends Resource
{
    protected static ?string $model = AdminAuditLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $recordTitleAttribute = 'action';

    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 90;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('audit.view') || auth()->user()?->isAdmin() || false;
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
        return AdminAuditLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdminAuditLogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdminAuditLogs::route('/'),
            'view' => ViewAdminAuditLog::route('/{record}'),
        ];
    }
}
