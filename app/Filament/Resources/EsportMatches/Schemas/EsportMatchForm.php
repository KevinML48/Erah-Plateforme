<?php

declare(strict_types=1);

namespace App\Filament\Resources\EsportMatches\Schemas;

use App\Enums\MatchResult;
use App\Enums\MatchStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class EsportMatchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('game')->required()->maxLength(50),
                TextInput::make('title')->required()->maxLength(255),
                TextInput::make('format')->maxLength(20)->default('BO3'),
                DateTimePicker::make('starts_at')->required(),
                DateTimePicker::make('lock_at'),
                Select::make('status')->options(MatchStatus::class)->required()->default(MatchStatus::Draft->value),
                Select::make('result')->options(MatchResult::class),
                TextInput::make('points_reward')->required()->integer()->minValue(1)->default(100),
                Textarea::make('result_json')->rows(3)->columnSpanFull(),
            ]);
    }
}
