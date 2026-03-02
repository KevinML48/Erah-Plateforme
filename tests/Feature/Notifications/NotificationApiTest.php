<?php

namespace Tests\Feature\Notifications;

use App\Application\Actions\Notifications\NotifyAction;
use App\Domain\Notifications\Enums\NotificationCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_and_update_notification_preferences(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $getResponse = $this->getJson('/api/me/notification-preferences');
        $getResponse->assertOk()
            ->assertJsonPath('channels.email_opt_in', true)
            ->assertJsonPath('categories.duel.email_enabled', true);

        $putResponse = $this->putJson('/api/me/notification-preferences', [
            'channels' => [
                'email_opt_in' => false,
                'push_opt_in' => true,
            ],
            'categories' => [
                'duel' => [
                    'email_enabled' => false,
                    'push_enabled' => true,
                ],
            ],
        ]);

        $putResponse->assertOk()
            ->assertJsonPath('channels.email_opt_in', false)
            ->assertJsonPath('channels.push_opt_in', true)
            ->assertJsonPath('categories.duel.email_enabled', false)
            ->assertJsonPath('categories.duel.push_enabled', true);
    }

    public function test_user_can_register_device_idempotently(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = [
            'platform' => 'web',
            'device_token' => 'token-12345678',
            'device_name' => 'Chrome',
            'meta' => ['app_version' => '0.1.0'],
        ];

        $first = $this->postJson('/api/me/devices', $payload);
        $first->assertCreated()
            ->assertJsonPath('data.platform', 'web')
            ->assertJsonPath('data.device_token', 'token-12345678');

        $second = $this->postJson('/api/me/devices', $payload);
        $second->assertOk();

        $this->assertDatabaseCount('user_devices', 1);
    }

    public function test_user_can_list_notifications_and_mark_one_as_read(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $notifyAction = app(NotifyAction::class);
        $first = $notifyAction->execute(
            user: $user,
            category: NotificationCategory::SYSTEM->value,
            message: 'System update',
            title: 'System',
        );

        $notifyAction->execute(
            user: $user,
            category: NotificationCategory::MATCH->value,
            message: 'Match ready',
            title: 'Match',
        );

        $list = $this->getJson('/api/notifications?limit=10');
        $list->assertOk()
            ->assertJsonCount(2, 'data');

        $read = $this->postJson('/api/notifications/'.$first->id.'/read');
        $read->assertOk()
            ->assertJsonPath('data.id', $first->id);

        $unread = $this->getJson('/api/notifications?unread=1');
        $unread->assertOk()
            ->assertJsonCount(1, 'data');
    }
}
