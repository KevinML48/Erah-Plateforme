<?php

namespace App\Services\Gifts;

use App\Models\Gift;
use App\Models\GiftRedemption;
use App\Models\GiftRedemptionEvent;
use App\Models\User;
use App\Services\ProfileCosmeticService;
use App\Support\LaunchGiftCatalog;
use RuntimeException;

class GiftRedemptionAutomationService
{
    public function __construct(
        private readonly ProfileCosmeticService $profileCosmeticService
    ) {
    }

    public function assertRedeemable(User $user, Gift $gift): void
    {
        $blockedReason = $this->profileCosmeticService->blocksRepurchase($user, $gift);
        if ($blockedReason !== null) {
            throw new RuntimeException($blockedReason);
        }
    }

    /**
     * @return array{auto_delivered: bool, grant_summary: string|null}
     */
    public function autoDeliverIfEligible(User $user, Gift $gift, GiftRedemption $redemption): array
    {
        $definition = LaunchGiftCatalog::definitionForGift($gift);
        if (! LaunchGiftCatalog::isAutoDeliverableDefinition($definition)) {
            return [
                'auto_delivered' => false,
                'grant_summary' => null,
            ];
        }

        $result = $this->profileCosmeticService->grantFromRedemption($user, $gift, $redemption);
        if (! $result['applied']) {
            return [
                'auto_delivered' => false,
                'grant_summary' => null,
            ];
        }

        $redemption->status = GiftRedemption::STATUS_DELIVERED;
        $redemption->approved_at = $redemption->approved_at ?: now();
        $redemption->delivered_at = $redemption->delivered_at ?: now();
        $redemption->reason = 'Recompense attribuee automatiquement sur le profil.';
        $redemption->save();

        GiftRedemptionEvent::query()->updateOrCreate(
            [
                'redemption_id' => $redemption->id,
                'type' => 'profile_unlock_granted',
            ],
            [
                'actor_user_id' => null,
                'data' => [
                    'gift_key' => $gift->launchCatalogKey(),
                    'items' => $result['granted']->values()->all(),
                ],
                'created_at' => now(),
            ],
        );

        GiftRedemptionEvent::query()->updateOrCreate(
            [
                'redemption_id' => $redemption->id,
                'type' => 'auto_delivered',
            ],
            [
                'actor_user_id' => null,
                'data' => [
                    'delivery_type' => $definition['delivery_type'] ?? 'profile',
                    'auto_equipped_slots' => $result['auto_equipped_slots'],
                ],
                'created_at' => now(),
            ],
        );

        $summary = $result['granted']
            ->pluck('label')
            ->filter()
            ->implode(', ');

        return [
            'auto_delivered' => true,
            'grant_summary' => $summary !== '' ? $summary : null,
        ];
    }
}
