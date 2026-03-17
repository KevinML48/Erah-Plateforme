<?php

namespace Tests\Feature\Notifications;

use App\Application\Actions\Notifications\NotifyAction;
use App\Domain\Notifications\Enums\NotificationCategory;
use App\Jobs\SendNotificationChannelJob;
use App\Models\NotificationPreference;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\UserNotificationChannel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class NotifyActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_in_app_notification_is_always_created_even_when_global_channels_are_opted_out(): void
    {
        $user = User::factory()->create();

        UserNotificationChannel::query()->create([
            'user_id' => $user->id,
            'email_opt_in' => false,
            'push_opt_in' => false,
        ]);

        NotificationPreference::query()->create([
            'user_id' => $user->id,
            'category' => NotificationCategory::SYSTEM->value,
            'email_enabled' => false,
            'push_enabled' => false,
        ]);

        Queue::fake();

        app(NotifyAction::class)->execute(
            user: $user,
            category: NotificationCategory::SYSTEM->value,
            message: 'In-app should exist',
            title: 'System',
        );

        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'category' => NotificationCategory::SYSTEM->value,
            'message' => 'In-app should exist',
        ]);

        Queue::assertNothingPushed();
    }

    public function test_notify_action_respects_global_and_category_preferences_for_channels(): void
    {
        $user = User::factory()->create();

        UserNotificationChannel::query()->create([
            'user_id' => $user->id,
            'email_opt_in' => true,
            'push_opt_in' => true,
        ]);

        NotificationPreference::query()->create([
            'user_id' => $user->id,
            'category' => NotificationCategory::DUEL->value,
            'email_enabled' => false,
            'push_enabled' => true,
        ]);

        UserDevice::query()->create([
            'user_id' => $user->id,
            'platform' => 'web',
            'device_token' => 'web-token-12345678',
            'is_active' => true,
            'last_seen_at' => now(),
        ]);

        Queue::fake();

        app(NotifyAction::class)->execute(
            user: $user,
            category: NotificationCategory::DUEL->value,
            message: 'Duel invite',
            title: 'Duel',
        );

        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'category' => NotificationCategory::DUEL->value,
        ]);

        Queue::assertPushed(SendNotificationChannelJob::class, function (SendNotificationChannelJob $job) {
            return $job->channel === 'push';
        });

        Queue::assertNotPushed(SendNotificationChannelJob::class, function (SendNotificationChannelJob $job) {
            return $job->channel === 'email';
        });
    }

    public function test_notify_action_queues_email_channel_when_allowed(): void
    {
        $user = User::factory()->create();

        UserNotificationChannel::query()->create([
            'user_id' => $user->id,
            'email_opt_in' => true,
            'push_opt_in' => false,
        ]);

        NotificationPreference::query()->create([
            'user_id' => $user->id,
            'category' => NotificationCategory::SYSTEM->value,
            'email_enabled' => true,
            'push_enabled' => false,
        ]);

        Queue::fake();

        app(NotifyAction::class)->execute(
            user: $user,
            category: NotificationCategory::SYSTEM->value,
            message: 'Email delivery test',
            title: 'System',
        );

        Queue::assertPushed(SendNotificationChannelJob::class, function (SendNotificationChannelJob $job) {
            return $job->channel === 'email';
        });
    }
}
