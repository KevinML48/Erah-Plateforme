<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')->required()->maxLength(255)->unique(ignoreRecord: true),
                Select::make('type')
                    ->required()
                    ->options([
                        'json' => 'json',
                        'string' => 'string',
                        'number' => 'number',
                        'boolean' => 'boolean',
                    ])
                    ->default('json'),
                KeyValue::make('value')->required()->columnSpanFull(),
                Textarea::make('description')->rows(3)->columnSpanFull(),
            ]);
    }
}
