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
use App\Services\Gifts\GiftEligibilityService;
use App\Services\Gifts\GiftRedemptionAutomationService;
use App\Services\MissionEngine;
use App\Services\PlatformPointService;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RedeemGiftAction
{
    public function __construct(
        private readonly PlatformPointService $platformPointService,
        private readonly StoreAuditLogAction $storeAuditLogAction,
        private readonly NotifyAction $notifyAction,
        private readonly MissionEngine $missionEngine,
        private readonly GiftEligibilityService $giftEligibilityService,
        private readonly GiftRedemptionAutomationService $giftRedemptionAutomationService
    ) {
    }

    /**
     * @return array{redemption: GiftRedemption, idempotent: bool}
     */
    public function execute(User $user, int $giftId, string $idempotencyKey): array
    {
        $result = DB::transaction(function () use ($user, $giftId, $idempotencyKey) {
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

            $this->giftEligibilityService->assertPurchasable($user, $gift);

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

            $walletResult = $this->platformPointService->debit(
                user: $user,
                amount: (int) $gift->cost_points,
                type: RewardWalletTransaction::TYPE_GIFT_PURCHASE,
                uniqueKey: $transactionKey,
                refType: RewardWalletTransaction::REF_TYPE_GIFT,
                refId: (string) $redemption->id,
                meta: [
                    'gift_id' => $gift->id,
                    'gift_title' => $gift->title,
                    'idempotency_key' => $idempotencyKey,
                ],
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

            $automation = $this->giftRedemptionAutomationService->autoDeliverIfEligible(
                user: $user,
                gift: $gift,
                redemption: $redemption
            );

            $this->storeAuditLogAction->execute(
                action: 'gift.redeem',
                actor: $user,
                target: $redemption->fresh(),
                context: [
                    'gift_id' => $gift->id,
                    'gift_title' => $gift->title,
                    'cost_points' => (int) $gift->cost_points,
                    'idempotency_key' => $idempotencyKey,
                    'balance_after' => (int) $walletResult['wallet']->balance,
                    'auto_delivered' => $automation['auto_delivered'],
                    'grant_summary' => $automation['grant_summary'],
                ],
            );

            $this->notifyAction->execute(
                user: $user,
                category: NotificationCategory::SYSTEM->value,
                title: $automation['auto_delivered'] ? 'Objet de profil livre' : 'Cadeau en attente',
                message: $automation['auto_delivered']
                    ? 'Ton achat "'.$gift->title.'" est disponible sur ton profil.'
                    : 'Ta demande pour "'.$gift->title.'" est en attente de validation.',
                data: [
                    'gift_id' => $gift->id,
                    'redemption_id' => $redemption->id,
                    'auto_delivered' => $automation['auto_delivered'],
                ],
            );

            return [
                'redemption' => $redemption->fresh(['gift']),
                'idempotent' => false,
            ];
        });

        if (! $result['idempotent']) {
            /** @var GiftRedemption $redemption */
            $redemption = $result['redemption'];

            $this->missionEngine->recordEvent($user, 'gift.redeemed', 1, [
                'event_key' => 'gift.redeemed.'.$redemption->id,
                'subject_type' => GiftRedemption::class,
                'subject_id' => (string) $redemption->id,
                'gift_id' => $redemption->gift_id,
                'cost_points' => (int) $redemption->cost_points_snapshot,
            ]);
        }

        return $result;
    }
}
