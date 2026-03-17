<?php

namespace Tests\Feature\Web;

use App\Domain\Notifications\Enums\NotificationCategory;
use App\Models\NotificationPreference;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\UserNotificationChannel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationPreferencesPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_preferences_page_displays_only_all_on_and_all_off_actions(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('notifications.preferences'))
            ->assertOk()
            ->assertSee('Actions rapides')
            ->assertSee('Tout activer')
            ->assertSee('Tout desactiver')
                ->assertDontSee('Tout activer Email')
                ->assertDontSee('Tout desactiver Email')
                ->assertDontSee('Tout activer Push')
                ->assertDontSee('Tout desactiver Push')
                ->assertDontSee('Reglages recommandes')
                ->assertDontSee('Activer seulement l essentiel');
    }

    public function test_all_enable_payload_persists_all_channels_when_push_device_exists(): void
    {
        $user = $this->createUserWithActiveDevice();

        $this->actingAs($user)
            ->post(route('notifications.preferences.update'), $this->payload(true, true, NotificationCategory::values(), NotificationCategory::values()))
            ->assertRedirect(route('notifications.preferences'));

        $this->assertStoredState($user, true, true, NotificationCategory::values(), NotificationCategory::values());
    }

    public function test_all_disable_payload_persists_all_channels_off(): void
    {
        $user = $this->createUserWithActiveDevice();

        $this->actingAs($user)
            ->post(route('notifications.preferences.update'), $this->payload(false, false, [], []))
            ->assertRedirect(route('notifications.preferences'));

        $this->assertStoredState($user, false, false, [], []);
    }

    public function test_push_preferences_are_forced_off_without_active_device(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('notifications.preferences'))
            ->assertOk()
            ->assertSee('Aucun device actif detecte');

        $this->actingAs($user)
            ->post(route('notifications.preferences.update'), $this->payload(true, true, NotificationCategory::values(), NotificationCategory::values()))
            ->assertRedirect(route('notifications.preferences'));

        $this->assertStoredState($user, true, false, NotificationCategory::values(), []);
    }

    /**
     * @param array<int, string> $emailCategories
     * @param array<int, string> $pushCategories
     * @return array<string, int>
     */
    private function payload(bool $emailOptIn, bool $pushOptIn, array $emailCategories, array $pushCategories): array
    {
        $payload = [
            'email_opt_in' => $emailOptIn ? 1 : 0,
            'push_opt_in' => $pushOptIn ? 1 : 0,
        ];

        foreach (NotificationCategory::values() as $category) {
            $payload[$category.'_email'] = in_array($category, $emailCategories, true) ? 1 : 0;
            $payload[$category.'_push'] = in_array($category, $pushCategories, true) ? 1 : 0;
        }

        return $payload;
    }

    /**
     * @param array<int, string> $expectedEmailCategories
     * @param array<int, string> $expectedPushCategories
     */
    private function assertStoredState(User $user, bool $expectedEmailOptIn, bool $expectedPushOptIn, array $expectedEmailCategories, array $expectedPushCategories): void
    {
        $channels = UserNotificationChannel::query()->where('user_id', $user->id)->firstOrFail();

        self::assertSame($expectedEmailOptIn, $channels->email_opt_in);
        self::assertSame($expectedPushOptIn, $channels->push_opt_in);

        foreach (NotificationCategory::values() as $category) {
            $preference = NotificationPreference::query()
                ->where('user_id', $user->id)
                ->where('category', $category)
                ->firstOrFail();

            self::assertSame(in_array($category, $expectedEmailCategories, true), $preference->email_enabled, 'Unexpected email state for '.$category);
            self::assertSame(in_array($category, $expectedPushCategories, true), $preference->push_enabled, 'Unexpected push state for '.$category);
        }
    }

    private function createUserWithActiveDevice(): User
    {
        $user = User::factory()->create();

        UserDevice::query()->create([
            'user_id' => $user->id,
            'platform' => 'web',
            'device_token' => 'token-'.$user->id,
            'device_name' => 'Chrome',
            'is_active' => true,
        ]);

        return $user;
    }
}