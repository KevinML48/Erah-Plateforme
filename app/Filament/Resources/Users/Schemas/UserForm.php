<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('email')->label('Email address')->email()->required()->maxLength(255),
                TextInput::make('password')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn ($state): bool => filled($state))
                    ->minLength(8),
                TextInput::make('points_balance')->required()->numeric()->minValue(0)->default(0),
                Select::make('rank_id')->relationship('rank', 'name')->label('Rank')->searchable()->preload(),
                Select::make('status')
                    ->options([
                        UserStatus::Active->value => 'Active',
                        UserStatus::Suspended->value => 'Suspended',
                        UserStatus::Banned->value => 'Banned',
                    ])
                    ->required()
                    ->default(UserStatus::Active->value),
                Toggle::make('is_admin')->label('Legacy admin flag')->default(false),
                Select::make('role')->options(UserRole::class)->required()->default(UserRole::User->value),
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->visible(fn (): bool => auth()->user()?->hasRole('super_admin') ?? false),
            ]);
    }
}
