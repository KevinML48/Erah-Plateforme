<?php

namespace App\Application\Actions\Bets;

use App\Models\User;
use App\Models\UserWallet;
use App\Models\WalletTransaction;
use RuntimeException;

class ApplyWalletTransactionAction
{
    /**
     * @param array<string, mixed> $metadata
     * @return array{wallet: UserWallet, transaction: WalletTransaction, idempotent: bool}
     */
    public function execute(
        User $user,
        string $type,
        int $amount,
        string $uniqueKey,
        ?string $refType = null,
        ?string $refId = null,
        array $metadata = [],
        int $initialBalanceIfMissing = 0
    ): array {
        $existing = WalletTransaction::query()
            ->where('user_id', $user->id)
            ->where('unique_key', $uniqueKey)
            ->lockForUpdate()
            ->first();

        if ($existing) {
            $wallet = UserWallet::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->firstOrFail();

            return [
                'wallet' => $wallet,
                'transaction' => $existing,
                'idempotent' => true,
            ];
        }

        $wallet = UserWallet::query()
            ->where('user_id', $user->id)
            ->lockForUpdate()
            ->first();

        if (! $wallet) {
            UserWallet::query()->create([
                'user_id' => $user->id,
                'balance' => $initialBalanceIfMissing,
            ]);

            $wallet = UserWallet::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->firstOrFail();
        }

        $nextBalance = $wallet->balance + $amount;
        if ($nextBalance < 0) {
            throw new RuntimeException('Insufficient wallet balance.');
        }

        $transaction = WalletTransaction::query()->create([
            'user_id' => $user->id,
            'type' => $type,
            'amount' => $amount,
            'balance_after' => $nextBalance,
            'ref_type' => $refType,
            'ref_id' => $refId,
            'unique_key' => $uniqueKey,
            'metadata' => $metadata ?: null,
            'created_at' => now(),
        ]);

        $wallet->balance = $nextBalance;
        $wallet->save();

        return [
            'wallet' => $wallet,
            'transaction' => $transaction,
            'idempotent' => false,
        ];
    }
}
