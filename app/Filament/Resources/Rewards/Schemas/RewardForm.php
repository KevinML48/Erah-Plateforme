<?php

declare(strict_types=1);

namespace App\Filament\Resources\Rewards\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RewardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('slug')->required()->alphaDash()->maxLength(255)->unique(ignoreRecord: true),
                Textarea::make('description')->rows(4)->columnSpanFull(),
                TextInput::make('points_cost')->required()->integer()->minValue(1)->maxValue(1000000),
                TextInput::make('stock')->integer()->nullable()->minValue(0)->helperText('Leave empty for unlimited stock.'),
                Toggle::make('is_active')->required()->default(true),
                TextInput::make('image_url')->url()->maxLength(2048),
                DateTimePicker::make('starts_at'),
                DateTimePicker::make('ends_at')->afterOrEqual('starts_at'),
            ]);
    }
}
