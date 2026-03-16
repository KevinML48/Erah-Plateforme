<?php

namespace App\Services\Gifts;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Application\Actions\Notifications\NotifyAction;
use App\Domain\Notifications\Enums\NotificationCategory;
use App\Models\ActivityEvent;
use App\Models\Gift;
use App\Models\GiftCartItem;
use App\Models\GiftRedemption;
use App\Models\GiftRedemptionEvent;
use App\Models\RewardWalletTransaction;
use App\Models\User;
use App\Models\UserRewardWallet;
use App\Services\Gifts\GiftRedemptionAutomationService;
use App\Services\PlatformPointService;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class GiftCartService
{
    public function __construct(
        private readonly PlatformPointService $platformPointService,
        private readonly StoreAuditLogAction $storeAuditLogAction,
        private readonly NotifyAction $notifyAction,
        private readonly GiftRedemptionAutomationService $giftRedemptionAutomationService
    ) {
    }

    /**
     * @return array{
     *     line_items: Collection<int, array{
     *         cart_item: GiftCartItem,
     *         gift: Gift|null,
     *         line_total: int,
     *         is_available: bool,
     *         status_copy: string
     *     }>,
     *     total_points: int,
     *     total_items: int,
     *     wallet_balance: int,
     *     missing_points: int,
     *     can_checkout: bool
     * }
     */
    public function summarize(User $user): array
    {
        $wallet = UserRewardWallet::query()->firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );

        $items = GiftCartItem::query()
            ->where('user_id', $user->id)
            ->with('gift')
            ->latest('updated_at')
            ->get();

        $lineItems = $items->map(function (GiftCartItem $item): array {
            $gift = $item->gift;
            $lineTotal = (int) $item->quantity * (int) ($gift?->cost_points ?? 0);
            $isAvailable = $gift instanceof Gift
                && $gift->is_active
                && (int) $gift->stock >= (int) $item->quantity;

            $statusCopy = $gift === null
                ? 'Cadeau introuvable'
                : (! $gift->is_active
                    ? 'Cadeau desactive'
                    : ((int) $gift->stock < (int) $item->quantity
                        ? 'Stock insuffisant'
                        : 'Pret a commander'));

            return [
                'cart_item' => $item,
                'gift' => $gift,
                'line_total' => $lineTotal,
                'is_available' => $isAvailable,
                'status_copy' => $statusCopy,
            ];
        })->values();

        $totalPoints = (int) $lineItems->sum('line_total');
        $walletBalance = (int) $wallet->balance;
        $missingPoints = max(0, $totalPoints - $walletBalance);

        return [
            'line_items' => $lineItems,
            'total_points' => $totalPoints,
            'total_items' => (int) $items->sum('quantity'),
            'wallet_balance' => $walletBalance,
            'missing_points' => $missingPoints,
            'can_checkout' => $lineItems->isNotEmpty()
                && $lineItems->every(fn (array $lineItem): bool => $lineItem['is_available'] === true)
                && $missingPoints === 0,
        ];
    }

    public function countItems(User $user): int
    {
        return (int) GiftCartItem::query()->where('user_id', $user->id)->sum('quantity');
    }

    public function add(User $user, int $giftId, int $quantity = 1): GiftCartItem
    {
        if ($quantity < 1) {
            throw new RuntimeException('Quantite invalide pour ce cadeau.');
        }

        return DB::transaction(function () use ($user, $giftId, $quantity): GiftCartItem {
            $gift = Gift::query()->whereKey($giftId)->lockForUpdate()->firstOrFail();

            if (! $gift->is_active) {
                throw new RuntimeException('Ce cadeau est actuellement indisponible.');
            }

            if ((int) $gift->stock <= 0) {
                throw new RuntimeException('Ce cadeau est en rupture de stock.');
            }

            $this->giftRedemptionAutomationService->assertRedeemable($user, $gift);

            $cartItem = GiftCartItem::query()
                ->where('user_id', $user->id)
                ->where('gift_id', $gift->id)
                ->lockForUpdate()
                ->first();

            $nextQuantity = ($cartItem ? (int) $cartItem->quantity : 0) + $quantity;
            if ($nextQuantity > (int) $gift->stock) {
                throw new RuntimeException('Stock insuffisant pour ajouter cette quantite au panier.');
            }

            if (! $cartItem) {
                $cartItem = GiftCartItem::query()->create([
                    'user_id' => $user->id,
                    'gift_id' => $gift->id,
                    'quantity' => $nextQuantity,
                    'added_at' => now(),
                ]);
            } else {
                $cartItem->quantity = $nextQuantity;
                $cartItem->save();
            }

            $this->storeActivityEvent(
                user: $user,
                eventType: ActivityEvent::TYPE_GIFT_CART_ADD,
                refType: 'gift',
                refId: (string) $gift->id,
                metadata: [
                    'gift_id' => $gift->id,
                    'gift_title' => $gift->title,
                    'quantity_added' => $quantity,
                    'quantity_total' => $nextQuantity,
                ],
            );

            $this->storeAuditLogAction->execute(
                action: 'gift.cart.add',
                actor: $user,
                target: $cartItem,
                context: [
                    'gift_id' => $gift->id,
                    'quantity_added' => $quantity,
                    'quantity_total' => $nextQuantity,
                ],
            );

            return $cartItem->fresh(['gift']);
        });
    }

    public function updateQuantity(User $user, int $cartItemId, int $quantity): GiftCartItem
    {
        if ($quantity < 1) {
            throw new RuntimeException('Quantite invalide pour ce cadeau.');
        }

        return DB::transaction(function () use ($user, $cartItemId, $quantity): GiftCartItem {
            $cartItem = GiftCartItem::query()
                ->where('user_id', $user->id)
                ->whereKey($cartItemId)
                ->with('gift')
                ->lockForUpdate()
                ->firstOrFail();

            $gift = Gift::query()->whereKey($cartItem->gift_id)->lockForUpdate()->firstOrFail();
            if (! $gift->is_active) {
                throw new RuntimeException('Ce cadeau est desactive et ne peut plus etre commande.');
            }

            if ((int) $gift->stock < $quantity) {
                throw new RuntimeException('Stock insuffisant pour cette quantite.');
            }

            $cartItem->quantity = $quantity;
            $cartItem->save();

            $this->storeActivityEvent(
                user: $user,
                eventType: ActivityEvent::TYPE_GIFT_CART_UPDATE,
                refType: 'gift',
                refId: (string) $gift->id,
                metadata: [
                    'gift_id' => $gift->id,
                    'quantity_total' => $quantity,
                ],
            );

            $this->storeAuditLogAction->execute(
                action: 'gift.cart.update',
                actor: $user,
                target: $cartItem,
                context: [
                    'gift_id' => $gift->id,
                    'quantity_total' => $quantity,
                ],
            );

            return $cartItem->fresh(['gift']);
        });
    }

    public function remove(User $user, int $cartItemId): void
    {
        DB::transaction(function () use ($user, $cartItemId): void {
            $cartItem = GiftCartItem::query()
                ->where('user_id', $user->id)
                ->whereKey($cartItemId)
                ->with('gift')
                ->lockForUpdate()
                ->firstOrFail();

            $gift = $cartItem->gift;
            $cartItem->delete();

            $this->storeActivityEvent(
                user: $user,
                eventType: ActivityEvent::TYPE_GIFT_CART_REMOVE,
                refType: 'gift',
                refId: (string) ($gift?->id ?? 0),
                metadata: [
                    'gift_id' => $gift?->id,
                    'gift_title' => $gift?->title,
                ],
            );

            $this->storeAuditLogAction->execute(
                action: 'gift.cart.remove',
                actor: $user,
                target: $gift,
                context: ['gift_id' => $gift?->id],
            );
        });
    }

    /**
     * @return array{redemptions: EloquentCollection<int, GiftRedemption>, idempotent: bool, total_points: int}
     */
    public function checkout(User $user, string $idempotencyKey): array
    {
        if (trim($idempotencyKey) === '') {
            throw new RuntimeException('Validation panier impossible: cle idempotente manquante.');
        }

        return DB::transaction(function () use ($user, $idempotencyKey): array {
            $checkoutUniqueKey = 'gift.cart.checkout.total.'.$idempotencyKey;

            $existingTransaction = RewardWalletTransaction::query()
                ->where('user_id', $user->id)
                ->where('unique_key', $checkoutUniqueKey)
                ->lockForUpdate()
                ->first();

            if ($existingTransaction) {
                return [
                    'redemptions' => new EloquentCollection(),
                    'idempotent' => true,
                    'total_points' => abs((int) $existingTransaction->amount),
                ];
            }

            $cartItems = GiftCartItem::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->get();

            if ($cartItems->isEmpty()) {
                throw new RuntimeException('Votre panier cadeaux est vide.');
            }

            $gifts = Gift::query()
                ->whereIn('id', $cartItems->pluck('gift_id')->all())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $wallet = UserRewardWallet::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (! $wallet) {
                UserRewardWallet::query()->create([
                    'user_id' => $user->id,
                    'balance' => 0,
                ]);

                $wallet = UserRewardWallet::query()
                    ->where('user_id', $user->id)
                    ->lockForUpdate()
                    ->firstOrFail();
            }

            $lines = [];
            $totalPoints = 0;

            foreach ($cartItems as $cartItem) {
                $gift = $gifts->get((int) $cartItem->gift_id);
                if (! $gift) {
                    throw new RuntimeException('Un cadeau du panier n est plus disponible.');
                }

                if (! $gift->is_active) {
                    throw new RuntimeException('Le cadeau "'.$gift->title.'" est desactive.');
                }

                if ((int) $gift->stock < (int) $cartItem->quantity) {
                    throw new RuntimeException('Stock insuffisant pour "'.$gift->title.'".');
                }

                $this->giftRedemptionAutomationService->assertRedeemable($user, $gift);

                $lineTotal = (int) $gift->cost_points * (int) $cartItem->quantity;
                $totalPoints += $lineTotal;

                $lines[] = [
                    'gift_id' => $gift->id,
                    'gift_title' => $gift->title,
                    'quantity' => (int) $cartItem->quantity,
                    'unit_cost' => (int) $gift->cost_points,
                    'line_total' => $lineTotal,
                ];
            }

            if ((int) $wallet->balance < $totalPoints) {
                throw new RuntimeException('Solde insuffisant pour valider tout le panier.');
            }

            $walletResult = $this->platformPointService->debit(
                user: $user,
                amount: $totalPoints,
                type: RewardWalletTransaction::TYPE_GIFT_PURCHASE,
                uniqueKey: $checkoutUniqueKey,
                meta: [
                    'idempotency_key' => $idempotencyKey,
                    'source' => 'gift_cart_checkout',
                    'lines' => $lines,
                    'total_points' => $totalPoints,
                ],
                refType: RewardWalletTransaction::REF_TYPE_GIFT,
                refId: 'cart',
            );

            if ($walletResult['idempotent']) {
                return [
                    'redemptions' => new EloquentCollection(),
                    'idempotent' => true,
                    'total_points' => $totalPoints,
                ];
            }

            $redemptions = new EloquentCollection();
            $autoDeliveredCount = 0;

            foreach ($cartItems as $cartItem) {
                $gift = $gifts->get((int) $cartItem->gift_id);
                if (! $gift) {
                    continue;
                }

                for ($index = 0; $index < (int) $cartItem->quantity; $index++) {
                    $redemption = GiftRedemption::query()->create([
                        'user_id' => $user->id,
                        'gift_id' => $gift->id,
                        'cost_points_snapshot' => (int) $gift->cost_points,
                        'status' => GiftRedemption::STATUS_PENDING,
                        'reason' => null,
                        'tracking_code' => null,
                        'tracking_carrier' => null,
                        'shipping_note' => null,
                        'internal_note' => null,
                        'requested_at' => now(),
                    ]);

                    GiftRedemptionEvent::query()->create([
                        'redemption_id' => $redemption->id,
                        'actor_user_id' => $user->id,
                        'type' => 'redeem_requested',
                        'data' => [
                            'gift_id' => $gift->id,
                            'cost_points' => (int) $gift->cost_points,
                            'source' => 'gift_cart_checkout',
                            'idempotency_key' => $idempotencyKey,
                        ],
                        'created_at' => now(),
                    ]);

                    $automation = $this->giftRedemptionAutomationService->autoDeliverIfEligible(
                        user: $user,
                        gift: $gift,
                        redemption: $redemption
                    );

                    if ($automation['auto_delivered']) {
                        $autoDeliveredCount++;
                    }

                    $redemptions->push($redemption->fresh());
                }

                $gift->stock = max(0, (int) $gift->stock - (int) $cartItem->quantity);
                $gift->save();
            }

            $transaction = $walletResult['transaction'];
            $metadata = is_array($transaction->metadata) ? $transaction->metadata : [];
            $metadata['redemption_ids'] = $redemptions->pluck('id')->values()->all();
            $metadata['redemptions_count'] = $redemptions->count();
            $transaction->metadata = $metadata;
            $transaction->save();

            GiftCartItem::query()->where('user_id', $user->id)->delete();

            $this->storeActivityEvent(
                user: $user,
                eventType: ActivityEvent::TYPE_GIFT_CART_CHECKOUT,
                refType: 'gift_cart',
                refId: (string) $transaction->id,
                metadata: [
                    'total_points' => $totalPoints,
                    'line_count' => count($lines),
                    'redemptions_count' => $redemptions->count(),
                    'idempotency_key' => $idempotencyKey,
                ],
            );

            $this->storeAuditLogAction->execute(
                action: 'gift.cart.checkout',
                actor: $user,
                target: $transaction,
                context: [
                    'total_points' => $totalPoints,
                    'line_count' => count($lines),
                    'redemptions_count' => $redemptions->count(),
                    'auto_delivered_count' => $autoDeliveredCount,
                    'idempotency_key' => $idempotencyKey,
                ],
            );

            $pendingCount = $redemptions->where('status', GiftRedemption::STATUS_PENDING)->count();
            $this->notifyAction->execute(
                user: $user,
                category: NotificationCategory::SYSTEM->value,
                title: $pendingCount > 0 ? 'Commande cadeaux en attente' : 'Recompenses attribuees',
                message: $pendingCount > 0
                    ? 'Ta commande contient '.$pendingCount.' demande(s) en attente'
                        .($autoDeliveredCount > 0 ? ' et '.$autoDeliveredCount.' recompense(s) attribuee(s) automatiquement.' : '.')
                    : 'Tes recompenses achetees sont deja disponibles.',
                data: [
                    'total_points' => $totalPoints,
                    'redemptions_count' => $redemptions->count(),
                    'auto_delivered_count' => $autoDeliveredCount,
                ],
            );

            return [
                'redemptions' => $redemptions,
                'idempotent' => false,
                'total_points' => $totalPoints,
            ];
        });
    }

    /**
     * @param array<string, mixed> $metadata
     */
    private function storeActivityEvent(
        User $user,
        string $eventType,
        string $refType,
        string $refId,
        array $metadata = []
    ): void {
        ActivityEvent::query()->create([
            'user_id' => $user->id,
            'event_type' => $eventType,
            'ref_type' => $refType,
            'ref_id' => $refId,
            'occurred_at' => now(),
            'unique_key' => $eventType.':'.Str::uuid()->toString(),
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }
}
