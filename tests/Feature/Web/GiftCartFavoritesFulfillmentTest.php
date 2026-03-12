<?php

namespace Tests\Feature\Web;

use App\Models\Gift;
use App\Models\GiftCartItem;
use App\Models\GiftFavorite;
use App\Models\GiftRedemption;
use App\Models\RewardWalletTransaction;
use App\Models\User;
use App\Models\UserRewardWallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GiftCartFavoritesFulfillmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_to_cart_and_compute_total_points(): void
    {
        $user = User::factory()->create();

        $giftA = Gift::query()->create([
            'title' => 'Sticker Pack',
            'description' => 'Stickers',
            'cost_points' => 150,
            'stock' => 10,
            'is_active' => true,
        ]);
        $giftB = Gift::query()->create([
            'title' => 'Mug ERAH',
            'description' => 'Mug',
            'cost_points' => 300,
            'stock' => 8,
            'is_active' => true,
        ]);

        UserRewardWallet::query()->create([
            'user_id' => $user->id,
            'balance' => 3000,
        ]);

        $this->actingAs($user)->post(route('gifts.cart.add', $giftA->id), ['quantity' => 2])->assertRedirect();
        $this->actingAs($user)->post(route('gifts.cart.add', $giftB->id), ['quantity' => 1])->assertRedirect();

        $this->assertDatabaseHas('gift_cart_items', [
            'user_id' => $user->id,
            'gift_id' => $giftA->id,
            'quantity' => 2,
        ]);
        $this->assertDatabaseHas('gift_cart_items', [
            'user_id' => $user->id,
            'gift_id' => $giftB->id,
            'quantity' => 1,
        ]);

        $this->actingAs($user)
            ->get(route('gifts.cart'))
            ->assertOk()
            ->assertSee('Panier cadeaux')
            ->assertSee('Sticker Pack')
            ->assertSee('Mug ERAH')
            ->assertSee('600 pts');
    }

    public function test_user_can_remove_item_from_cart(): void
    {
        $user = User::factory()->create();
        $gift = Gift::query()->create([
            'title' => 'Casquette',
            'description' => 'Casquette',
            'cost_points' => 450,
            'stock' => 4,
            'is_active' => true,
        ]);

        $item = GiftCartItem::query()->create([
            'user_id' => $user->id,
            'gift_id' => $gift->id,
            'quantity' => 1,
            'added_at' => now(),
        ]);

        $this->actingAs($user)
            ->delete(route('gifts.cart.remove', $item->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('gift_cart_items', [
            'id' => $item->id,
        ]);
    }

    public function test_checkout_multi_gift_creates_redemptions_and_debits_wallet(): void
    {
        $user = User::factory()->create();

        $giftA = Gift::query()->create([
            'title' => 'Poster',
            'description' => 'Poster',
            'cost_points' => 200,
            'stock' => 10,
            'is_active' => true,
        ]);
        $giftB = Gift::query()->create([
            'title' => 'T-Shirt',
            'description' => 'T-Shirt',
            'cost_points' => 300,
            'stock' => 10,
            'is_active' => true,
        ]);

        UserRewardWallet::query()->create([
            'user_id' => $user->id,
            'balance' => 1500,
        ]);

        $this->actingAs($user)->post(route('gifts.cart.add', $giftA->id), ['quantity' => 2])->assertRedirect();
        $this->actingAs($user)->post(route('gifts.cart.add', $giftB->id), ['quantity' => 1])->assertRedirect();

        $this->actingAs($user)
            ->post(route('gifts.cart.checkout'), ['idempotency_key' => 'checkout-multi-0001'])
            ->assertRedirect(route('gifts.redemptions'));

        $this->assertSame(
            3,
            GiftRedemption::query()->where('user_id', $user->id)->count()
        );
        $this->assertDatabaseHas('user_reward_wallets', [
            'user_id' => $user->id,
            'balance' => 800,
        ]);
        $this->assertDatabaseHas('gifts', [
            'id' => $giftA->id,
            'stock' => 8,
        ]);
        $this->assertDatabaseHas('gifts', [
            'id' => $giftB->id,
            'stock' => 9,
        ]);
        $this->assertDatabaseHas('reward_wallet_transactions', [
            'user_id' => $user->id,
            'unique_key' => 'gift.cart.checkout.total.checkout-multi-0001',
            'type' => RewardWalletTransaction::TYPE_GIFT_PURCHASE,
        ]);
        $this->assertDatabaseCount('gift_cart_items', 0);
    }

    public function test_checkout_fails_if_wallet_balance_is_insufficient(): void
    {
        $user = User::factory()->create();
        $gift = Gift::query()->create([
            'title' => 'Sweat',
            'description' => 'Sweat',
            'cost_points' => 1200,
            'stock' => 5,
            'is_active' => true,
        ]);

        UserRewardWallet::query()->create([
            'user_id' => $user->id,
            'balance' => 300,
        ]);

        $this->actingAs($user)->post(route('gifts.cart.add', $gift->id), ['quantity' => 1])->assertRedirect();

        $response = $this->actingAs($user)
            ->post(route('gifts.cart.checkout'), ['idempotency_key' => 'checkout-balance-ko-001']);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Solde insuffisant: votre panier depasse votre reserve de points.');

        $this->assertDatabaseHas('gift_cart_items', [
            'user_id' => $user->id,
            'gift_id' => $gift->id,
            'quantity' => 1,
        ]);
        $this->assertDatabaseMissing('gift_redemptions', [
            'user_id' => $user->id,
            'gift_id' => $gift->id,
        ]);
    }

    public function test_checkout_fails_if_stock_becomes_insufficient(): void
    {
        $user = User::factory()->create();
        $gift = Gift::query()->create([
            'title' => 'Hoodie',
            'description' => 'Hoodie',
            'cost_points' => 500,
            'stock' => 1,
            'is_active' => true,
        ]);

        UserRewardWallet::query()->create([
            'user_id' => $user->id,
            'balance' => 2000,
        ]);

        $this->actingAs($user)->post(route('gifts.cart.add', $gift->id), ['quantity' => 1])->assertRedirect();
        $gift->update(['stock' => 0]);

        $response = $this->actingAs($user)
            ->post(route('gifts.cart.checkout'), ['idempotency_key' => 'checkout-stock-ko-001']);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Stock insuffisant sur un des cadeaux du panier. Ajustez les quantites puis reessayez.');

        $this->assertDatabaseMissing('gift_redemptions', [
            'user_id' => $user->id,
            'gift_id' => $gift->id,
        ]);
        $this->assertDatabaseMissing('reward_wallet_transactions', [
            'user_id' => $user->id,
            'unique_key' => 'gift.cart.checkout.total.checkout-stock-ko-001',
        ]);
    }

    public function test_user_can_add_and_remove_favorites_and_see_favorites_block(): void
    {
        $user = User::factory()->create();

        $gift = Gift::query()->create([
            'title' => 'Badge collector',
            'description' => 'Badge collector',
            'cost_points' => 90,
            'stock' => 15,
            'is_active' => true,
        ]);

        UserRewardWallet::query()->create([
            'user_id' => $user->id,
            'balance' => 500,
        ]);

        $this->actingAs($user)
            ->post(route('gifts.favorites.toggle', $gift->id))
            ->assertRedirect();

        $this->assertDatabaseHas('gift_favorites', [
            'user_id' => $user->id,
            'gift_id' => $gift->id,
        ]);

        $this->actingAs($user)
            ->get(route('gifts.index'))
            ->assertOk()
            ->assertSee('Mes favoris')
            ->assertSee('Badge collector');

        $this->actingAs($user)
            ->post(route('gifts.favorites.toggle', $gift->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('gift_favorites', [
            'user_id' => $user->id,
            'gift_id' => $gift->id,
        ]);
    }

    public function test_user_only_sees_own_cart_and_favorites(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $giftA = Gift::query()->create([
            'title' => 'Gift A',
            'description' => 'A',
            'cost_points' => 100,
            'stock' => 10,
            'is_active' => true,
        ]);
        $giftB = Gift::query()->create([
            'title' => 'Gift B',
            'description' => 'B',
            'cost_points' => 120,
            'stock' => 10,
            'is_active' => true,
        ]);

        GiftCartItem::query()->create([
            'user_id' => $userA->id,
            'gift_id' => $giftA->id,
            'quantity' => 1,
            'added_at' => now(),
        ]);
        GiftCartItem::query()->create([
            'user_id' => $userB->id,
            'gift_id' => $giftB->id,
            'quantity' => 1,
            'added_at' => now(),
        ]);

        GiftFavorite::query()->create([
            'user_id' => $userA->id,
            'gift_id' => $giftA->id,
        ]);
        GiftFavorite::query()->create([
            'user_id' => $userB->id,
            'gift_id' => $giftB->id,
        ]);

        $this->actingAs($userA)
            ->get(route('gifts.cart'))
            ->assertOk()
            ->assertSee('Gift A')
            ->assertDontSee('Gift B');

        $this->actingAs($userA)
            ->get(route('gifts.favorites'))
            ->assertOk()
            ->assertSee('Gift A')
            ->assertDontSee('Gift B');
    }

    public function test_admin_can_process_pending_to_approved_to_shipped_to_delivered(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $member = User::factory()->create(['role' => User::ROLE_USER]);

        $gift = Gift::query()->create([
            'title' => 'Echarpe',
            'description' => 'Echarpe',
            'cost_points' => 500,
            'stock' => 20,
            'is_active' => true,
        ]);

        $redemption = GiftRedemption::query()->create([
            'user_id' => $member->id,
            'gift_id' => $gift->id,
            'cost_points_snapshot' => 500,
            'status' => GiftRedemption::STATUS_PENDING,
            'requested_at' => now()->subHour(),
        ]);

        $this->actingAs($admin)->post(route('admin.redemptions.approve', $redemption->id))->assertRedirect();
        $this->actingAs($admin)->post(route('admin.redemptions.ship', $redemption->id), [
            'tracking_code' => 'SHIP-001',
            'tracking_carrier' => 'Chronopost',
            'shipping_note' => 'Depart entrepot',
        ])->assertRedirect();
        $this->actingAs($admin)->post(route('admin.redemptions.deliver', $redemption->id))->assertRedirect();

        $this->assertDatabaseHas('gift_redemptions', [
            'id' => $redemption->id,
            'status' => GiftRedemption::STATUS_DELIVERED,
            'tracking_code' => 'SHIP-001',
            'tracking_carrier' => 'Chronopost',
        ]);
        $this->assertDatabaseHas('gift_redemption_events', ['redemption_id' => $redemption->id, 'type' => 'admin_approved']);
        $this->assertDatabaseHas('gift_redemption_events', ['redemption_id' => $redemption->id, 'type' => 'admin_shipped']);
        $this->assertDatabaseHas('gift_redemption_events', ['redemption_id' => $redemption->id, 'type' => 'admin_delivered']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'gift.redeem.approve', 'target_id' => $redemption->id]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'gift.redeem.ship', 'target_id' => $redemption->id]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'gift.redeem.deliver', 'target_id' => $redemption->id]);
    }

    public function test_admin_reject_requires_reason_and_logs_refund(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $member = User::factory()->create(['role' => User::ROLE_USER]);

        $gift = Gift::query()->create([
            'title' => 'Patch textile',
            'description' => 'Patch',
            'cost_points' => 320,
            'stock' => 0,
            'is_active' => true,
        ]);

        $redemption = GiftRedemption::query()->create([
            'user_id' => $member->id,
            'gift_id' => $gift->id,
            'cost_points_snapshot' => 320,
            'status' => GiftRedemption::STATUS_PENDING,
            'requested_at' => now()->subHour(),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.redemptions.reject', $redemption->id), ['reason' => ''])
            ->assertSessionHasErrors('reason');

        $this->actingAs($admin)
            ->post(route('admin.redemptions.reject', $redemption->id), ['reason' => 'Adresse invalide'])
            ->assertRedirect();

        $this->assertDatabaseHas('gift_redemptions', [
            'id' => $redemption->id,
            'status' => GiftRedemption::STATUS_REJECTED,
            'reason' => 'Adresse invalide',
        ]);
        $this->assertDatabaseHas('reward_wallet_transactions', [
            'user_id' => $member->id,
            'unique_key' => 'gift.redeem.refund.redemption.'.$redemption->id,
            'type' => RewardWalletTransaction::TYPE_REDEEM_REFUND,
            'amount' => 320,
        ]);
        $this->assertDatabaseHas('gifts', [
            'id' => $gift->id,
            'stock' => 1,
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'gift.redeem.reject',
            'target_id' => $redemption->id,
        ]);
    }

    public function test_admin_can_add_internal_note_and_refund_with_audit_log(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $member = User::factory()->create(['role' => User::ROLE_USER]);

        $gift = Gift::query()->create([
            'title' => 'Pack premium',
            'description' => 'Pack',
            'cost_points' => 650,
            'stock' => 5,
            'is_active' => true,
        ]);

        $redemption = GiftRedemption::query()->create([
            'user_id' => $member->id,
            'gift_id' => $gift->id,
            'cost_points_snapshot' => 650,
            'status' => GiftRedemption::STATUS_SHIPPED,
            'requested_at' => now()->subDays(2),
            'approved_at' => now()->subDays(2),
            'shipped_at' => now()->subDay(),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.redemptions.note', $redemption->id), [
                'internal_note' => 'Controle adresse et historique client effectue.',
            ])->assertRedirect();

        $this->actingAs($admin)
            ->post(route('admin.redemptions.refund', $redemption->id), [
                'reason' => 'Remboursement SAV',
            ])->assertRedirect();

        $this->assertDatabaseHas('gift_redemptions', [
            'id' => $redemption->id,
            'internal_note' => 'Controle adresse et historique client effectue.',
            'status' => GiftRedemption::STATUS_REFUNDED,
        ]);
        $this->assertDatabaseHas('reward_wallet_transactions', [
            'user_id' => $member->id,
            'unique_key' => 'gift.redeem.refund.redemption.'.$redemption->id,
            'type' => RewardWalletTransaction::TYPE_REDEEM_REFUND,
            'amount' => 650,
        ]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'gift.redeem.note', 'target_id' => $redemption->id]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'gift.redeem.refund', 'target_id' => $redemption->id]);
    }

    public function test_non_admin_cannot_access_admin_redemption_detail(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $gift = Gift::query()->create([
            'title' => 'Carte cadeau',
            'description' => 'Carte',
            'cost_points' => 100,
            'stock' => 10,
            'is_active' => true,
        ]);
        $redemption = GiftRedemption::query()->create([
            'user_id' => $user->id,
            'gift_id' => $gift->id,
            'cost_points_snapshot' => 100,
            'status' => GiftRedemption::STATUS_PENDING,
            'requested_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('admin.redemptions.show', $redemption->id))
            ->assertForbidden();
    }

    public function test_admin_can_view_gift_console_index(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        Gift::query()->create([
            'title' => 'Porte cle',
            'description' => 'Porte cle',
            'cost_points' => 80,
            'stock' => 12,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.gifts.index'))
            ->assertOk()
            ->assertSee('Console fulfilment cadeaux')
            ->assertSee('Centre de traitement commandes cadeaux');
    }

    public function test_admin_can_view_redemption_detail_page(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $member = User::factory()->create(['role' => User::ROLE_USER]);
        $gift = Gift::query()->create([
            'title' => 'Carte collector',
            'description' => 'Carte',
            'cost_points' => 210,
            'stock' => 2,
            'is_active' => true,
        ]);
        $redemption = GiftRedemption::query()->create([
            'user_id' => $member->id,
            'gift_id' => $gift->id,
            'cost_points_snapshot' => 210,
            'status' => GiftRedemption::STATUS_PENDING,
            'requested_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.redemptions.show', $redemption->id))
            ->assertOk()
            ->assertSee('CMD-'.str_pad((string) $redemption->id, 6, '0', STR_PAD_LEFT))
            ->assertSee('Actions fulfillment');
    }
}
