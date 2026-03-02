<?php

namespace App\Application\Actions\Bets;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\Bet;
use App\Models\EsportMatch;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CancelBetAction
{
    public function __construct(
        private readonly ApplyWalletTransactionAction $applyWalletTransactionAction,
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    /**
     * @return array{bet: Bet, idempotent: bool}
     */
    public function execute(User $user, int $betId, string $idempotencyKey): array
    {
        return DB::transaction(function () use ($user, $betId, $idempotencyKey) {
            $bet = Bet::query()
                ->whereKey($betId)
                ->lockForUpdate()
                ->first();

            if (! $bet) {
                throw new ModelNotFoundException('Bet not found.');
            }

            if ((int) $bet->user_id !== (int) $user->id) {
                throw new AuthorizationException('You cannot cancel this bet.');
            }

            if ($bet->status === Bet::STATUS_CANCELLED) {
                return [
                    'bet' => $bet->fresh(['match:id,match_key,home_team,away_team,starts_at,status,result']),
                    'idempotent' => true,
                ];
            }

            if (in_array($bet->status, [Bet::STATUS_WON, Bet::STATUS_LOST, Bet::STATUS_VOID], true)) {
                throw new RuntimeException('Settled bet cannot be cancelled.');
            }

            $match = EsportMatch::query()
                ->whereKey((int) $bet->match_id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockAt = $match->locked_at ?? $match->starts_at;
            if ($lockAt && now()->greaterThanOrEqualTo($lockAt)) {
                throw new RuntimeException('Bet cannot be cancelled after lock.');
            }

            $cancelWindowMinutes = (int) config('betting.cancellation.window_minutes', 60);
            $placedAt = $bet->placed_at ?? $bet->created_at;
            if (! $placedAt) {
                throw new RuntimeException('Bet cancellation window has expired.');
            }

            $cancelDeadline = $placedAt->copy()->addMinutes($cancelWindowMinutes);
            if (now()->greaterThanOrEqualTo($cancelDeadline)) {
                throw new RuntimeException('Bet can only be cancelled within '.$cancelWindowMinutes.' minutes of placement.');
            }

            $bet->status = Bet::STATUS_CANCELLED;
            $bet->cancelled_at = now();
            $bet->save();

            $this->applyWalletTransactionAction->execute(
                user: $user,
                type: WalletTransaction::TYPE_REFUND,
                amount: (int) $bet->stake_points,
                uniqueKey: 'bet.refund.'.$bet->id.'.'.$idempotencyKey,
                refType: WalletTransaction::REF_TYPE_BET,
                refId: (string) $bet->id,
                metadata: [
                    'bet_id' => $bet->id,
                    'match_id' => $bet->match_id,
                    'reason' => 'cancel_within_window_before_lock',
                ],
                initialBalanceIfMissing: 0,
            );

            $this->storeAuditLogAction->execute(
                action: 'bets.cancelled',
                actor: $user,
                target: $bet,
                context: [
                    'bet_id' => $bet->id,
                    'match_id' => $bet->match_id,
                    'idempotency_key' => $idempotencyKey,
                ],
            );

            return [
                'bet' => $bet->fresh(['match:id,match_key,home_team,away_team,starts_at,status,result']),
                'idempotent' => false,
            ];
        });
    }
}
