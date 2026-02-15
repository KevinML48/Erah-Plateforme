<?php

declare(strict_types=1);

namespace App\Filament\Resources\Missions\Tables;

use App\Models\Mission;
use App\Services\AdminAuditService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount('progresses'))
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('title')->searchable(),
                TextColumn::make('slug')->searchable(),
                TextColumn::make('points_reward')->numeric()->sortable(),
                TextColumn::make('recurrence')->badge(),
                TextColumn::make('completion_rule')->badge(),
                IconColumn::make('is_active')->boolean(),
                TextColumn::make('progresses_count')->label('Progressions')->numeric(),
                TextColumn::make('starts_at')->dateTime()->placeholder('-'),
                TextColumn::make('ends_at')->dateTime()->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('recurrence')->options([
                    'ONE_TIME' => 'ONE_TIME',
                    'DAILY' => 'DAILY',
                    'WEEKLY' => 'WEEKLY',
                    'MONTHLY' => 'MONTHLY',
                ]),
                SelectFilter::make('is_active')->options([
                    '1' => 'Active',
                    '0' => 'Inactive',
                ]),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('toggleActive')
                    ->label(fn (Mission $record): string => $record->is_active ? 'Disable' : 'Enable')
                    ->requiresConfirmation()
                    ->action(function (Mission $record): void {
                        $record->is_active = !$record->is_active;
                        $record->save();

                        app(AdminAuditService::class)->log(
                            actor: auth()->user(),
                            action: 'mission.toggle_active',
                            entityType: 'mission',
                            entityId: (int) $record->id,
                            metadata: ['is_active' => (bool) $record->is_active]
                        );
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
