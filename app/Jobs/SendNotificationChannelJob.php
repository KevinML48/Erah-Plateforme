<?php

namespace App\Jobs;

use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendNotificationChannelJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly int $notificationId,
        public readonly string $channel
    ) {
    }

    public function handle(): void
    {
        $notification = Notification::query()->find($this->notificationId);
        if (! $notification) {
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
