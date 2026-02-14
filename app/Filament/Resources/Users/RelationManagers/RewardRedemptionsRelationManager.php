<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RewardRedemptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'rewardRedemptions';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('reward_name_snapshot')
            ->columns([
                TextColumn::make('reward_name_snapshot')->label('Reward')->searchable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('points_cost_snapshot')->label('Cost')->numeric(),
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
