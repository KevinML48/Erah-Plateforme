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
use Illuminate\Support\Facades\DB;

class PlatformPointService
{
    public function __construct(
        private readonly ApplyRewardWalletTransactionAction $applyRewardWalletTransactionAction,
        private readonly ApplyWalletTransactionAction $applyWalletTransactionAction,
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    /**
     * @param array<string, mixed> $meta
     * @return array{
     *     wallet: \App\Models\UserRewardWallet,
     *     transaction: \App\Models\RewardWalletTransaction,
     *     idempotent: bool,
     *     actual_amount: int,
     *     legacy_transaction: \App\Models\WalletTransaction|null
     * }
     */
    public function apply(
        User $user,
        int $amount,
        string $type,
        string $uniqueKey,
        array $meta = [],
        ?string $refType = null,
        ?string $refId = null,
        bool $allowPartialDebit = false,
        bool $mirrorLegacyBetLedger = false,
        ?string $legacyWalletType = null,
        int $initialBalanceIfMissing = 0
    ): array {
        return DB::transaction(function () use (
            $user,
            $amount,
            $type,
            $uniqueKey,
            $meta,
            $refType,
            $refId,
            $allowPartialDebit,
            $mirrorLegacyBetLedger,
            $legacyWalletType,
            $initialBalanceIfMissing
        ) {
            $walletExists = UserRewardWallet::query()->where('user_id', $user->id)->exists();
            $beforeBalance = $walletExists ? $this->balance($user) : max(0, $initialBalanceIfMissing);
            $actualAmount = $amount;

            if ($amount < 0 && $allowPartialDebit) {
                $actualAmount = -min($beforeBalance, abs($amount));
            }

            $result = $this->applyRewardWalletTransactionAction->execute(
                user: $user,
                type: $type,
                amount: $actualAmount,
                uniqueKey: $uniqueKey,
                refType: $refType,
                refId: $refId,
                metadata: $meta,
                initialBalanceIfMissing: $initialBalanceIfMissing,
            );

            $afterBalance = (int) $result['wallet']->balance;
            $legacyTransaction = null;

            if ($mirrorLegacyBetLedger) {
                $legacyTransaction = $this->mirrorLegacyWalletTransaction(
                    user: $user,
                    beforeBalance: $beforeBalance,
                    amount: $actualAmount,
                    uniqueKey: $uniqueKey,
                    type: $legacyWalletType ?: WalletTransaction::TYPE_ADJUST,
                    refType: $refType,
                    refId: $refId,
                    meta: $meta,
                );
            } else {
                $this->syncLegacyWalletBalance($user, $afterBalance);
            }

            $this->storeAuditLogAction->execute(
                action: 'platform.points.applied',
                actor: $user,
                target: $result['transaction'],
                context: [
                    'type' => $type,
                    'requested_amount' => $amount,
                    'actual_amount' => $actualAmount,
                    'balance_before' => $beforeBalance,
                    'balance_after' => $afterBalance,
                    'unique_key' => $uniqueKey,
                    'legacy_mirror' => $mirrorLegacyBetLedger,
                ],
            );

            return $result + [
                'actual_amount' => $actualAmount,
                'legacy_transaction' => $legacyTransaction,
            ];
        });
    }

    public function balance(User $user): int
    {
        return (int) ($user->rewardWallet?->balance
            ?? UserRewardWallet::query()->where('user_id', $user->id)->value('balance')
            ?? 0);
    }

    /**
     * @param array<string, mixed> $meta
     */
    public function credit(
        User $user,
        int $amount,
        string $type,
        string $uniqueKey,
        array $meta = [],
        ?string $refType = null,
        ?string $refId = null,
        bool $mirrorLegacyBetLedger = false,
        ?string $legacyWalletType = null,
        int $initialBalanceIfMissing = 0
    ): array {
        return $this->apply(
            user: $user,
            amount: abs($amount),
            type: $type,
            uniqueKey: $uniqueKey,
            meta: $meta,
            refType: $refType,
            refId: $refId,
            allowPartialDebit: false,
            mirrorLegacyBetLedger: $mirrorLegacyBetLedger,
            legacyWalletType: $legacyWalletType,
            initialBalanceIfMissing: $initialBalanceIfMissing,
        );
    }

    /**
     * @param array<string, mixed> $meta
     */
    public function debit(
        User $user,
        int $amount,
        string $type,
        string $uniqueKey,
        array $meta = [],
        ?string $refType = null,
        ?string $refId = null,
        bool $allowPartialDebit = false,
        bool $mirrorLegacyBetLedger = false,
        ?string $legacyWalletType = null,
        int $initialBalanceIfMissing = 0
    ): array {
        return $this->apply(
            user: $user,
            amount: -abs($amount),
            type: $type,
            uniqueKey: $uniqueKey,
            meta: $meta,
            refType: $refType,
            refId: $refId,
            allowPartialDebit: $allowPartialDebit,
            mirrorLegacyBetLedger: $mirrorLegacyBetLedger,
            legacyWalletType: $legacyWalletType,
            initialBalanceIfMissing: $initialBalanceIfMissing,
        );
    }

    /**
     * @param array<string, mixed> $meta
     */
    private function mirrorLegacyWalletTransaction(
        User $user,
        int $beforeBalance,
        int $amount,
        string $uniqueKey,
        string $type,
        ?string $refType,
        ?string $refId,
        array $meta
    ): ?WalletTransaction {
        $legacyUniqueKey = 'platform.mirror.'.$uniqueKey;
        $existing = WalletTransaction::query()
            ->where('user_id', $user->id)
            ->where('unique_key', $legacyUniqueKey)
            ->first();

        if ($existing) {
            $this->syncLegacyWalletBalance($user, (int) $existing->balance_after);

            return $existing;
        }

        $this->syncLegacyWalletBalance($user, $beforeBalance);

        $result = $this->applyWalletTransactionAction->execute(
            user: $user,
            type: $type,
            amount: $amount,
            uniqueKey: $legacyUniqueKey,
            refType: $refType,
            refId: $refId,
            metadata: $meta,
            initialBalanceIfMissing: $beforeBalance,
        );

        return $result['transaction'];
    }

    private function syncLegacyWalletBalance(User $user, int $balance): void
    {
        UserWallet::query()->updateOrCreate(
            ['user_id' => $user->id],
            ['balance' => $balance],
        );
    }
}
