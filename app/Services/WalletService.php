<?php

namespace App\Services;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\RewardWalletTransaction;
use App\Models\User;
use App\Models\WalletTransaction;

class WalletService
{
    public function __construct(
        private readonly PlatformPointService $platformPointService,
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    /**
     * @param array<string, mixed> $meta
     * @return array{wallet: \App\Models\UserRewardWallet, transaction: RewardWalletTransaction, idempotent: bool, actual_amount: int, legacy_transaction: \App\Models\WalletTransaction|null}
     */
    public function adjustPoints(
        User $user,
        int $amount,
        string $uniqueKey,
        array $meta = [],
        bool $allowPartialDebit = false
    ): array {
        $result = $this->platformPointService->apply(
            user: $user,
            amount: $amount,
            type: RewardWalletTransaction::TYPE_ADJUST,
            uniqueKey: $uniqueKey,
            meta: $meta,
            refType: RewardWalletTransaction::REF_TYPE_SYSTEM,
            refId: $uniqueKey,
            allowPartialDebit: $allowPartialDebit,
        );

        $this->storeAuditLogAction->execute(
            action: 'wallet.reward.adjusted',
            actor: $user,
            target: $result['transaction'],
            context: [
                'requested_amount' => $amount,
                'actual_amount' => $result['actual_amount'],
                'unique_key' => $uniqueKey,
            ],
        );

        return $result;
    }

    /**
     * @param array<string, mixed> $meta
     * @return array{wallet: \App\Models\UserRewardWallet, transaction: RewardWalletTransaction, idempotent: bool, actual_amount: int, legacy_transaction: \App\Models\WalletTransaction|null}
     */
    public function adjustRewardPoints(
        User $user,
        int $amount,
        string $uniqueKey,
        array $meta = [],
        bool $allowPartialDebit = false
    ): array {
        return $this->adjustPoints($user, $amount, $uniqueKey, $meta, $allowPartialDebit);
    }

    /**
     * @param array<string, mixed> $meta
     * @return array{wallet: \App\Models\UserRewardWallet, transaction: RewardWalletTransaction, idempotent: bool, actual_amount: int, legacy_transaction: WalletTransaction|null}
     */
    public function adjustBetPoints(
        User $user,
        int $amount,
        string $uniqueKey,
        array $meta = [],
        bool $allowPartialDebit = false
    ): array {
        $result = $this->platformPointService->apply(
            user: $user,
            amount: $amount,
            type: RewardWalletTransaction::TYPE_ADJUST,
            uniqueKey: $uniqueKey,
            meta: $meta,
            refType: WalletTransaction::REF_TYPE_SYSTEM,
            refId: $uniqueKey,
            allowPartialDebit: $allowPartialDebit,
            mirrorLegacyBetLedger: true,
            legacyWalletType: WalletTransaction::TYPE_ADJUST,
        );

        $this->storeAuditLogAction->execute(
            action: 'wallet.bet.adjusted',
            actor: $user,
            target: $result['legacy_transaction'] ?? $result['transaction'],
            context: [
                'requested_amount' => $amount,
                'actual_amount' => $result['actual_amount'],
                'unique_key' => $uniqueKey,
            ],
        );

        return $result;
    }

    public function pointsBalance(User $user): int
    {
        return $this->platformPointService->balance($user);
    }
}
