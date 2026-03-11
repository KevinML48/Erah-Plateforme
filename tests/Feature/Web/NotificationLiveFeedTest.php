<?php

namespace Tests\Feature\Web;

use App\Application\Actions\Notifications\NotifyAction;
use App\Domain\Notifications\Enums\NotificationCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationLiveFeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_fetch_only_new_mission_notifications_for_live_toasts(): void
    {
        $user = User::factory()->create();
        $notifyAction = app(NotifyAction::class);

        $olderMission = $notifyAction->execute(
            user: $user,
            category: NotificationCategory::MISSION->value,
            title: 'Mission en progression',
            message: 'Premiere avancee',
            data: ['toast_kind' => 'progress'],
        );

        $notifyAction->execute(
            user: $user,
            category: NotificationCategory::SYSTEM->value,
            title: 'Systeme',
            message: 'Hors mission',
        );

        $newerMission = $notifyAction->execute(
            user: $user,
            category: NotificationCategory::MISSION->value,
            title: 'Mission terminee',
            message: 'Mission completee',
            data: ['toast_kind' => 'completed'],
        );

        $this->actingAs($user)
            ->getJson(route('notifications.live', [
                'category' => NotificationCategory::MISSION->value,
                'after_id' => $olderMission->id,
            ]))
            ->assertOk()
            ->assertJsonPath('meta.latest_id', $newerMission->id)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $newerMission->id)
            ->assertJsonPath('data.0.category', NotificationCategory::MISSION->value)
            ->assertJsonPath('data.0.data.toast_kind', 'completed');
    }
}
