<?php

namespace App\Application\Actions\Bets;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class GrantWalletAction
{
    public function __construct(
        private readonly ApplyWalletTransactionAction $applyWalletTransactionAction,
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    /**
     * @return array{wallet_balance: int, idempotent: bool}
     */
    public function execute(
        User $actor,
        User $targetUser,
        int $amount,
        string $reason,
        string $idempotencyKey
    ): array {
        if ($amount <= 0) {
            throw new RuntimeException('Grant amount must be greater than zero.');
        }

        return DB::transaction(function () use ($actor, $targetUser, $amount, $reason, $idempotencyKey) {
            $result = $this->applyWalletTransactionAction->execute(
                user: $targetUser,
                type: WalletTransaction::TYPE_GRANT,
                amount: $amount,
                uniqueKey: 'admin.wallet.grant.'.$idempotencyKey,
                refType: WalletTransaction::REF_TYPE_ADMIN,
                refId: (string) $actor->id,
                metadata: [
                    'reason' => $reason,
                    'actor_id' => $actor->id,
                ],
                initialBalanceIfMissing: (int) config('betting.wallet.initial_balance', 1000),
            );

            $this->storeAuditLogAction->execute(
                action: 'wallet.grant',
                actor: $actor,
                target: $targetUser,
                context: [
                    'target_user_id' => $targetUser->id,
                    'amount' => $amount,
                    'reason' => $reason,
                    'idempotency_key' => $idempotencyKey,
                    'idempotent' => $result['idempotent'],
                ],
            );

            return [
                'wallet_balance' => (int) $result['wallet']->balance,
                'idempotent' => (bool) $result['idempotent'],
            ];
        });
    }
}
