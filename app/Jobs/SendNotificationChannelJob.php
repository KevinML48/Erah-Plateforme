<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Services\PushNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendNotificationChannelJob implements ShouldQueue
{
    use Queueable;

    // This job is processused asynchronously and requires an active queue worker in production.
    public function __construct(
        public readonly int $notificationId,
        public readonly string $channel
    ) {
    }

    public function handle(PushNotificationService $pushNotificationService): void
    {
        $notification = Notification::query()->find($this->notificationId);
        if (! $notification) {
            return;
        }

        if ($this->channel === 'push') {
            $pushNotificationService->sendNotification($notification);

            return;
        }

        Log::info('notifications.channel.stub.sent', [
            'notification_id' => $notification->id,
            'user_id' => $notification->user_id,
            'channel' => $this->channel,
            'category' => $notification->category,
        ]);
    }
}
