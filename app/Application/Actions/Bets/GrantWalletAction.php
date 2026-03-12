<?php

namespace App\Application\Actions\Bets;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\RewardWalletTransaction;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use App\Services\PlatformPointService;
use RuntimeException;

class GrantWalletAction
{
    public function __construct(
        private readonly PlatformPointService $platformPointService,
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
        string $idempotencyKey,
        bool $mirrorLegacyBetLedger = false
    ): array {
        if ($amount <= 0) {
            throw new RuntimeException('Grant amount must be greater than zero.');
        }

        return DB::transaction(function () use ($actor, $targetUser, $amount, $reason, $idempotencyKey, $mirrorLegacyBetLedger) {
            $result = $this->platformPointService->credit(
                user: $targetUser,
                amount: $amount,
                type: RewardWalletTransaction::TYPE_ADMIN_ADJUSTMENT,
                uniqueKey: 'admin.wallet.grant.'.$idempotencyKey,
                refType: RewardWalletTransaction::REF_TYPE_ADMIN,
                refId: (string) $actor->id,
                meta: [
                    'reason' => $reason,
                    'actor_id' => $actor->id,
                ],
                mirrorLegacyBetLedger: $mirrorLegacyBetLedger,
                legacyWalletType: WalletTransaction::TYPE_GRANT,
                initialBalanceIfMissing: 0,
            );

            $this->storeAuditLogAction->execute(
                action: 'wallet.grant',
                actor: $actor,
                target: $result['transaction'],
                context: [
                    'target_user_id' => $targetUser->id,
                    'amount' => $amount,
                    'reason' => $reason,
                    'idempotency_key' => $idempotencyKey,
                    'idempotent' => $result['idempotent'],
                    'legacy_mirror' => $mirrorLegacyBetLedger,
                ],
            );

            return [
                'wallet_balance' => (int) $result['wallet']->balance,
                'idempotent' => (bool) $result['idempotent'],
            ];
        });
    }
}
