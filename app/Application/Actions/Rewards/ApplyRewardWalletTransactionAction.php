<?php

namespace App\Application\Actions\Rewards;

use App\Models\RewardWalletTransaction;
use App\Models\User;
use App\Models\UserRewardWallet;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ApplyRewardWalletTransactionAction
{
    /**
     * @param array<string, mixed> $metadata
     * @return array{wallet: UserRewardWallet, transaction: RewardWalletTransaction, idempotent: bool}
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
        return DB::transaction(function () use (
            $user,
            $type,
            $amount,
            $uniqueKey,
            $refType,
            $refId,
            $metadata,
            $initialBalanceIfMissing
        ) {
            $existing = RewardWalletTransaction::query()
                ->where('user_id', $user->id)
                ->where('unique_key', $uniqueKey)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                $wallet = UserRewardWallet::query()
                    ->where('user_id', $user->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                return [
                    'wallet' => $wallet,
                    'transaction' => $existing,
                    'idempotent' => true,
                ];
            }

            $wallet = UserRewardWallet::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (! $wallet) {
                $wallet = UserRewardWallet::query()->create([
                    'user_id' => $user->id,
                    'balance' => $initialBalanceIfMissing,
                ]);
            }

            $nextBalance = (int) $wallet->balance + $amount;
            if ($nextBalance < 0) {
                throw new RuntimeException('Insufficient reward points balance.');
            }

            $wallet->balance = $nextBalance;
            $wallet->save();

            $transaction = RewardWalletTransaction::query()->create([
                'user_id' => $user->id,
                'type' => $type,
                'amount' => $amount,
                'balance_after' => $nextBalance,
                'ref_type' => $refType,
                'ref_id' => $refId,
                'unique_key' => $uniqueKey,
                'metadata' => $metadata,
                'created_at' => now(),
            ]);

            return [
                'wallet' => $wallet->fresh(),
                'transaction' => $transaction,
                'idempotent' => false,
            ];
        });
    }
}

