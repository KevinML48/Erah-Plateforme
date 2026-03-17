<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Notifications\UserPlatformNotificationMail;
use App\Services\PushNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendNotificationChannelJob implements ShouldQueue
{
    use Queueable;

    // This job is processed asynchronously and requires an active queue worker in production.
    public function __construct(
        public readonly int $notificationId,
        public readonly string $channel
    ) {
    }

    public function handle(PushNotificationService $pushNotificationService): void
    {
        $notification = Notification::query()->with('user')->find($this->notificationId);
        if (! $notification) {
            return;
        }

        if ($this->channel === 'push') {
            $pushNotificationService->sendNotification($notification);

            return;
        }

        if ($this->channel !== 'email') {
            Log::warning('notifications.channel.unsupported', [
                'notification_id' => $notification->id,
                'user_id' => $notification->user_id,
                'channel' => $this->channel,
                'category' => $notification->category,
            ]);

            return;
        }

        $user = $notification->user;
        if (! $user || ! filled($user->email)) {
            Log::warning('notifications.email.skipped', [
                'notification_id' => $notification->id,
                'user_id' => $notification->user_id,
                'reason' => 'missing_recipient_email',
                'category' => $notification->category,
            ]);

            return;
        }

        $user->notify(new UserPlatformNotificationMail($notification));

        Log::info('notifications.email.sent', [
            'notification_id' => $notification->id,
            'user_id' => $notification->user_id,
            'channel' => $this->channel,
            'category' => $notification->category,
        ]);
    }
}
