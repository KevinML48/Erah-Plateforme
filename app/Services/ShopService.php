<?php

namespace App\Services;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\ShopItem;
use App\Models\User;
use App\Models\UserPurchase;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ShopService
{
    public function __construct(
        private readonly PlatformPointService $platformPointService,
        private readonly MissionEngine $missionEngine,
        private readonly AchievementService $achievementService,
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    public function purchase(User $user, ShopItem $item): UserPurchase
    {
        return DB::transaction(function () use ($user, $item) {
            $item = ShopItem::query()->whereKey($item->id)->lockForUpdate()->firstOrFail();

            if (! $item->is_active) {
                throw new RuntimeException('Article indisponible.');
            }

            if ($item->stock !== null && $item->stock <= 0) {
                throw new RuntimeException('Article epuise.');
            }

            $this->platformPointService->debit(
                user: $user,
                amount: (int) $item->cost_points,
                type: \App\Models\RewardWalletTransaction::TYPE_SHOP_PURCHASE,
                uniqueKey: 'shop.purchase.'.$user->id.'.'.$item->id.'.'.now()->timestamp,
                meta: ['shop_item_id' => $item->id],
            );

            $purchase = UserPurchase::query()->create([
                'shop_item_id' => $item->id,
                'user_id' => $user->id,
                'cost_points' => (int) $item->cost_points,
                'status' => 'completed',
                'payload' => $item->payload,
                'purchased_at' => now(),
            ]);

            if ($item->stock !== null) {
                $item->stock = max(0, (int) $item->stock - 1);
                $item->save();
            }

            $this->missionEngine->recordEvent($user, 'shop.purchase', 1, [
                'event_key' => 'shop.purchase.'.$purchase->id,
                'subject_type' => UserPurchase::class,
                'subject_id' => (string) $purchase->id,
            ]);
            $this->achievementService->sync($user);

            $this->storeAuditLogAction->execute(
                action: 'shop.purchase.completed',
                actor: $user,
                target: $purchase,
                context: [
                    'shop_item_key' => $item->key,
                    'cost_points' => $item->cost_points,
                ],
            );

            return $purchase;
        });
    }

    public function seedDefaults(): void
    {
        foreach ((array) config('community.shop.defaults', []) as $definition) {
            ShopItem::query()->updateOrCreate(
                ['key' => $definition['key']],
                [
                    'name' => $definition['name'],
                    'description' => $definition['description'] ?? null,
                    'type' => $definition['type'],
                    'cost_points' => (int) $definition['cost_points'],
                    'stock' => $definition['stock'],
                    'payload' => $definition['payload'] ?? null,
                    'is_active' => true,
                    'is_featured' => (bool) ($definition['featured'] ?? false),
                    'sort_order' => (int) ($definition['sort_order'] ?? 0),
                ],
            );
        }
    }
}
