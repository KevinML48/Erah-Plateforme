<?php

namespace App\Filament\Resources\EsportMatches\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EsportMatchInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('game_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('game'),
                TextEntry::make('title'),
                TextEntry::make('format')
                    ->placeholder('-'),
                TextEntry::make('starts_at')
                    ->dateTime(),
                TextEntry::make('lock_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('result')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('result_json')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('points_reward')
                    ->numeric(),
                TextEntry::make('predictions_locked_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('completed_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_by')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
