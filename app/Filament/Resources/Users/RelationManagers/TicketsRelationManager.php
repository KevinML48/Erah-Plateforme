<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TicketsRelationManager extends RelationManager
{
    protected static string $relationship = 'tickets';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('match.title')->label('Match')->searchable()->placeholder('-'),
                TextColumn::make('status')->badge(),
                TextColumn::make('stake_points')->numeric(),
                TextColumn::make('payout_points')->numeric(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->recordActions([
                ViewAction::make(),
            ])
            ->headerActions([])
            ->toolbarActions([]);
    }
}
