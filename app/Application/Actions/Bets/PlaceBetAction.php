<?php

namespace App\Application\Actions\Bets;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\Bet;
use App\Models\EsportMatch;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PlaceBetAction
{
    public function __construct(
        private readonly ApplyWalletTransactionAction $applyWalletTransactionAction,
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{bet: Bet, idempotent: bool}
     */
    public function execute(User $user, array $payload): array
    {
        try {
            return DB::transaction(function () use ($user, $payload) {
                $match = EsportMatch::query()
                    ->whereKey((int) $payload['match_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($match->status !== EsportMatch::STATUS_SCHEDULED || $match->settled_at !== null) {
                    throw new RuntimeException('Match is not open for betting.');
                }

                $lockAt = $match->locked_at ?? $match->starts_at;
                if ($lockAt && now()->greaterThanOrEqualTo($lockAt)) {
                    throw new RuntimeException('Betting is closed for this match.');
                }

                $idempotencyKey = (string) $payload['idempotency_key'];

                $existingByKey = Bet::query()
                    ->where('user_id', $user->id)
                    ->where('idempotency_key', $idempotencyKey)
                    ->lockForUpdate()
                    ->first();

                if ($existingByKey) {
                    return [
                        'bet' => $existingByKey->fresh(['match:id,match_key,home_team,away_team,starts_at,status']),
                        'idempotent' => true,
                    ];
                }

                $existingByMatch = Bet::query()
                    ->where('user_id', $user->id)
                    ->where('match_id', $match->id)
                    ->where('market_key', 'WINNER')
                    ->lockForUpdate()
                    ->first();

                if ($existingByMatch) {
                    throw new RuntimeException('Bet already exists for this match.');
                }

                $stakePoints = (int) $payload['stake_points'];
                $prediction = (string) $payload['prediction'];

                $multiplier = $prediction === Bet::PREDICTION_DRAW ? 3 : 2;
                $potentialPayout = $stakePoints * $multiplier;
                $selectionKey = match ($prediction) {
                    Bet::PREDICTION_HOME => Bet::SELECTION_TEAM_A,
                    Bet::PREDICTION_AWAY => Bet::SELECTION_TEAM_B,
                    default => Bet::SELECTION_DRAW,
                };

                $bet = Bet::query()->create([
                    'user_id' => $user->id,
                    'match_id' => $match->id,
                    'market_key' => 'WINNER',
                    'selection_key' => $selectionKey,
                    'stake' => $stakePoints,
                    'odds_snapshot' => $multiplier,
                    'prediction' => $prediction,
                    'stake_points' => $stakePoints,
                    'potential_payout' => $potentialPayout,
                    'settlement_points' => 0,
                    'status' => Bet::STATUS_PENDING,
                    'idempotency_key' => $idempotencyKey,
                    'placed_at' => now(),
                    'settled_at' => null,
                    'meta' => $payload['meta'] ?? null,
                ]);

                $this->applyWalletTransactionAction->execute(
                    user: $user,
                    type: WalletTransaction::TYPE_STAKE,
                    amount: -$stakePoints,
                    uniqueKey: 'bet.stake.'.$bet->id,
                    refType: WalletTransaction::REF_TYPE_BET,
                    refId: (string) $bet->id,
                    metadata: [
                        'match_id' => $match->id,
                        'prediction' => $prediction,
                    ],
                    initialBalanceIfMissing: (int) config('betting.wallet.initial_balance', 1000),
                );

                $this->storeAuditLogAction->execute(
                    action: 'bets.placed',
                    actor: $user,
                    target: $bet,
                    context: [
                        'bet_id' => $bet->id,
                        'match_id' => $match->id,
                        'prediction' => $prediction,
                        'stake_points' => $stakePoints,
                        'idempotency_key' => $idempotencyKey,
                    ],
                );

                return [
                    'bet' => $bet->fresh(['match:id,match_key,home_team,away_team,starts_at,status']),
                    'idempotent' => false,
                ];
            });
        } catch (QueryException $exception) {
            $message = $exception->getMessage();

            $isIdempotencyCollision = str_contains($message, 'bets_user_idempotency_unique')
                || str_contains($message, 'UNIQUE constraint failed: bets.user_id, bets.idempotency_key');

            if ($isIdempotencyCollision) {
                $existing = Bet::query()
                    ->where('user_id', $user->id)
                    ->where('idempotency_key', (string) $payload['idempotency_key'])
                    ->firstOrFail();

                return [
                    'bet' => $existing->fresh(['match:id,match_key,home_team,away_team,starts_at,status']),
                    'idempotent' => true,
                ];
            }

            $isSingleBetPerMatchCollision = str_contains($message, 'bets_user_match_unique')
                || str_contains($message, 'UNIQUE constraint failed: bets.user_id, bets.match_id');

            if ($isSingleBetPerMatchCollision) {
                throw new RuntimeException('Bet already exists for this match.');
            }

            $isSingleBetPerMarketCollision = str_contains($message, 'bets_user_match_market_unique')
                || str_contains($message, 'UNIQUE constraint failed: bets.user_id, bets.match_id, bets.market_key');

            if ($isSingleBetPerMarketCollision) {
                throw new RuntimeException('Bet already exists for this match.');
            }

            throw $exception;
        }
    }
}
