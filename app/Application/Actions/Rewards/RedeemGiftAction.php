<?php

namespace App\Application\Actions\Rewards;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Application\Actions\Notifications\NotifyAction;
use App\Domain\Notifications\Enums\NotificationCategory;
use App\Models\Gift;
use App\Models\GiftRedemption;
use App\Models\GiftRedemptionEvent;
use App\Models\RewardWalletTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RedeemGiftAction
{
    public function __construct(
        private readonly ApplyRewardWalletTransactionAction $applyRewardWalletTransactionAction,
        private readonly StoreAuditLogAction $storeAuditLogAction,
        private readonly NotifyAction $notifyAction
    ) {
    }

    /**
     * @return array{redemption: GiftRedemption, idempotent: bool}
     */
    public function execute(User $user, int $giftId, string $idempotencyKey): array
    {
        return DB::transaction(function () use ($user, $giftId, $idempotencyKey) {
            $transactionKey = 'gift.redeem.cost.'.$idempotencyKey;

            $existingTx = RewardWalletTransaction::query()
                ->where('user_id', $user->id)
                ->where('unique_key', $transactionKey)
                ->lockForUpdate()
                ->first();

            if ($existingTx) {
                $existingRedemption = GiftRedemption::query()->find((int) $existingTx->ref_id);
                if ($existingRedemption) {
                    return [
                        'redemption' => $existingRedemption,
                        'idempotent' => true,
                    ];
                }
            }

            $gift = Gift::query()
                ->whereKey($giftId)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $gift->is_active) {
                throw new RuntimeException('Ce cadeau n est pas disponible.');
            }

            if ((int) $gift->stock <= 0) {
                throw new RuntimeException('Stock indisponible pour ce cadeau.');
            }

            $redemption = GiftRedemption::query()->create([
                'user_id' => $user->id,
                'gift_id' => $gift->id,
                'cost_points_snapshot' => (int) $gift->cost_points,
                'status' => GiftRedemption::STATUS_PENDING,
                'reason' => null,
                'tracking_code' => null,
                'requested_at' => now(),
                'approved_at' => null,
                'rejected_at' => null,
                'shipped_at' => null,
                'delivered_at' => null,
            ]);

            $walletResult = $this->applyRewardWalletTransactionAction->execute(
                user: $user,
                type: RewardWalletTransaction::TYPE_REDEEM_COST,
                amount: -((int) $gift->cost_points),
                uniqueKey: $transactionKey,
                refType: RewardWalletTransaction::REF_TYPE_GIFT,
                refId: (string) $redemption->id,
                metadata: [
                    'gift_id' => $gift->id,
                    'gift_title' => $gift->title,
                    'idempotency_key' => $idempotencyKey,
                ],
                initialBalanceIfMissing: 0,
            );

            if ($walletResult['idempotent']) {
                return [
                    'redemption' => GiftRedemption::query()->findOrFail($redemption->id),
                    'idempotent' => true,
                ];
            }

            $gift->stock = (int) $gift->stock - 1;
            $gift->save();

            GiftRedemptionEvent::query()->create([
                'redemption_id' => $redemption->id,
                'actor_user_id' => $user->id,
                'type' => 'redeem_requested',
                'data' => [
                    'gift_id' => $gift->id,
                    'cost_points' => (int) $gift->cost_points,
                ],
                'created_at' => now(),
            ]);

            $this->storeAuditLogAction->execute(
                action: 'gift.redeem',
                actor: $user,
                target: $redemption,
                context: [
                    'gift_id' => $gift->id,
                    'gift_title' => $gift->title,
                    'cost_points' => (int) $gift->cost_points,
                    'idempotency_key' => $idempotencyKey,
                    'balance_after' => (int) $walletResult['wallet']->balance,
                ],
            );

            $this->notifyAction->execute(
                user: $user,
                category: NotificationCategory::SYSTEM->value,
                title: 'Cadeau en attente',
                message: 'Ta demande pour "'.$gift->title.'" est en attente de validation.',
                data: [
                    'gift_id' => $gift->id,
                    'redemption_id' => $redemption->id,
                ],
            );

            return [
                'redemption' => $redemption->fresh(['gift']),
                'idempotent' => false,
            ];
        });
    }
}

