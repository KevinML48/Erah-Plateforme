<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContentPages\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ContentPageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('slug'),
                TextEntry::make('title'),
                TextEntry::make('body')->html()->columnSpanFull(),
                IconEntry::make('is_active')->boolean(),
                TextEntry::make('updated_at')->dateTime(),
            ]);
    }
}
