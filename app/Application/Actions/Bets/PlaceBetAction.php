<?php

namespace App\Application\Actions\Bets;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Application\Actions\Matches\SyncMatchMarketsAction;
use App\Models\Bet;
use App\Models\EsportMatch;
use App\Models\MatchMarket;
use App\Models\MatchSelection;
use App\Models\RewardWalletTransaction;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\PlatformPointService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PlaceBetAction
{
    public function __construct(
        private readonly PlatformPointService $platformPointService,
        private readonly StoreAuditLogAction $storeAuditLogAction,
        private readonly SyncMatchMarketsAction $syncMatchMarketsAction
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
                    ->with([
                        'parentMatch:id,child_matches_unlocked_at',
                        'markets' => fn ($query) => $query->where('is_active', true)->with('selections'),
                    ])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($match->status !== EsportMatch::STATUS_SCHEDULED || $match->settled_at !== null) {
                    throw new RuntimeException('Match is not open for betting.');
                }

                if ($match->parentMatch && ! $match->parentMatch->hasUnlockedChildMatches()) {
                    throw new RuntimeException('Tournament match phase is not unlocked yet.');
                }

                $lockAt = $match->locked_at ?? $match->starts_at;
                if ($lockAt && now()->greaterThanOrEqualTo($lockAt)) {
                    throw new RuntimeException('Betting is closed for this match.');
                }

                if ($match->markets->isEmpty()) {
                    $this->syncMatchMarketsAction->execute($match, null, $match->toArray());
                    $match->load([
                        'markets' => fn ($query) => $query->where('is_active', true)->with('selections'),
                    ]);
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
                    ->where('market_key', $this->resolveMarketKey($payload))
                    ->lockForUpdate()
                    ->first();

                if ($existingByMatch) {
                    throw new RuntimeException('Bet already exists for this match.');
                }

                $stakePoints = (int) $payload['stake_points'];
                [$market, $selection] = $this->resolveSelection($match, $payload);
                $selectionKey = (string) $selection->key;
                $prediction = $this->resolvePredictionValue($market->key, $selectionKey);
                $multiplier = round((float) $selection->odds, 3);
                $potentialPayout = (int) round($stakePoints * $multiplier, 0);

                $bet = Bet::query()->create([
                    'user_id' => $user->id,
                    'match_id' => $match->id,
                    'market_key' => $market->key,
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

                $this->platformPointService->débit(
                    user: $user,
                    amount: $stakePoints,
                    type: RewardWalletTransaction::TYPE_BET_STAKE,
                    uniqueKey: 'bet.stake.'.$bet->id,
                    refType: WalletTransaction::REF_TYPE_BET,
                    refId: (string) $bet->id,
                    meta: [
                        'match_id' => $match->id,
                        'market_key' => $market->key,
                        'selection_key' => $selectionKey,
                        'prediction' => $prediction,
                    ],
                    mirrorLegacyBetLedger: true,
                    legacyWalletType: WalletTransaction::TYPE_STAKE,
                    initialBalanceIfMissing: (int) config('betting.wallet.initial_balance', 1000),
                );

                $this->storeAuditLogAction->execute(
                    action: 'bets.placed',
                    actor: $user,
                    target: $bet,
                    context: [
                        'bet_id' => $bet->id,
                        'match_id' => $match->id,
                        'market_key' => $market->key,
                        'selection_key' => $selectionKey,
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

    /**
     * @param array<string, mixed> $payload
     */
    private function resolveMarketKey(array $payload): string
    {
        $marketKey = trim((string) ($payload['market_key'] ?? ''));

        return $marketKey !== '' ? strtoupper($marketKey) : MatchMarket::KEY_WINNER;
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{0: MatchMarket, 1: MatchSelection}
     */
    private function resolveSelection(EsportMatch $match, array $payload): array
    {
        $marketKey = $this->resolveMarketKey($payload);
        $selectionKey = strtolower(trim((string) ($payload['selection_key'] ?? '')));

        if ($selectionKey === '') {
            $selectionKey = match ((string) ($payload['prediction'] ?? '')) {
                Bet::PREDICTION_HOME => MatchSelection::KEY_TEAM_A,
                Bet::PREDICTION_AWAY => MatchSelection::KEY_TEAM_B,
                Bet::PREDICTION_DRAW => MatchSelection::KEY_DRAW,
                default => '',
            };
        }

        $market = $match->markets->firstWhere('key', $marketKey);
        if (! $market) {
            throw new RuntimeException('Requested market is not available for this match.');
        }

        $selection = $market->selections->firstWhere('key', $selectionKey);
        if (! $selection) {
            throw new RuntimeException('Requested selection is not available for this market.');
        }

        return [$market, $selection];
    }

    private function resolvePredictionValue(string $marketKey, string $selectionKey): string
    {
        if ($marketKey !== MatchMarket::KEY_WINNER) {
            return $selectionKey;
        }

        return match ($selectionKey) {
            MatchSelection::KEY_TEAM_A => Bet::PREDICTION_HOME,
            MatchSelection::KEY_TEAM_B => Bet::PREDICTION_AWAY,
            MatchSelection::KEY_DRAW => Bet::PREDICTION_DRAW,
            default => $selectionKey,
        };
    }
}
