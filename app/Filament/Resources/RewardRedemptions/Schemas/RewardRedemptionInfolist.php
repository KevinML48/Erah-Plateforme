<?php

namespace App\Filament\Resources\RewardRedemptions\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RewardRedemptionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('reward.name')
                    ->label('Reward'),
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('points_cost_snapshot')
                    ->numeric(),
                TextEntry::make('reward_name_snapshot'),
                TextEntry::make('shipping_name')
                    ->placeholder('-'),
                TextEntry::make('shipping_email')
                    ->placeholder('-'),
                TextEntry::make('shipping_phone')
                    ->placeholder('-'),
                TextEntry::make('shipping_address1')
                    ->placeholder('-'),
                TextEntry::make('shipping_address2')
                    ->placeholder('-'),
                TextEntry::make('shipping_city')
                    ->placeholder('-'),
                TextEntry::make('shipping_postal_code')
                    ->placeholder('-'),
                TextEntry::make('shipping_country')
                    ->placeholder('-'),
                TextEntry::make('admin_note')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('tracking_code')
                    ->placeholder('-'),
                IconEntry::make('debited_points')
                    ->boolean(),
                IconEntry::make('refunded_points')
                    ->boolean(),
                IconEntry::make('reserved_stock')
                    ->boolean(),
                TextEntry::make('approved_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('shipped_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('cancelled_at')
                    ->dateTime()
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
