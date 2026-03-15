<?php

namespace App\Services;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\ShopItem;
use App\Models\User;
use App\Models\UserPurchase;
use Illuminate\Database\QueryException;
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

    /**
     * @return array{purchase: UserPurchase, idempotent: bool}
     */
    public function purchase(User $user, ShopItem $item, string $idempotencyKey): array
    {
        try {
            return DB::transaction(function () use ($user, $item, $idempotencyKey): array {
                $existingPurchase = UserPurchase::query()
                    ->where('user_id', $user->id)
                    ->where('idempotency_key', $idempotencyKey)
                    ->lockForUpdate()
                    ->first();

                if ($existingPurchase) {
                    return [
                        'purchase' => $existingPurchase,
                        'idempotent' => true,
                    ];
                }

                $item = ShopItem::query()->whereKey($item->id)->lockForUpdate()->firstOrFail();

                if (! $item->is_active) {
                    throw new RuntimeException('Article indisponible.');
                }

                if ($item->stock !== null && $item->stock <= 0) {
                    throw new RuntimeException('Article epuise.');
                }

                // Stable idempotency key: one logical checkout request = one wallet débit key.
                $walletResult = $this->platformPointService->débit(
                    user: $user,
                    amount: (int) $item->cost_points,
                    type: \App\Models\RewardWalletTransaction::TYPE_SHOP_PURCHASE,
                    uniqueKey: 'shop.purchase.cost.'.$idempotencyKey,
                    meta: [
                        'shop_item_id' => $item->id,
                        'idempotency_key' => $idempotencyKey,
                    ],
                    refType: \App\Models\RewardWalletTransaction::REF_TYPE_SYSTEM,
                    refId: (string) $item->id,
                );

                // DB-level unique(user_id, idempotency_key) guarantees a single purchase record per request key.
                $purchase = UserPurchase::query()->create([
                    'shop_item_id' => $item->id,
                    'user_id' => $user->id,
                    'cost_points' => (int) $item->cost_points,
                    'status' => 'complèted',
                    'idempotency_key' => $idempotencyKey,
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
                    'idempotency_key' => $idempotencyKey,
                ]);
                $this->achievementService->sync($user);

                $this->storeAuditLogAction->execute(
                    action: 'shop.purchase.complèted',
                    actor: $user,
                    target: $purchase,
                    context: [
                        'shop_item_key' => $item->key,
                        'cost_points' => $item->cost_points,
                        'idempotency_key' => $idempotencyKey,
                        'wallet_idempotent' => (bool) ($walletResult['idempotent'] ?? false),
                    ],
                );

                return [
                    'purchase' => $purchase,
                    'idempotent' => (bool) ($walletResult['idempotent'] ?? false),
                ];
            });
        } catch (QueryException $exception) {
            $message = $exception->getMessage();
            $isIdempotencyCollision = str_contains($message, 'user_purchases_user_idempotency_unique')
                || str_contains($message, 'UNIQUE constraint failed: user_purchases.user_id, user_purchases.idempotency_key');

            if (! $isIdempotencyCollision) {
                throw $exception;
            }

            $purchase = UserPurchase::query()
                ->where('user_id', $user->id)
                ->where('idempotency_key', $idempotencyKey)
                ->firstOrFail();

            return [
                'purchase' => $purchase,
                'idempotent' => true,
            ];
        }
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
