<?php

namespace Tests\Feature\Web;

use App\Domain\Notifications\Enums\NotificationCategory;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_notifications_index_groups_unread_and_read_items(): void
    {
        $user = User::factory()->create();

        Notification::query()->create([
            'user_id' => $user->id,
            'category' => NotificationCategory::SYSTEM->value,
            'title' => 'Nouvelle recompense',
            'message' => 'Une recompense vous attend.',
            'data' => [],
            'read_at' => null,
        ]);

        Notification::query()->create([
            'user_id' => $user->id,
            'category' => NotificationCategory::EVENT->value,
            'title' => 'Invitation acceptee',
            'message' => 'Votre demande a bien ete acceptee.',
            'data' => [],
            'read_at' => now(),
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('notifications.index'));

        $response->assertOk();
        $response->assertSeeText('A traiter en priorite');
        $response->assertSeeText('Historique recent');
        $response->assertSeeText('Sur cette page');
        $response->assertSeeText('Nouvelle recompense');
        $response->assertSeeText('Invitation acceptee');
    }
}