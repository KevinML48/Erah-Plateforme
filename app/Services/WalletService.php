<?php

namespace App\Services;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Application\Actions\Bets\ApplyWalletTransactionAction;
use App\Application\Actions\Rewards\ApplyRewardWalletTransactionAction;
use App\Models\RewardWalletTransaction;
use App\Models\User;
use App\Models\UserRewardWallet;
use App\Models\UserWallet;
use App\Models\WalletTransaction;

class WalletService
{
    public function __construct(
        private readonly ApplyRewardWalletTransactionAction $applyRewardWalletTransactionAction,
        private readonly ApplyWalletTransactionAction $applyWalletTransactionAction,
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    /**
     * @param array<string, mixed> $meta
     * @return array{wallet: UserRewardWallet, transaction: RewardWalletTransaction, idempotent: bool, actual_amount: int}
     */
    public function adjustPoints(
        User $user,
        int $amount,
        string $uniqueKey,
        array $meta = [],
        bool $allowPartialDebit = false
    ): array {
        $actualAmount = $amount;
        if ($amount < 0 && $allowPartialDebit) {
            $currentBalance = (int) ($user->rewardWallet?->balance
                ?? UserRewardWallet::query()->where('user_id', $user->id)->value('balance')
                ?? 0);

            $actualAmount = -min($currentBalance, abs($amount));
        }

        $result = $this->applyRewardWalletTransactionAction->execute(
            user: $user,
            type: RewardWalletTransaction::TYPE_ADJUST,
            amount: $actualAmount,
            uniqueKey: $uniqueKey,
            refType: RewardWalletTransaction::REF_TYPE_SYSTEM,
            refId: $uniqueKey,
            metadata: $meta,
        );

        $this->storeAuditLogAction->execute(
            action: 'wallet.reward.adjusted',
            actor: $user,
            target: $result['transaction'],
            context: [
                'requested_amount' => $amount,
                'actual_amount' => $actualAmount,
                'unique_key' => $uniqueKey,
            ],
        );

        return $result + ['actual_amount' => $actualAmount];
    }

    /**
     * @param array<string, mixed> $meta
     * @return array{wallet: UserRewardWallet, transaction: RewardWalletTransaction, idempotent: bool, actual_amount: int}
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
     * @return array{wallet: UserWallet, transaction: WalletTransaction, idempotent: bool, actual_amount: int}
     */
    public function adjustBetPoints(
        User $user,
        int $amount,
        string $uniqueKey,
        array $meta = [],
        bool $allowPartialDebit = false
    ): array {
        $actualAmount = $amount;
        if ($amount < 0 && $allowPartialDebit) {
            $currentBalance = (int) ($user->wallet?->balance
                ?? UserWallet::query()->where('user_id', $user->id)->value('balance')
                ?? 0);

            $actualAmount = -min($currentBalance, abs($amount));
        }

        $result = $this->applyWalletTransactionAction->execute(
            user: $user,
            type: WalletTransaction::TYPE_ADJUST,
            amount: $actualAmount,
            uniqueKey: $uniqueKey,
            refType: WalletTransaction::REF_TYPE_SYSTEM,
            refId: $uniqueKey,
            metadata: $meta,
        );

        $this->storeAuditLogAction->execute(
            action: 'wallet.bet.adjusted',
            actor: $user,
            target: $result['transaction'],
            context: [
                'requested_amount' => $amount,
                'actual_amount' => $actualAmount,
                'unique_key' => $uniqueKey,
            ],
        );

        return $result + ['actual_amount' => $actualAmount];
    }

    public function pointsBalance(User $user): int
    {
        return (int) ($user->rewardWallet?->balance
            ?? UserRewardWallet::query()->where('user_id', $user->id)->value('balance')
            ?? 0);
    }
}
