<?php

namespace Tests\Feature\Web;

use App\Models\Gift;
use App\Models\GiftRedemption;
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
            'rewards' => [
                'xp_amount' => 50,
                'rank_points_amount' => 0,
                'reward_points_amount' => 100,
                'bet_points_amount' => 0,
            ],
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
            ->assertSee('Cadeaux');
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
        $response->assertSessionHas('error');

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

