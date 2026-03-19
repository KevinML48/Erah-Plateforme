<?php

namespace App\Services\Gifts;

use App\Models\GiftRedemption;
use App\Models\UserProfileCosmetic;
use App\Support\LaunchGiftCatalog;

class GiftDeliveryRepairService
{
    public function __construct(
        private readonly GiftRedemptionAutomationService $giftRedemptionAutomationService,
    ) {
    }

    /**
     * @return array{redemptions_scanned: int, repaired: int, already_ok: int, skipped: int}
     */
    public function repair(?int $userId = null, bool $dryRun = false, int $chunk = 100): array
    {
        $stats = [
            'redemptions_scanned' => 0,
            'repaired' => 0,
            'already_ok' => 0,
            'skipped' => 0,
        ];

        GiftRedemption::query()
            ->with(['user', 'gift'])
            ->when($userId !== null, fn ($query) => $query->where('user_id', $userId))
            ->whereIn('status', [
                GiftRedemption::STATUS_PENDING,
                GiftRedemption::STATUS_APPROVED,
                GiftRedemption::STATUS_SHIPPED,
                GiftRedemption::STATUS_DELIVERED,
            ])
            ->orderBy('id')
            ->chunkById($chunk, function ($redemptions) use (&$stats, $dryRun): void {
                foreach ($redemptions as $redemption) {
                    $stats['redemptions_scanned']++;

                    $user = $redemption->user;
                    $gift = $redemption->gift;
                    $definition = LaunchGiftCatalog::definitionForGift($gift);

                    if (! $user || ! $gift || ! LaunchGiftCatalog::isAutoDeliverableDefinition($definition)) {
                        $stats['skipped']++;
                        continue;
                    }

                    $unlockKeys = LaunchGiftCatalog::profileUnlocksForDefinition($definition)
                        ->pluck('cosmetic_key')
                        ->filter(fn ($value): bool => is_string($value) && $value !== '')
                        ->values();

                    $ownedUnlocks = UserProfileCosmetic::query()
                        ->where('user_id', $user->id)
                        ->whereIn('cosmetic_key', $unlockKeys->all())
                        ->distinct('cosmetic_key')
                        ->count('cosmetic_key');

                    $hasAllUnlocks = $unlockKeys->isNotEmpty() && $ownedUnlocks >= $unlockKeys->count();
                    if ($redemption->status === GiftRedemption::STATUS_DELIVERED && $hasAllUnlocks) {
                        $stats['already_ok']++;
                        continue;
                    }

                    $stats['repaired']++;

                    if (! $dryRun) {
                        $this->giftRedemptionAutomationService->autoDeliverIfEligible($user, $gift, $redemption);
                    }
                }
            });

        return $stats;
    }
}