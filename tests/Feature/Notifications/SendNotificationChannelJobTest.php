<?php

namespace Tests\Feature\Notifications;

use App\Jobs\SendNotificationChannelJob;
use App\Models\Notification as PlatformNotification;
use App\Models\User;
use App\Notifications\UserPlatformNotificationMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendNotificationChannelJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_channel_sends_a_real_laravel_notification(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'member@erah.test',
        ]);

        $notification = PlatformNotification::query()->create([
            'user_id' => $user->id,
            'category' => 'system',
            'title' => 'Cadeau approuve',
            'message' => 'Ta demande de cadeau est approuvee.',
            'data' => [
                'redemption_id' => 42,
            ],
        ]);

        $job = new SendNotificationChannelJob($notification->id, 'email');
        $job->handle(app(\App\Services\PushNotificationService::class));

        Notification::assertSentTo($user, UserPlatformNotificationMail::class);
    }
}