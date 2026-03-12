<?php

namespace Tests\Feature\Web;

use App\Models\Gift;
use App\Models\GiftRedemption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserDetailAndGiftTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_user_detail_page_with_gift_orders(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $member = User::factory()->create(['role' => User::ROLE_USER, 'name' => 'Kevin Demo']);

        $gift = Gift::query()->create([
            'title' => 'Hoodie ERAH',
            'description' => 'Hoodie demo',
            'cost_points' => 900,
            'stock' => 10,
            'is_active' => true,
        ]);

        $redemption = GiftRedemption::query()->create([
            'user_id' => $member->id,
            'gift_id' => $gift->id,
            'cost_points_snapshot' => 900,
            'status' => GiftRedemption::STATUS_PENDING,
            'requested_at' => now()->subHour(),
        ]);

        $orderNumber = 'CMD-'.str_pad((string) $redemption->id, 6, '0', STR_PAD_LEFT);

        $this->actingAs($admin)
            ->get(route('admin.users.show', $member->id))
            ->assertOk()
            ->assertSee('Detail utilisateur admin')
            ->assertSee('Kevin Demo')
            ->assertSee($orderNumber)
            ->assertSee('Commandes cadeaux');
    }

    public function test_non_admin_cannot_view_user_detail_page(): void
    {
        $member = User::factory()->create(['role' => User::ROLE_USER]);
        $other = User::factory()->create(['role' => User::ROLE_USER]);

        $this->actingAs($member)
            ->get(route('admin.users.show', $other->id))
            ->assertForbidden();
    }

    public function test_admin_gift_console_displays_in_progress_orders_section(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $member = User::factory()->create(['role' => User::ROLE_USER, 'name' => 'Follow Requester']);

        $gift = Gift::query()->create([
            'title' => 'Casquette ERAH',
            'description' => 'Casquette demo',
            'cost_points' => 350,
            'stock' => 6,
            'is_active' => true,
        ]);

        GiftRedemption::query()->create([
            'user_id' => $member->id,
            'gift_id' => $gift->id,
            'cost_points_snapshot' => 350,
            'status' => GiftRedemption::STATUS_PENDING,
            'requested_at' => now()->subMinutes(30),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.gifts.index'))
            ->assertOk()
            ->assertSee('Suivi par utilisateur (commandes en cours)')
            ->assertSee('Commandes en cours a traiter')
            ->assertSee('Follow Requester')
            ->assertSee('Centre de traitement commandes cadeaux');
    }

    public function test_admin_gift_console_user_filter_reduces_rows(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $firstUser = User::factory()->create(['role' => User::ROLE_USER, 'name' => 'Filter Target']);
        $secondUser = User::factory()->create(['role' => User::ROLE_USER, 'name' => 'Other Requester']);

        $firstGift = Gift::query()->create([
            'title' => 'Starter Pack',
            'description' => 'Pack demo',
            'cost_points' => 420,
            'stock' => 8,
            'is_active' => true,
        ]);

        $secondGift = Gift::query()->create([
            'title' => 'Mug Demo',
            'description' => 'Mug demo',
            'cost_points' => 260,
            'stock' => 8,
            'is_active' => true,
        ]);

        GiftRedemption::query()->create([
            'user_id' => $firstUser->id,
            'gift_id' => $firstGift->id,
            'cost_points_snapshot' => 420,
            'status' => GiftRedemption::STATUS_PENDING,
            'requested_at' => now()->subMinutes(20),
        ]);

        GiftRedemption::query()->create([
            'user_id' => $secondUser->id,
            'gift_id' => $secondGift->id,
            'cost_points_snapshot' => 260,
            'status' => GiftRedemption::STATUS_PENDING,
            'requested_at' => now()->subMinutes(10),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.gifts.index', ['user_id' => $firstUser->id]))
            ->assertOk()
            ->assertSee('Filter Target')
            ->assertDontSee('Other Requester')
            ->assertSee('Filtres actifs');
    }
}
