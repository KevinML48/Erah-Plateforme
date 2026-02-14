<?php

declare(strict_types=1);

namespace App\Filament\Resources\Announcements\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AnnouncementInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title'),
                TextEntry::make('body')->html()->columnSpanFull(),
                IconEntry::make('is_active')->boolean(),
                TextEntry::make('starts_at')->dateTime()->placeholder('-'),
                TextEntry::make('ends_at')->dateTime()->placeholder('-'),
                TextEntry::make('updated_at')->dateTime(),
            ]);
    }
}
