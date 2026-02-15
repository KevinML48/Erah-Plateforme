<?php

declare(strict_types=1);

namespace App\Filament\Resources\Missions\RelationManagers;

use App\Services\AdminAuditService;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StepsRelationManager extends RelationManager
{
    protected static string $relationship = 'steps';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                TextColumn::make('order')->numeric()->sortable(),
                TextColumn::make('label')->searchable(),
                TextColumn::make('step_key')->badge()->searchable(),
                TextColumn::make('step_value')->placeholder('-')->limit(50),
                TextColumn::make('updated_at')->since(),
            ])
            ->defaultSort('order')
            ->headerActions([
                CreateAction::make()
                    ->form([
                        TextInput::make('label')->required()->maxLength(255),
                        TextInput::make('step_key')->required()->maxLength(100),
                        TextInput::make('step_value')->maxLength(1000),
                        TextInput::make('order')->numeric()->required()->default(0)->minValue(0),
                    ])
                    ->after(function ($record): void {
                        app(AdminAuditService::class)->log(
                            actor: auth()->user(),
                            action: 'mission.step.create',
                            entityType: 'mission_step',
                            entityId: (int) $record->id,
                            metadata: ['after' => $record->toArray()]
                        );
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->after(function ($record): void {
                        app(AdminAuditService::class)->log(
                            actor: auth()->user(),
                            action: 'mission.step.update',
                            entityType: 'mission_step',
                            entityId: (int) $record->id,
                            metadata: ['after' => $record->fresh()?->toArray()]
                        );
                    }),
                DeleteAction::make()
                    ->after(function ($record): void {
                        app(AdminAuditService::class)->log(
                            actor: auth()->user(),
                            action: 'mission.step.delete',
                            entityType: 'mission_step',
                            entityId: (int) $record->id,
                            metadata: []
                        );
                    }),
            ])
            ->toolbarActions([]);
    }
}
