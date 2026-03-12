<?php

namespace Tests\Feature\Web;

use App\Models\AuditLog;
use App\Models\ClubReview;
use App\Models\EsportMatch;
use App\Models\Gift;
use App\Models\GiftRedemption;
use App\Models\ShopItem;
use App\Models\User;
use App\Models\UserPurchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOperationsCockpitTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_render_cockpit_and_load_live_payload(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Cockpit operations')
            ->assertSee('Flux activite admin');

        $this->actingAs($admin)
            ->getJson(route('admin.dashboard.live'))
            ->assertOk()
            ->assertJsonStructure([
                'kpis',
                'pending',
                'alerts',
                'feed_items',
                'alerts_html',
                'pending_html',
                'feed_rows_html',
            ]);
    }

    public function test_non_admin_cannot_access_cockpit_or_quick_actions(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $gift = Gift::query()->create([
            'title' => 'Poster ERAH',
            'description' => 'Poster',
            'cost_points' => 1000,
            'stock' => 4,
            'is_active' => true,
        ]);

        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($user)->getJson(route('admin.dashboard.live'))->assertForbidden();
        $this->actingAs($user)->post(route('admin.operations.gifts.stock', $gift->id), [
            'stock' => 9,
        ])->assertForbidden();
    }

    public function test_live_feed_can_be_filtered_by_module(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $member = User::factory()->create(['role' => User::ROLE_USER]);

        $shopItem = ShopItem::query()->create([
            'key' => 'boost-pack',
            'name' => 'Boost Pack',
            'description' => 'Pack',
            'type' => 'boost',
            'cost_points' => 500,
            'stock' => 12,
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 0,
        ]);

        $purchase = UserPurchase::query()->create([
            'shop_item_id' => $shopItem->id,
            'user_id' => $member->id,
            'cost_points' => 500,
            'status' => 'completed',
            'payload' => null,
            'purchased_at' => now(),
        ]);

        $match = EsportMatch::factory()->create([
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
            'status' => EsportMatch::STATUS_FINISHED,
            'result' => EsportMatch::RESULT_TEAM_A,
        ]);

        AuditLog::query()->create([
            'actor_type' => User::class,
            'actor_id' => $admin->id,
            'action' => 'shop.purchase.completed',
            'target_type' => UserPurchase::class,
            'target_id' => $purchase->id,
            'context' => ['shop_item_key' => $shopItem->key],
            'created_at' => now()->subSecond(),
        ]);

        AuditLog::query()->create([
            'actor_type' => User::class,
            'actor_id' => $admin->id,
            'action' => 'matches.settled',
            'target_type' => EsportMatch::class,
            'target_id' => $match->id,
            'context' => ['match_id' => $match->id],
            'created_at' => now(),
        ]);

        $payload = $this->actingAs($admin)
            ->getJson(route('admin.dashboard.live', [
                'feed_module' => 'shop',
                'feed_type' => 'shop.purchase.completed',
            ]))
            ->assertOk()
            ->json('feed_items');

        $this->assertNotEmpty($payload);
        $this->assertTrue(collect($payload)->every(fn (array $item): bool => $item['module_key'] === 'shop'));
        $this->assertTrue(collect($payload)->every(fn (array $item): bool => $item['type_key'] === 'shop.purchase.completed'));
    }

    public function test_quick_actions_update_stock_and_status_with_audit_logs(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $gift = Gift::query()->create([
            'title' => 'Casquette ERAH',
            'description' => 'Casquette',
            'cost_points' => 1200,
            'stock' => 2,
            'is_active' => true,
        ]);

        $shopItem = ShopItem::query()->create([
            'key' => 'badge-pack',
            'name' => 'Badge Pack',
            'description' => 'Badges',
            'type' => 'badge',
            'cost_points' => 300,
            'stock' => 3,
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 0,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.operations.gifts.stock', $gift->id), ['stock' => 7])
            ->assertRedirect();
        $this->assertDatabaseHas('gifts', ['id' => $gift->id, 'stock' => 7]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'gifts.stock.updated', 'target_id' => $gift->id]);

        $this->actingAs($admin)
            ->post(route('admin.operations.gifts.status', $gift->id), ['is_active' => 0])
            ->assertRedirect();
        $this->assertDatabaseHas('gifts', ['id' => $gift->id, 'is_active' => 0]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'gifts.deactivated', 'target_id' => $gift->id]);

        $this->actingAs($admin)
            ->post(route('admin.operations.shop-items.stock', $shopItem->id), ['stock' => 9])
            ->assertRedirect();
        $this->assertDatabaseHas('shop_items', ['id' => $shopItem->id, 'stock' => 9]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'shop.items.stock.updated', 'target_id' => $shopItem->id]);

        $this->actingAs($admin)
            ->post(route('admin.operations.shop-items.status', $shopItem->id), ['is_active' => 0])
            ->assertRedirect();
        $this->assertDatabaseHas('shop_items', ['id' => $shopItem->id, 'is_active' => 0]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'shop.items.deactivated', 'target_id' => $shopItem->id]);
    }

    public function test_shipping_action_stores_tracking_carrier_and_note(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $member = User::factory()->create(['role' => User::ROLE_USER]);

        $gift = Gift::query()->create([
            'title' => 'T-shirt ERAH',
            'description' => 'T-shirt',
            'cost_points' => 1800,
            'stock' => 6,
            'is_active' => true,
        ]);

        $redemption = GiftRedemption::query()->create([
            'user_id' => $member->id,
            'gift_id' => $gift->id,
            'cost_points_snapshot' => 1800,
            'status' => GiftRedemption::STATUS_APPROVED,
            'requested_at' => now()->subDay(),
            'approved_at' => now()->subHours(12),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.redemptions.ship', $redemption->id), [
                'tracking_code' => 'TRK-123',
                'tracking_carrier' => 'Chronopost',
                'shipping_note' => 'Expedition prioritaire',
            ])->assertRedirect();

        $this->assertDatabaseHas('gift_redemptions', [
            'id' => $redemption->id,
            'status' => GiftRedemption::STATUS_SHIPPED,
            'tracking_code' => 'TRK-123',
            'tracking_carrier' => 'Chronopost',
            'shipping_note' => 'Expedition prioritaire',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'gift.redeem.ship',
            'target_id' => $redemption->id,
        ]);
    }

    public function test_review_events_are_logged_for_member_and_admin_actions(): void
    {
        $member = User::factory()->create(['role' => User::ROLE_USER]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($member)
            ->post(route('profile.reviews.store'), [
                'content' => 'Plateforme tres claire, missions efficaces et progression motivante au quotidien.',
            ])
            ->assertRedirect();

        $review = ClubReview::query()->where('user_id', $member->id)->firstOrFail();

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'reviews.created',
            'target_type' => ClubReview::class,
            'target_id' => $review->id,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.reviews.update', $review->id), [
                'status' => ClubReview::STATUS_HIDDEN,
                'is_featured' => 0,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'reviews.moderated',
            'target_type' => ClubReview::class,
            'target_id' => $review->id,
        ]);
    }
}

