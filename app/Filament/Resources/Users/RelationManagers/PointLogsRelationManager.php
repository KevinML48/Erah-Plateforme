<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PointLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'pointLogs';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->columns([
                TextColumn::make('amount')->numeric()->sortable(),
                TextColumn::make('type')->badge(),
                TextColumn::make('description')->limit(50)->placeholder('-'),
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
