<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email')->label('Email address'),
                TextEntry::make('status')->badge(),
                TextEntry::make('points_balance')->numeric(),
                TextEntry::make('rank.name')->label('Rank')->placeholder('No rank'),
                TextEntry::make('roles.name')->label('Roles')->badge()->separator(','),
                IconEntry::make('is_admin')->label('Legacy admin')->boolean(),
                TextEntry::make('role')->badge(),
                TextEntry::make('created_at')->dateTime()->placeholder('-'),
                TextEntry::make('updated_at')->dateTime()->placeholder('-'),
            ]);
    }
}
