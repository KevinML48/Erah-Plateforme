<?php

namespace Tests\Feature\Web;

use App\Models\Gift;
use App\Models\GiftRedemption;
use App\Models\RewardWalletTransaction;
use App\Models\User;
use App\Models\UserRewardWallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GiftDetailPurchaseFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_active_gift_detail_by_slug(): void
    {
        $gift = Gift::query()->create([
            'slug' => 'sticker-pack-erah',
            'title' => 'Sticker Pack ERAH',
            'description' => 'Pack officiel de stickers.',
            'cost_points' => 180,
            'stock' => 40,
            'is_active' => true,
            'metadata' => [
                'short_description' => 'Un pack collector simple a recuperer.',
            ],
        ]);

        $this->get(route('app.gifts.show', $gift->routeIdentifier()))
            ->assertOk()
            ->assertSee('Sticker Pack ERAH')
            ->assertSee('Se connecter pour acheter')
            ->assertSee('Retour vers le shop cadeaux');
    }

    public function test_missing_or_inactive_gift_detail_returns_404(): void
    {
        $inactiveGift = Gift::query()->create([
            'slug' => 'cadeau-inactif',
            'title' => 'Cadeau inactif',
            'description' => 'Invisible',
            'cost_points' => 500,
            'stock' => 8,
            'is_active' => false,
        ]);

        $this->get(route('app.gifts.show', 'gift-introuvable'))->assertNotFound();
        $this->get(route('app.gifts.show', $inactiveGift->routeIdentifier()))->assertNotFound();

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('gifts.show', $inactiveGift->routeIdentifier()))
            ->assertNotFound();
    }

    public function test_guest_cannot_redeem_gift_from_detail_flow(): void
    {
        $gift = Gift::query()->create([
            'slug' => 'casquette-erah',
            'title' => 'Casquette ERAH',
            'description' => 'Edition supporter.',
            'cost_points' => 420,
            'stock' => 5,
            'is_active' => true,
        ]);

        $this->post(route('gifts.redeem', $gift->routeIdentifier()), [
            'idempotency_key' => 'guest-blocked-gift-redeem',
        ])->assertRedirect(route('login', ['required' => 'participation']));
    }

    public function test_user_cannot_redeem_gift_without_enough_points(): void
    {
        $user = User::factory()->create();
        $gift = Gift::query()->create([
            'slug' => 'ticket-event-erah',
            'title' => 'Ticket event ERAH',
            'description' => 'Ticket premium.',
            'cost_points' => 2000,
            'stock' => 5,
            'is_active' => true,
        ]);

        UserRewardWallet::query()->create([
            'user_id' => $user->id,
            'balance' => 300,
        ]);

        $this->actingAs($user)
            ->post(route('gifts.redeem', $gift->routeIdentifier()), [
                'idempotency_key' => 'gift-detail-insufficient-points',
            ])
            ->assertRedirect(route('gifts.show', $gift->routeIdentifier()))
            ->assertSessionHas('error', 'Solde insuffisant: il vous manque des points pour valider cette demande.');

        $this->assertDatabaseMissing('gift_redemptions', [
            'user_id' => $user->id,
            'gift_id' => $gift->id,
        ]);
    }

    public function test_user_cannot_redeem_out_of_stock_gift(): void
    {
        $user = User::factory()->create();
        $gift = Gift::query()->create([
            'slug' => 'mug-collector-erah',
            'title' => 'Mug Collector ERAH',
            'description' => 'Derniere piece.',
            'cost_points' => 300,
            'stock' => 0,
            'is_active' => true,
        ]);

        UserRewardWallet::query()->create([
            'user_id' => $user->id,
            'balance' => 1200,
        ]);

        $this->actingAs($user)
            ->post(route('gifts.redeem', $gift->routeIdentifier()), [
                'idempotency_key' => 'gift-detail-out-of-stock',
            ])
            ->assertRedirect(route('gifts.show', $gift->routeIdentifier()))
            ->assertSessionHas('error', 'Ce cadeau est en rupture pour le moment. Revenez plus tard.');

        $this->assertDatabaseMissing('gift_redemptions', [
            'user_id' => $user->id,
            'gift_id' => $gift->id,
        ]);
    }

    public function test_user_can_complete_purchase_from_gift_detail_and_reach_order_page(): void
    {
        $user = User::factory()->create();
        $gift = Gift::query()->create([
            'slug' => 'sweat-erah',
            'title' => 'Sweat ERAH',
            'description' => 'Edition equipe.',
            'cost_points' => 1000,
            'stock' => 10,
            'is_active' => true,
            'metadata' => [
                'short_description' => 'Un sweat premium pour les membres actifs.',
                'long_description' => 'Commande test pour verifier le debit, la creation de redemption et la redirection utile.',
            ],
        ]);

        UserRewardWallet::query()->create([
            'user_id' => $user->id,
            'balance' => 1500,
        ]);

        $response = $this->actingAs($user)
            ->post(route('gifts.redeem', $gift->routeIdentifier()), [
                'idempotency_key' => 'gift-detail-success',
            ]);

        $redemption = GiftRedemption::query()
            ->where('user_id', $user->id)
            ->where('gift_id', $gift->id)
            ->firstOrFail();

        $response
            ->assertRedirect(route('gifts.redemptions.show', $redemption->id))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('gift_redemptions', [
            'id' => $redemption->id,
            'status' => GiftRedemption::STATUS_PENDING,
            'cost_points_snapshot' => 1000,
        ]);
        $this->assertDatabaseHas('reward_wallet_transactions', [
            'user_id' => $user->id,
            'unique_key' => 'gift.redeem.cost.gift-detail-success',
            'type' => RewardWalletTransaction::TYPE_GIFT_PURCHASE,
        ]);
        $this->assertDatabaseHas('user_reward_wallets', [
            'user_id' => $user->id,
            'balance' => 500,
        ]);
        $this->assertDatabaseHas('gifts', [
            'id' => $gift->id,
            'stock' => 9,
        ]);

        $this->actingAs($user)
            ->get(route('gifts.redemptions.show', $redemption->id))
            ->assertOk()
            ->assertSee('CMD-'.str_pad((string) $redemption->id, 6, '0', STR_PAD_LEFT))
            ->assertSee('Sweat ERAH');
    }

    public function test_non_repeatable_gift_is_blocked_after_first_purchase(): void
    {
        $user = User::factory()->create();
        $gift = Gift::query()->create([
            'slug' => 'box-unique-erah',
            'title' => 'Box unique ERAH',
            'description' => 'Une seule fois par compte.',
            'cost_points' => 250,
            'stock' => 10,
            'is_active' => true,
            'metadata' => [
                'is_repeatable' => false,
            ],
        ]);

        UserRewardWallet::query()->create([
            'user_id' => $user->id,
            'balance' => 2000,
        ]);

        $this->actingAs($user)
            ->post(route('gifts.redeem', $gift->routeIdentifier()), [
                'idempotency_key' => 'gift-non-repeatable-first',
            ])
            ->assertRedirect();

        $this->actingAs($user)
            ->post(route('gifts.redeem', $gift->routeIdentifier()), [
                'idempotency_key' => 'gift-non-repeatable-second',
            ])
            ->assertRedirect(route('gifts.show', $gift->routeIdentifier()))
            ->assertSessionHas('error', 'Ce cadeau ne peut etre commande qu une seule fois avec ce compte.');

        $this->assertSame(1, GiftRedemption::query()
            ->where('user_id', $user->id)
            ->where('gift_id', $gift->id)
            ->count());
    }
}