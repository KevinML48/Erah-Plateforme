<?php

declare(strict_types=1);

namespace App\Filament\Resources\AdminAuditLogs\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AdminAuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('actor:id,name,email'))
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('actor.name')->label('Actor')->placeholder('-')->searchable(),
                TextColumn::make('action')->searchable(),
                TextColumn::make('entity_type')->searchable(),
                TextColumn::make('entity_id')->numeric()->placeholder('-'),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('actor')->relationship('actor', 'name')->searchable()->preload(),
                Filter::make('action')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('value'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when(
                            isset($data['value']) && $data['value'] !== '',
                            fn ($q) => $q->where('action', 'like', '%'.(string) $data['value'].'%')
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
