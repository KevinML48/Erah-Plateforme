<?php

namespace App\Application\Actions\Bets;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Application\Actions\Notifications\NotifyAction;
use App\Domain\Betting\Support\MatchOutcomeResolver;
use App\Domain\Notifications\Enums\NotificationCategory;
use App\Models\Bet;
use App\Models\EsportMatch;
use App\Models\MatchSettlement;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SettleMatchBetsAction
{
    public function __construct(
        private readonly ApplyWalletTransactionAction $applyWalletTransactionAction,
        private readonly NotifyAction $notifyAction,
        private readonly StoreAuditLogAction $storeAuditLogAction,
        private readonly MatchOutcomeResolver $matchOutcomeResolver
    ) {
    }

    /**
     * @return array{idempotent: bool, settlement: MatchSettlement, match: EsportMatch}
     */
    public function execute(
        User $actor,
        int $matchId,
        string $result,
        string $idempotencyKey,
        ?int $teamAScore = null,
        ?int $teamBScore = null
    ): array {
        try {
            return DB::transaction(function () use ($actor, $matchId, $result, $idempotencyKey, $teamAScore, $teamBScore) {
                $match = EsportMatch::query()
                    ->whereKey($matchId)
                    ->with(['markets' => fn ($query) => $query->where('is_active', true)->with('selections')])
                    ->lockForUpdate()
                    ->firstOrFail();
                $resolved = $this->matchOutcomeResolver->resolve(
                    $match,
                    $result,
                    $teamAScore ?? $match->team_a_score,
                    $teamBScore ?? $match->team_b_score,
                );

                $existingSettlement = MatchSettlement::query()
                    ->where('match_id', $match->id)
                    ->lockForUpdate()
                    ->first();

                if ($existingSettlement) {
                    if ($existingSettlement->idempotency_key === $idempotencyKey) {
                        return [
                            'idempotent' => true,
                            'settlement' => $existingSettlement->fresh(),
                            'match' => $match->fresh(),
                        ];
                    }

                    throw new RuntimeException('Match already settled.');
                }

                if ($match->status === EsportMatch::STATUS_CANCELLED) {
                    throw new RuntimeException('Cancelled match cannot be settled.');
                }

                /** @var EloquentCollection<int, Bet> $pendingBets */
                $pendingBets = Bet::query()
                    ->where('match_id', $match->id)
                    ->where('status', Bet::STATUS_PENDING)
                    ->with('user:id,name')
                    ->lockForUpdate()
                    ->get();

                $wonCount = 0;
                $lostCount = 0;
                $voidCount = 0;
                $payoutTotal = 0;
                $now = now();

                foreach ($pendingBets as $bet) {
                    if ($resolved['is_void']) {
                        $bet->status = Bet::STATUS_VOID;
                        $bet->settlement_points = $bet->stake_points;
                        $bet->payout = $bet->stake_points;

                        $this->applyWalletTransactionAction->execute(
                            user: $bet->user,
                            type: WalletTransaction::TYPE_VOID_REFUND,
                            amount: (int) $bet->stake_points,
                            uniqueKey: 'bet.void_refund.'.$bet->id,
                            refType: WalletTransaction::REF_TYPE_BET,
                            refId: (string) $bet->id,
                            metadata: ['match_id' => $match->id]
                        );

                        $voidCount++;
                        $payoutTotal += $bet->stake_points;
                    } else {
                        $winningSelection = $resolved['resolved_markets'][$bet->market_key] ?? null;
                        if ($winningSelection === null) {
                            throw new RuntimeException('Missing market resolution for '.$bet->market_key.'.');
                        }

                        if ((string) $bet->selection_key === (string) $winningSelection) {
                            $bet->status = Bet::STATUS_WON;
                            $bet->settlement_points = $bet->potential_payout;
                            $bet->payout = $bet->potential_payout;

                            $this->applyWalletTransactionAction->execute(
                                user: $bet->user,
                                type: WalletTransaction::TYPE_PAYOUT,
                                amount: (int) $bet->potential_payout,
                                uniqueKey: 'bet.payout.'.$bet->id,
                                refType: WalletTransaction::REF_TYPE_BET,
                                refId: (string) $bet->id,
                                metadata: [
                                    'match_id' => $match->id,
                                    'market_key' => $bet->market_key,
                                    'selection_key' => $bet->selection_key,
                                ]
                            );

                            $wonCount++;
                            $payoutTotal += $bet->potential_payout;
                        } else {
                            $bet->status = Bet::STATUS_LOST;
                            $bet->settlement_points = 0;
                            $bet->payout = 0;
                            $lostCount++;
                        }
                    }

                    $bet->settled_at = $now;
                    $bet->save();

                    $this->notifyAction->execute(
                        user: $bet->user,
                        category: NotificationCategory::BET->value,
                        title: 'Pari regle',
                        message: $this->buildBetSettlementMessage($bet),
                        data: [
                            'bet_id' => $bet->id,
                            'match_id' => $match->id,
                            'market_key' => $bet->market_key,
                            'selection_key' => $bet->selection_key,
                            'status' => $bet->status,
                            'settlement_points' => $bet->settlement_points,
                        ],
                    );
                }

                $match->status = EsportMatch::STATUS_FINISHED;
                $match->result = $resolved['stored_result'];
                $match->team_a_score = $resolved['team_a_score'];
                $match->team_b_score = $resolved['team_b_score'];
                $match->finished_at = $match->finished_at ?? $now;
                $match->settled_at = $now;
                $match->updated_by = $actor->id;
                $match->save();

                $settlement = MatchSettlement::query()->create([
                    'match_id' => $match->id,
                    'idempotency_key' => $idempotencyKey,
                    'result' => $resolved['stored_result'],
                    'bets_total' => $pendingBets->count(),
                    'won_count' => $wonCount,
                    'lost_count' => $lostCount,
                    'void_count' => $voidCount,
                    'payout_total' => $payoutTotal,
                    'processed_by' => $actor->id,
                    'processed_at' => $now,
                    'meta' => [
                        'resolved_markets' => $resolved['resolved_markets'],
                        'team_a_score' => $resolved['team_a_score'],
                        'team_b_score' => $resolved['team_b_score'],
                    ],
                ]);

                $this->storeAuditLogAction->execute(
                    action: 'matches.settled',
                    actor: $actor,
                    target: $match,
                    context: [
                        'match_id' => $match->id,
                        'match_key' => $match->match_key,
                        'result' => $resolved['stored_result'],
                        'team_a_score' => $resolved['team_a_score'],
                        'team_b_score' => $resolved['team_b_score'],
                        'idempotency_key' => $idempotencyKey,
                        'bets_total' => $pendingBets->count(),
                        'won_count' => $wonCount,
                        'lost_count' => $lostCount,
                        'void_count' => $voidCount,
                        'payout_total' => $payoutTotal,
                    ],
                );

                return [
                    'idempotent' => false,
                    'settlement' => $settlement->fresh(),
                    'match' => $match->fresh(),
                ];
            });
        } catch (QueryException $exception) {
            $message = $exception->getMessage();

            $isSettlementCollision = str_contains($message, 'match_settlements_match_unique')
                || str_contains($message, 'UNIQUE constraint failed: match_settlements.match_id');

            if (! $isSettlementCollision) {
                throw $exception;
            }

            $existingSettlement = MatchSettlement::query()->where('match_id', $matchId)->firstOrFail();
            if ($existingSettlement->idempotency_key !== $idempotencyKey) {
                throw new RuntimeException('Match already settled.');
            }

            $match = EsportMatch::query()->findOrFail($matchId);

            return [
                'idempotent' => true,
                'settlement' => $existingSettlement,
                'match' => $match,
            ];
        }
    }

    private function buildBetSettlementMessage(Bet $bet): string
    {
        return match ($bet->status) {
            Bet::STATUS_WON => 'Pari gagne: +'.$bet->settlement_points.' bet_points.',
            Bet::STATUS_VOID => 'Pari annule: remboursement '.$bet->settlement_points.' bet_points.',
            default => 'Pari perdu. Aucun gain.',
        };
    }
}
