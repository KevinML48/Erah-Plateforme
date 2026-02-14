<?php

declare(strict_types=1);

namespace App\Filament\Resources\Rewards\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class RewardsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('creator:id,name'))
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('slug')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('points_cost')->label('Cost')->numeric()->sortable(),
                TextColumn::make('stock')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state === null ? 'Unlimited' : (string) $state),
                IconColumn::make('is_active')->boolean(),
                ImageColumn::make('image_url')->square()->size(48)->defaultImageUrl(url('/images/user/user-01.jpg')),
                TextColumn::make('creator.name')->label('Created by')->placeholder('-'),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('Active'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
