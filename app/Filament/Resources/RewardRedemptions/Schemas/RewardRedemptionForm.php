<?php

namespace App\Filament\Resources\RewardRedemptions\Schemas;

use App\Enums\RewardRedemptionStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RewardRedemptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('reward_id')
                    ->relationship('reward', 'name')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('status')
                    ->options(RewardRedemptionStatus::class)
                    ->default('PENDING')
                    ->required(),
                TextInput::make('points_cost_snapshot')
                    ->required()
                    ->numeric(),
                TextInput::make('reward_name_snapshot')
                    ->required(),
                TextInput::make('shipping_name')
                    ->default(null),
                TextInput::make('shipping_email')
                    ->email()
                    ->default(null),
                TextInput::make('shipping_phone')
                    ->tel()
                    ->default(null),
                TextInput::make('shipping_address1')
                    ->default(null),
                TextInput::make('shipping_address2')
                    ->default(null),
                TextInput::make('shipping_city')
                    ->default(null),
                TextInput::make('shipping_postal_code')
                    ->default(null),
                TextInput::make('shipping_country')
                    ->default(null),
                Textarea::make('admin_note')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('tracking_code')
                    ->default(null),
                Toggle::make('debited_points')
                    ->required(),
                Toggle::make('refunded_points')
                    ->required(),
                Toggle::make('reserved_stock')
                    ->required(),
                DateTimePicker::make('approved_at'),
                DateTimePicker::make('shipped_at'),
                DateTimePicker::make('cancelled_at'),
            ]);
    }
}
