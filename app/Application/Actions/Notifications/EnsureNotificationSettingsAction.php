<?php

namespace App\Application\Actions\Notifications;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Domain\Notifications\Enums\NotificationCategory;
use App\Models\NotificationPreference;
use App\Models\User;
use App\Models\UserNotificationChannel;
use Illuminate\Support\Facades\DB;

class EnsureNotificationSettingsAction
{
    public function __construct(
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    public function execute(User $user): void
    {
        DB::transaction(function () use ($user) {
            $channels = UserNotificationChannel::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (! $channels) {
                UserNotificationChannel::query()->create([
                    'user_id' => $user->id,
                    'email_opt_in' => true,
                    'push_opt_in' => true,
                ]);

                $this->storeAuditLogAction->execute(
                    action: 'notifications.channels.initialized',
                    actor: $user,
                    target: $user,
                    context: [],
                );
            }

            foreach (NotificationCategory::values() as $category) {
                NotificationPreference::query()->firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'category' => $category,
                    ],
                    [
                        'email_enabled' => true,
                        'push_enabled' => true,
                    ]
                );
            }
        });
    }
}
