<?php

namespace Tests\Feature\Web;

use App\Models\RewardWalletTransaction;
use App\Models\ShopItem;
use App\Models\User;
use App\Models\UserRewardWallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopPurchaseFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_shop_purchase_is_idempotent_for_same_request_key(): void
    {
        $user = User::factory()->create();

        $item = ShopItem::query()->create([
            'key' => 'shop.item.bootcamp.day',
            'name' => 'Bootcamp 1 jour',
            'description' => 'Acces bootcamp',
            'type' => 'access',
            'cost_points' => 250,
            'stock' => 5,
            'payload' => null,
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 1,
        ]);

        UserRewardWallet::query()->create([
            'user_id' => $user->id,
            'balance' => 1000,
        ]);

        $idempotencyKey = 'shop-purchase-test-001';

        $first = $this->actingAs($user)->post(route('shop.purchase', $item->id), [
            'idempotency_key' => $idempotencyKey,
        ]);
        $first->assertRedirect();
        $first->assertSessionHas('success');

        $second = $this->actingAs($user)->post(route('shop.purchase', $item->id), [
            'idempotency_key' => $idempotencyKey,
        ]);
        $second->assertRedirect();
        $second->assertSessionHas('success');

        $this->assertSame(
            1,
            \App\Models\UserPurchase::query()
                ->where('user_id', $user->id)
                ->where('shop_item_id', $item->id)
                ->where('idempotency_key', $idempotencyKey)
                ->count()
        );

        $this->assertSame(
            1,
            RewardWalletTransaction::query()
                ->where('user_id', $user->id)
                ->where('unique_key', 'shop.purchase.cost.'.$idempotencyKey)
                ->count()
        );

        $this->assertDatabaseHas('user_reward_wallets', [
            'user_id' => $user->id,
            'balance' => 750,
        ]);

        $this->assertDatabaseHas('shop_items', [
            'id' => $item->id,
            'stock' => 4,
        ]);
    }
}

