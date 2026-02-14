<?php

declare(strict_types=1);

namespace App\Filament\Resources\RewardRedemptions\Tables;

use App\Enums\RewardRedemptionStatus;
use App\Exceptions\RedemptionAlreadyProcessedException;
use App\Exceptions\RedemptionNotAllowedException;
use App\Models\RewardRedemption;
use App\Services\RedemptionService;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RewardRedemptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['user:id,name,email', 'reward:id,name']))
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('reward.name')->label('Reward')->searchable(),
                TextColumn::make('user.name')->label('User')->searchable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('points_cost_snapshot')->label('Cost')->numeric()->sortable(),
                IconColumn::make('debited_points')->boolean(),
                IconColumn::make('refunded_points')->boolean(),
                TextColumn::make('tracking_code')->searchable()->placeholder('-'),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    RewardRedemptionStatus::Pending->value => 'Pending',
                    RewardRedemptionStatus::Approved->value => 'Approved',
                    RewardRedemptionStatus::Rejected->value => 'Rejected',
                    RewardRedemptionStatus::Shipped->value => 'Shipped',
                    RewardRedemptionStatus::Cancelled->value => 'Cancelled',
                ]),
                SelectFilter::make('reward')->relationship('reward', 'name')->searchable()->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('approve')
                    ->requiresConfirmation()
                    ->visible(fn (RewardRedemption $record): bool => $record->status === RewardRedemptionStatus::Pending)
                    ->action(function (RewardRedemption $record): void {
                        /** @var RedemptionService $service */
                        $service = app(RedemptionService::class);

                        try {
                            $service->approveRedemption(auth()->user(), $record);
                            Notification::make()->title('Redemption approved.')->success()->send();
                        } catch (RedemptionNotAllowedException|RedemptionAlreadyProcessedException $exception) {
                            Notification::make()->title($exception->getMessage())->danger()->send();
                        }
                    }),
                Action::make('reject')
                    ->requiresConfirmation()
                    ->visible(fn (RewardRedemption $record): bool => $record->status === RewardRedemptionStatus::Pending)
                    ->form([
                        Textarea::make('note')->label('Admin note')->maxLength(500),
                    ])
                    ->action(function (RewardRedemption $record, array $data): void {
                        /** @var RedemptionService $service */
                        $service = app(RedemptionService::class);

                        try {
                            $service->rejectRedemption(auth()->user(), $record, $data['note'] ?? null);
                            Notification::make()->title('Redemption rejected.')->success()->send();
                        } catch (RedemptionNotAllowedException|RedemptionAlreadyProcessedException $exception) {
                            Notification::make()->title($exception->getMessage())->danger()->send();
                        }
                    }),
                Action::make('ship')
                    ->requiresConfirmation()
                    ->visible(fn (RewardRedemption $record): bool => $record->status === RewardRedemptionStatus::Approved)
                    ->form([
                        TextInput::make('tracking_code')->maxLength(255),
                    ])
                    ->action(function (RewardRedemption $record, array $data): void {
                        /** @var RedemptionService $service */
                        $service = app(RedemptionService::class);

                        try {
                            $service->markShipped(auth()->user(), $record, $data['tracking_code'] ?? null);
                            Notification::make()->title('Redemption shipped.')->success()->send();
                        } catch (RedemptionNotAllowedException $exception) {
                            Notification::make()->title($exception->getMessage())->danger()->send();
                        }
                    }),
                Action::make('cancel')
                    ->requiresConfirmation()
                    ->visible(fn (RewardRedemption $record): bool => $record->status === RewardRedemptionStatus::Pending)
                    ->action(function (RewardRedemption $record): void {
                        /** @var RedemptionService $service */
                        $service = app(RedemptionService::class);

                        try {
                            $service->cancelRedemption($record->user()->firstOrFail(), $record);
                            Notification::make()->title('Redemption cancelled and refunded.')->success()->send();
                        } catch (RedemptionNotAllowedException|RedemptionAlreadyProcessedException $exception) {
                            Notification::make()->title($exception->getMessage())->danger()->send();
                        }
                    }),
            ])
            ->toolbarActions([]);
    }
}
