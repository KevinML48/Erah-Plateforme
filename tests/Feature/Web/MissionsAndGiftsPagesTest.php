<?php

namespace Tests\Feature\Web;

use App\Models\Gift;
use App\Models\GiftRedemption;
use App\Models\GiftRedemptionEvent;
use App\Models\MissionTemplate;
use App\Models\RewardWalletTransaction;
use App\Models\User;
use App\Models\UserRewardWallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MissionsAndGiftsPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_missions_and_gifts_pages(): void
    {
        $user = User::factory()->create();

        MissionTemplate::query()->create([
            'key' => 'daily_clip_comments',
            'title' => 'Commenter 3 clips',
            'description' => 'Ajoute 3 commentaires.',
            'event_type' => 'clip_comment',
            'target_count' => 3,
            'scope' => MissionTemplate::SCOPE_DAILY,
            'constraints' => null,
            'rewards' => ['xp' => 50, 'points' => 100],
            'is_active' => true,
        ]);

        Gift::query()->create([
            'title' => 'Mug ERAH',
            'description' => 'Mug test',
            'image_url' => null,
            'cost_points' => 600,
            'stock' => 20,
            'is_active' => true,
        ]);

        UserRewardWallet::query()->create([
            'user_id' => $user->id,
            'balance' => 900,
        ]);

        $this->actingAs($user)->get(route('missions.index'))
            ->assertOk()
            ->assertSee('Missions');

        $this->actingAs($user)->get(route('gifts.index'))
            ->assertOk()
            ->assertSee('Catalogue cadeaux')
            ->assertSee('Filtrer');
    }

    public function test_gift_wallet_route_redirects_to_unified_points_wallet(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('gifts.wallet'))
            ->assertRedirect(route('wallet.index'));
    }

    public function test_user_can_add_mission_to_focus_and_see_it_on_profile(): void
    {
        $user = User::factory()->create();

        $template = MissionTemplate::query()->create([
            'key' => 'mission.focus.profile',
            'title' => 'Mission focus profil',
            'short_description' => 'Visible sur les deux pages.',
            'description' => 'Visible sur les deux pages.',
            'event_type' => 'login.daily',
            'target_count' => 1,
            'scope' => MissionTemplate::SCOPE_DAILY,
            'is_active' => true,
            'rewards' => ['xp' => 25, 'points' => 15],
        ]);

        app(\App\Application\Actions\Rewards\EnsureCurrentMissionInstancesAction::class)->execute($user);

        $this->actingAs($user)
            ->post(route('missions.focus.store', $template))
            ->assertRedirect();

        $this->actingAs($user)
            ->get(route('missions.index'))
            ->assertOk()
            ->assertSee('Mes 3 missions en focus')
            ->assertSee('Mission focus profil');

        $this->actingAs($user)
            ->get(route('profile.show'))
            ->assertOk()
            ->assertSee('Mes priorites du moment')
            ->assertSee('Mission focus profil');
    }

    public function test_user_can_filter_mission_board(): void
    {
        $user = User::factory()->create();

        MissionTemplate::query()->create([
            'key' => 'mission.filter.repeatable',
            'title' => 'Mission repeatable',
            'short_description' => 'Visible si filtre repeatable.',
            'event_type' => 'login.daily',
            'target_count' => 1,
            'scope' => MissionTemplate::SCOPE_DAILY,
            'type' => 'repeatable',
            'difficulty' => 'simple',
            'rewards' => ['xp' => 15, 'points' => 10],
            'is_active' => true,
        ]);

        MissionTemplate::query()->create([
            'key' => 'mission.filter.event',
            'title' => 'Mission event',
            'short_description' => 'Visible si filtre event.',
            'event_type' => 'clip.share',
            'target_count' => 1,
            'scope' => MissionTemplate::SCOPE_EVENT_WINDOW,
            'type' => 'event',
            'difficulty' => 'special',
            'start_at' => now()->subHour(),
            'end_at' => now()->addHour(),
            'rewards' => ['xp' => 50, 'points' => 25],
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get(route('missions.index', ['type' => 'event']))
            ->assertOk()
            ->assertSee('Mission event')
            ->assertSee('Filtrer');
    }

    public function test_user_can_view_gift_detail_page(): void
    {
        $user = User::factory()->create();

        $gift = Gift::query()->create([
            'title' => 'Sticker Pack ERAH',
            'description' => 'Pack officiel de stickers.',
            'image_url' => null,
            'cost_points' => 180,
            'stock' => 40,
            'is_active' => true,
        ]);

        UserRewardWallet::query()->create([
            'user_id' => $user->id,
            'balance' => 400,
        ]);

        $this->actingAs($user)->get(route('gifts.show', $gift->id))
            ->assertOk()
            ->assertSee('Sticker Pack ERAH')
            ->assertSee('Demander ce cadeau')
            ->assertSee('Mes demandes recentes');
    }

    public function test_user_can_view_gift_redemption_detail_with_timeline_and_history(): void
    {
        $user = User::factory()->create();

        $gift = Gift::query()->create([
            'title' => 'Casquette ERAH',
            'description' => 'Edition club',
            'image_url' => null,
            'cost_points' => 450,
            'stock' => 12,
            'is_active' => true,
        ]);

        $redemption = GiftRedemption::query()->create([
            'user_id' => $user->id,
            'gift_id' => $gift->id,
            'cost_points_snapshot' => 450,
            'status' => GiftRedemption::STATUS_SHIPPED,
            'tracking_code' => 'TRK-901',
            'tracking_carrier' => 'Chronopost',
            'shipping_note' => 'Laisse en point relais si absent',
            'requested_at' => now()->subDays(2),
            'approved_at' => now()->subDay(),
            'shipped_at' => now()->subHours(10),
        ]);

        GiftRedemptionEvent::query()->create([
            'redemption_id' => $redemption->id,
            'actor_user_id' => $user->id,
            'type' => 'redeem_requested',
            'data' => ['cost_points' => 450],
            'created_at' => now()->subDays(2),
        ]);

        GiftRedemptionEvent::query()->create([
            'redemption_id' => $redemption->id,
            'actor_user_id' => null,
            'type' => 'admin_shipped',
            'data' => [
                'tracking_code' => 'TRK-901',
                'tracking_carrier' => 'Chronopost',
                'shipping_note' => 'Laisse en point relais si absent',
            ],
            'created_at' => now()->subHours(10),
        ]);

        $response = $this->actingAs($user)->get(route('gifts.redemptions.show', $redemption->id));

        $response->assertOk()
            ->assertSee('CMD-'.str_pad((string) $redemption->id, 6, '0', STR_PAD_LEFT))
            ->assertSee('Timeline commande')
            ->assertSee('Commande expediee')
            ->assertSee('Historique complet')
            ->assertSee('TRK-901')
            ->assertSee('Chronopost');
    }

    public function test_user_can_see_only_its_own_gift_redemption_detail(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $gift = Gift::query()->create([
            'title' => 'Mug Collector',
            'description' => 'Edition limitee',
            'image_url' => null,
            'cost_points' => 300,
            'stock' => 3,
            'is_active' => true,
        ]);

        $otherRedemption = GiftRedemption::query()->create([
            'user_id' => $otherUser->id,
            'gift_id' => $gift->id,
            'cost_points_snapshot' => 300,
            'status' => GiftRedemption::STATUS_PENDING,
            'requested_at' => now(),
        ]);

        $this->actingAs($owner)
            ->get(route('gifts.redemptions.show', $otherRedemption->id))
            ->assertNotFound();
    }

    public function test_user_can_view_redemptions_list_with_filters_and_detail_links(): void
    {
        $user = User::factory()->create();

        $gift = Gift::query()->create([
            'title' => 'Pack Stickers',
            'description' => 'Pack logo',
            'image_url' => null,
            'cost_points' => 120,
            'stock' => 50,
            'is_active' => true,
        ]);

        $redemption = GiftRedemption::query()->create([
            'user_id' => $user->id,
            'gift_id' => $gift->id,
            'cost_points_snapshot' => 120,
            'status' => GiftRedemption::STATUS_PENDING,
            'requested_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('gifts.redemptions', [
            'status' => GiftRedemption::STATUS_PENDING,
            'search' => 'Pack',
        ]));

        $response->assertOk()
            ->assertSee('Mes commandes cadeaux')
            ->assertSee('Pack Stickers')
            ->assertSee('CMD-'.str_pad((string) $redemption->id, 6, '0', STR_PAD_LEFT))
            ->assertSee(route('gifts.redemptions.show', $redemption->id), false);
    }

    public function test_user_can_redeem_gift_once_with_idempotent_replay(): void
    {
        $user = User::factory()->create();

        $gift = Gift::query()->create([
            'title' => 'T-shirt ERAH',
            'description' => 'T-shirt test',
            'image_url' => null,
            'cost_points' => 1000,
            'stock' => 10,
            'is_active' => true,
        ]);

        UserRewardWallet::query()->create([
            'user_id' => $user->id,
            'balance' => 1500,
        ]);

        $idempotencyKey = 'test-redeem-gift-001';

        $first = $this->actingAs($user)->post(route('gifts.redeem', $gift->id), [
            'idempotency_key' => $idempotencyKey,
        ]);

        $first->assertRedirect();
        $first->assertSessionHas('success');

        $second = $this->actingAs($user)->post(route('gifts.redeem', $gift->id), [
            'idempotency_key' => $idempotencyKey,
        ]);

        $second->assertRedirect();
        $second->assertSessionHas('success');

        $this->assertSame(1, GiftRedemption::query()->where('user_id', $user->id)->where('gift_id', $gift->id)->count());
        $this->assertSame(1, RewardWalletTransaction::query()->where('user_id', $user->id)->where('unique_key', 'gift.redeem.cost.'.$idempotencyKey)->count());

        $this->assertDatabaseHas('gift_redemptions', [
            'user_id' => $user->id,
            'gift_id' => $gift->id,
            'status' => GiftRedemption::STATUS_PENDING,
            'cost_points_snapshot' => 1000,
        ]);

        $this->assertDatabaseHas('user_reward_wallets', [
            'user_id' => $user->id,
            'balance' => 500,
        ]);

        $this->assertDatabaseHas('gifts', [
            'id' => $gift->id,
            'stock' => 9,
        ]);
    }

    public function test_redeem_fails_when_reward_wallet_is_insufficient(): void
    {
        $user = User::factory()->create();

        $gift = Gift::query()->create([
            'title' => 'Ticket event',
            'description' => 'Ticket test',
            'image_url' => null,
            'cost_points' => 2000,
            'stock' => 5,
            'is_active' => true,
        ]);

        UserRewardWallet::query()->create([
            'user_id' => $user->id,
            'balance' => 300,
        ]);

        $response = $this->actingAs($user)->post(route('gifts.redeem', $gift->id), [
            'idempotency_key' => 'test-redeem-insufficient-001',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Solde insuffisant: il vous manque des points pour valider cette demande.');

        $this->assertDatabaseMissing('gift_redemptions', [
            'user_id' => $user->id,
            'gift_id' => $gift->id,
        ]);

        $this->assertDatabaseHas('gifts', [
            'id' => $gift->id,
            'stock' => 5,
        ]);
    }
}
