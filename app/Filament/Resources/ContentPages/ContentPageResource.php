<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContentPages;

use App\Filament\Resources\ContentPages\Pages\CreateContentPage;
use App\Filament\Resources\ContentPages\Pages\EditContentPage;
use App\Filament\Resources\ContentPages\Pages\ListContentPages;
use App\Filament\Resources\ContentPages\Pages\ViewContentPage;
use App\Filament\Resources\ContentPages\Schemas\ContentPageForm;
use App\Filament\Resources\ContentPages\Schemas\ContentPageInfolist;
use App\Filament\Resources\ContentPages\Tables\ContentPagesTable;
use App\Models\ContentPage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContentPageResource extends Resource
{
    protected static ?string $model = ContentPage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 70;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('content.manage') || auth()->user()?->isAdmin() || false;
    }

    public static function form(Schema $schema): Schema
    {
        return ContentPageForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ContentPageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContentPagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContentPages::route('/'),
            'create' => CreateContentPage::route('/create'),
            'view' => ViewContentPage::route('/{record}'),
            'edit' => EditContentPage::route('/{record}/edit'),
        ];
    }
}
