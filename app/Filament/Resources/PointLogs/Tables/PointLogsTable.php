<?php

declare(strict_types=1);

namespace App\Filament\Resources\PointLogs\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PointLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('user:id,name,email'))
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('user.name')->label('User')->searchable(),
                TextColumn::make('amount')->numeric()->sortable(),
                TextColumn::make('type')->badge()->searchable(),
                TextColumn::make('description')->limit(50)->placeholder('-'),
                TextColumn::make('reference_type')->placeholder('-')->searchable(),
                TextColumn::make('reference_id')->numeric()->placeholder('-'),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('user')->relationship('user', 'name')->searchable()->preload(),
                Filter::make('type')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('value')->label('Type'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when(
                            isset($data['value']) && $data['value'] !== '',
                            fn ($q) => $q->where('type', 'like', '%'.(string) $data['value'].'%')
                        );
                    }),
                Filter::make('created_between')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from'),
                        \Filament\Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(isset($data['from']) && $data['from'] !== null, fn ($q) => $q->whereDate('created_at', '>=', $data['from']))
                            ->when(isset($data['until']) && $data['until'] !== null, fn ($q) => $q->whereDate('created_at', '<=', $data['until']));
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([]);
    }
}
