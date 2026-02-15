<?php

declare(strict_types=1);

namespace App\Filament\Resources\MissionProgress\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MissionProgressTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['mission:id,title,slug', 'user:id,name,email']))
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('mission.title')->label('Mission')->searchable(),
                TextColumn::make('user.name')->label('User')->searchable(),
                TextColumn::make('period_key')->searchable(),
                TextColumn::make('progress_json.progress_percent')
                    ->label('Progress %')
                    ->formatStateUsing(fn ($state): string => ((int) $state).'%'),
                IconColumn::make('awarded_points')->boolean(),
                TextColumn::make('completed_at')->dateTime()->placeholder('-'),
                TextColumn::make('awarded_at')->dateTime()->placeholder('-'),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('mission')->relationship('mission', 'title')->searchable()->preload(),
                SelectFilter::make('user')->relationship('user', 'name')->searchable()->preload(),
                SelectFilter::make('awarded_points')->options([
                    '1' => 'Awarded',
                    '0' => 'Not awarded',
                ]),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
