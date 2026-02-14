<?php

declare(strict_types=1);

namespace App\Filament\Resources\EsportMatches\Tables;

use App\Enums\MatchResult;
use App\Enums\MatchStatus;
use App\Exceptions\MatchAlreadyCompletedException;
use App\Exceptions\MatchNotOpenException;
use App\Exceptions\MatchResultMissingException;
use App\Models\EsportMatch;
use App\Services\MatchService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EsportMatchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('creator:id,name')->withCount(['predictions', 'tickets']))
            ->defaultSort('starts_at', 'desc')
            ->columns([
                TextColumn::make('title')->searchable(),
                TextColumn::make('game')->badge(),
                TextColumn::make('format')->badge()->placeholder('-'),
                TextColumn::make('status')->badge(),
                TextColumn::make('result')->badge()->placeholder('-'),
                TextColumn::make('starts_at')->dateTime()->sortable(),
                TextColumn::make('lock_at')->dateTime()->placeholder('-')->sortable(),
                TextColumn::make('predictions_count')->label('Predictions')->numeric(),
                TextColumn::make('tickets_count')->label('Tickets')->numeric(),
                TextColumn::make('points_reward')->numeric(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    MatchStatus::Draft->value => 'Draft',
                    MatchStatus::Open->value => 'Open',
                    MatchStatus::Locked->value => 'Locked',
                    MatchStatus::Live->value => 'Live',
                    MatchStatus::Completed->value => 'Completed',
                    MatchStatus::Cancelled->value => 'Cancelled',
                ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('open')
                    ->visible(fn (EsportMatch $record): bool => in_array($record->status, [MatchStatus::Draft, MatchStatus::Locked], true))
                    ->action(function (EsportMatch $record): void {
                        /** @var MatchService $service */
                        $service = app(MatchService::class);

                        try {
                            $service->openPredictions($record);
                            Notification::make()->title('Predictions opened.')->success()->send();
                        } catch (MatchAlreadyCompletedException $exception) {
                            Notification::make()->title($exception->getMessage())->danger()->send();
                        }
                    }),
                Action::make('lock')
                    ->visible(fn (EsportMatch $record): bool => $record->status === MatchStatus::Open)
                    ->action(function (EsportMatch $record): void {
                        /** @var MatchService $service */
                        $service = app(MatchService::class);

                        try {
                            $service->lockPredictions($record);
                            Notification::make()->title('Predictions locked.')->success()->send();
                        } catch (MatchAlreadyCompletedException|MatchNotOpenException $exception) {
                            Notification::make()->title($exception->getMessage())->danger()->send();
                        }
                    }),
                Action::make('live')
                    ->visible(fn (EsportMatch $record): bool => in_array($record->status, [MatchStatus::Open, MatchStatus::Locked], true))
                    ->action(function (EsportMatch $record): void {
                        /** @var MatchService $service */
                        $service = app(MatchService::class);

                        try {
                            $service->setLive($record);
                            Notification::make()->title('Match set to live.')->success()->send();
                        } catch (MatchAlreadyCompletedException $exception) {
                            Notification::make()->title($exception->getMessage())->danger()->send();
                        }
                    }),
                Action::make('complete')
                    ->visible(fn (EsportMatch $record): bool => $record->status !== MatchStatus::Completed)
                    ->form([
                        Select::make('result')
                            ->required()
                            ->options([
                                MatchResult::Win->value => 'WIN',
                                MatchResult::Lose->value => 'LOSE',
                            ]),
                    ])
                    ->requiresConfirmation()
                    ->action(function (EsportMatch $record, array $data): void {
                        /** @var MatchService $service */
                        $service = app(MatchService::class);

                        try {
                            $service->completeMatchWithResult($record, (string) $data['result']);
                            Notification::make()->title('Match completed and points awarded.')->success()->send();
                        } catch (MatchAlreadyCompletedException|MatchResultMissingException $exception) {
                            Notification::make()->title($exception->getMessage())->danger()->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
