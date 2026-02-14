<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SettingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('key'),
                TextEntry::make('type')->badge(),
                KeyValueEntry::make('value')->columnSpanFull(),
                TextEntry::make('description')->placeholder('-')->columnSpanFull(),
                TextEntry::make('updated_at')->dateTime(),
            ]);
    }
}
