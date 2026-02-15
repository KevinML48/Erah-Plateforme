<?php

declare(strict_types=1);

namespace App\Filament\Resources\Missions\Schemas;

use App\Enums\MissionClaimType;
use App\Enums\MissionCompletionRule;
use App\Enums\MissionRecurrence;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->required()->maxLength(255),
                TextInput::make('slug')->required()->alphaDash()->maxLength(255)->unique(ignoreRecord: true),
                Textarea::make('description')->rows(3)->columnSpanFull(),
                TextInput::make('points_reward')->required()->integer()->minValue(1)->maxValue(1000000),
                Select::make('recurrence')->required()->options(MissionRecurrence::class)->default(MissionRecurrence::Daily->value),
                Select::make('completion_rule')->required()->options(MissionCompletionRule::class)->default(MissionCompletionRule::All->value),
                TextInput::make('any_n')->integer()->minValue(1)->nullable()->helperText('Required only when completion rule is ANY_N.'),
                Select::make('claim_type')->required()->options(MissionClaimType::class)->default(MissionClaimType::Auto->value),
                DateTimePicker::make('starts_at'),
                DateTimePicker::make('ends_at')->afterOrEqual('starts_at'),
                Toggle::make('is_active')->required()->default(true),
                TextInput::make('max_claims_total')->integer()->minValue(1)->nullable(),
            ]);
    }
}
