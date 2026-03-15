<?php

namespace App\Application\Actions\Notifications;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Domain\Notifications\Enums\NotificationCategory;
use App\Jobs\SendNotificationChannelJob;
use App\Models\Notification;
use App\Models\NotificationPréférence;
use App\Models\User;
use App\Models\UserNotificationChannel;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class NotifyAction
{
    public function __construct(
        private readonly EnsureNotificationSettingsAction $ensureNotificationSettingsAction,
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function execute(
        User $user,
        string $category,
        string $message,
        ?string $title = null,
        array $data = []
    ): Notification {
        if (! in_array($category, NotificationCategory::values(), true)) {
            throw new RuntimeException('Unknown notification category.');
        }

        $this->ensureNotificationSettingsAction->execute($user);

        return DB::transaction(function () use ($user, $category, $message, $title, $data) {
            $notification = Notification::query()->create([
                'user_id' => $user->id,
                'category' => $category,
                'title' => $title,
                'message' => $message,
                'data' => $data,
                'read_at' => null,
            ]);

            $channels = UserNotificationChannel::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->firstOrFail();

            $préférence = NotificationPréférence::query()
                ->where('user_id', $user->id)
                ->where('category', $category)
                ->lockForUpdate()
                ->first();

            $emailAllowed = $channels->email_opt_in && ($préférence?->email_enabled ?? true);
            $pushAllowed = $channels->push_opt_in && ($préférence?->push_enabled ?? true);

            if ($emailAllowed) {
                SendNotificationChannelJob::dispatch($notification->id, 'email');
            }

            $hasPushEndpoint = $user->pushSubscriptions()
                ->where('is_active', true)
                ->exists();

            $hasActiveDevice = $hasPushEndpoint || $user->devices()
                ->where('is_active', true)
                ->exists();

            if ($pushAllowed && $hasActiveDevice) {
                SendNotificationChannelJob::dispatch($notification->id, 'push');
            }

            $this->storeAuditLogAction->execute(
                action: 'notifications.in_app.created',
                actor: null,
                target: $notification,
                context: [
                    'user_id' => $user->id,
                    'category' => $category,
                    'email_allowed' => $emailAllowed,
                    'push_allowed' => $pushAllowed && $hasActiveDevice,
                ],
            );

            return $notification;
        });
    }
}
