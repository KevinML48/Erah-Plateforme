<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Tables;

use App\Enums\PointTransactionType;
use App\Enums\UserStatus;
use App\Models\User;
use App\Services\AdminAuditService;
use App\Services\PointService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['rank:id,name', 'roles:id,name']))
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->label('Email')->searchable(),
                TextColumn::make('points_balance')->label('Points')->sortable(),
                TextColumn::make('rank.name')->label('Rank')->placeholder('No rank')->badge(),
                TextColumn::make('status')->badge(),
                TextColumn::make('roles.name')->label('Roles')->badge()->separator(','),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        UserStatus::Active->value => 'Active',
                        UserStatus::Suspended->value => 'Suspended',
                        UserStatus::Banned->value => 'Banned',
                    ]),
                SelectFilter::make('rank_id')->relationship('rank', 'name')->label('Rank'),
                SelectFilter::make('roles')->relationship('roles', 'name')->label('Role'),
                Filter::make('points_range')
                    ->form([
                        TextInput::make('min')->numeric()->minValue(0),
                        TextInput::make('max')->numeric()->minValue(0),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(isset($data['min']) && $data['min'] !== '', fn ($q) => $q->where('points_balance', '>=', (int) $data['min']))
                            ->when(isset($data['max']) && $data['max'] !== '', fn ($q) => $q->where('points_balance', '<=', (int) $data['max']));
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()->visible(fn () => auth()->user()?->can('users.manage') || auth()->user()?->isAdmin()),
                Action::make('adjustPoints')
                    ->label('Adjust points')
                    ->icon('heroicon-o-banknotes')
                    ->visible(fn () => auth()->user()?->can('points.adjust') || auth()->user()?->isAdmin())
                    ->form([
                        TextInput::make('amount')
                            ->integer()
                            ->required()
                            ->helperText('Use a negative value to remove points.'),
                        Textarea::make('reason')->required()->maxLength(500),
                    ])
                    ->action(function (User $record, array $data): void {
                        /** @var PointService $pointService */
                        $pointService = app(PointService::class);
                        /** @var AdminAuditService $auditService */
                        $auditService = app(AdminAuditService::class);

                        $amount = (int) $data['amount'];
                        $reason = (string) $data['reason'];

                        if ($amount === 0) {
                            Notification::make()->title('Amount must not be zero.')->danger()->send();

                            return;
                        }

                        if ($amount > 0) {
                            $pointService->addPoints(
                                user: $record,
                                amount: $amount,
                                type: PointTransactionType::AdminAdjustment->value,
                                description: $reason,
                                referenceId: (int) auth()->id(),
                                referenceType: 'admin',
                                idempotencyKey: 'filament-admin-adjust:add:'.$record->id.':'.now()->timestamp.':'.random_int(1000, 9999)
                            );
                        } else {
                            $pointService->removePoints(
                                user: $record,
                                amount: abs($amount),
                                type: PointTransactionType::AdminAdjustment->value,
                                description: $reason,
                                referenceId: (int) auth()->id(),
                                referenceType: 'admin',
                                idempotencyKey: 'filament-admin-adjust:remove:'.$record->id.':'.now()->timestamp.':'.random_int(1000, 9999)
                            );
                        }

                        $auditService->log(
                            actor: auth()->user(),
                            action: 'users.adjust_points',
                            entityType: 'user',
                            entityId: (int) $record->id,
                            metadata: ['amount' => $amount, 'reason' => $reason]
                        );

                        Notification::make()->title('Points adjusted successfully.')->success()->send();
                    }),
                Action::make('assignRole')
                    ->label('Assign role')
                    ->icon('heroicon-o-shield-check')
                    ->visible(fn () => auth()->user()?->hasRole('super_admin'))
                    ->form([
                        Select::make('role')
                            ->required()
                            ->options([
                                'super_admin' => 'super_admin',
                                'admin' => 'admin',
                                'moderator' => 'moderator',
                                'logistics' => 'logistics',
                                'analyst' => 'analyst',
                            ]),
                    ])
                    ->action(function (User $record, array $data): void {
                        /** @var AdminAuditService $auditService */
                        $auditService = app(AdminAuditService::class);

                        $record->syncRoles([(string) $data['role']]);

                        $auditService->log(
                            actor: auth()->user(),
                            action: 'users.assign_role',
                            entityType: 'user',
                            entityId: (int) $record->id,
                            metadata: ['role' => (string) $data['role']]
                        );

                        Notification::make()->title('Role updated.')->success()->send();
                    }),
                Action::make('setStatus')
                    ->label('Set status')
                    ->icon('heroicon-o-no-symbol')
                    ->visible(fn () => auth()->user()?->can('users.manage') || auth()->user()?->isAdmin())
                    ->form([
                        Select::make('status')
                            ->required()
                            ->options([
                                UserStatus::Active->value => 'Active',
                                UserStatus::Suspended->value => 'Suspended',
                                UserStatus::Banned->value => 'Banned',
                            ]),
                    ])
                    ->action(function (User $record, array $data): void {
                        /** @var AdminAuditService $auditService */
                        $auditService = app(AdminAuditService::class);

                        $record->status = (string) $data['status'];
                        $record->save();

                        $auditService->log(
                            actor: auth()->user(),
                            action: 'users.set_status',
                            entityType: 'user',
                            entityId: (int) $record->id,
                            metadata: ['status' => (string) $data['status']]
                        );

                        Notification::make()->title('Status updated.')->success()->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->visible(fn () => auth()->user()?->hasRole('super_admin')),
                ]),
            ]);
    }
}
