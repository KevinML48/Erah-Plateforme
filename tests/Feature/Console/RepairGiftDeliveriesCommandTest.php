<?php

namespace Tests\Feature\Console;

use App\Models\Gift;
use App\Models\GiftRedemption;
use App\Models\GiftRedemptionEvent;
use App\Models\User;
use Database\Seeders\LaunchGiftCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepairGiftDeliveriesCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_repairs_pending_profile_digital_redemption_without_duplicate_events(): void
    {
        $this->seed(LaunchGiftCatalogSeeder::class);

        $user = User::factory()->create();
        $gift = Gift::query()->where('key', 'launch-profile-badge-exclusive')->firstOrFail();

        $redemption = GiftRedemption::query()->create([
            'user_id' => $user->id,
            'gift_id' => $gift->id,
            'cost_points_snapshot' => $gift->cost_points,
            'status' => GiftRedemption::STATUS_PENDING,
            'requested_at' => now()->subMinute(),
        ]);

        $this->artisan('erah:repair-gift-deliveries', [
            '--user-id' => $user->id,
        ])->assertExitCode(0);

        $redemption->refresh();
        $user->refresh();

        $this->assertSame(GiftRedemption::STATUS_DELIVERED, $redemption->status);
        $this->assertDatabaseHas('user_profile_cosmetics', [
            'user_id' => $user->id,
            'gift_redemption_id' => $redemption->id,
            'cosmetic_key' => 'launch_badge_exclusive',
        ]);
        $this->assertSame('launch_badge_exclusive', $user->equipped_profile_badge);
        $this->assertSame(1, GiftRedemptionEvent::query()->where('redemption_id', $redemption->id)->where('type', 'auto_delivered')->count());
        $this->assertSame(1, GiftRedemptionEvent::query()->where('redemption_id', $redemption->id)->where('type', 'profile_unlock_granted')->count());

        $this->artisan('erah:repair-gift-deliveries', [
            '--user-id' => $user->id,
        ])->assertExitCode(0);

        $this->assertSame(1, GiftRedemptionEvent::query()->where('redemption_id', $redemption->id)->where('type', 'auto_delivered')->count());
        $this->assertSame(1, GiftRedemptionEvent::query()->where('redemption_id', $redemption->id)->where('type', 'profile_unlock_granted')->count());
    }
}