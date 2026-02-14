<?php
declare(strict_types=1);

namespace App\Services;

use App\Enums\MarketStatus;
use App\Enums\MatchStatus;
use App\Enums\SelectionStatus;
use App\Models\AuditLog;
use App\Models\EsportMatch;
use App\Models\Market;
use App\Models\MarketOption;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SettlementService
{
    public function __construct(
        private readonly TicketReconciliationService $reconciliationService
    ) {
    }

    public function settleMarket(Market $market, ?int $winnerOptionId, ?int $actorUserId = null): Market
    {
        return DB::transaction(function () use ($market, $winnerOptionId, $actorUserId): Market {
            $lockedMarket = Market::query()
                ->with('options:id,market_id')
                ->whereKey($market->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (in_array($lockedMarket->status, [MarketStatus::Settled, MarketStatus::Void], true)) {
                return $lockedMarket;
            }

            if ($winnerOptionId === null) {
                $lockedMarket->status = MarketStatus::Void;
                $lockedMarket->settled_at = now();
                $lockedMarket->save();

                MarketOption::query()
                    ->where('market_id', $lockedMarket->id)
                    ->update(['is_winner' => null, 'settled_at' => now()]);

                $selectionStatus = SelectionStatus::Void->value;
            } else {
                $winnerExists = $lockedMarket->options->contains('id', $winnerOptionId);
                if (!$winnerExists) {
                    throw new InvalidArgumentException('winner_option_id invalide pour ce market.');
                }

                $lockedMarket->status = MarketStatus::Settled;
                $lockedMarket->settled_at = now();
                $lockedMarket->save();

                MarketOption::query()
                    ->where('market_id', $lockedMarket->id)
                    ->update([
                        'is_winner' => DB::raw('CASE WHEN id = '.(int) $winnerOptionId.' THEN 1 ELSE 0 END'),
                        'settled_at' => now(),
                    ]);

                $selectionStatus = null;
            }

            if ($selectionStatus !== null) {
                DB::table('ticket_selections')
                    ->where('market_id', $lockedMarket->id)
                    ->where('status', SelectionStatus::Pending->value)
                    ->update(['status' => $selectionStatus, 'updated_at' => now()]);
            } else {
                DB::table('ticket_selections')
                    ->where('market_id', $lockedMarket->id)
                    ->where('status', SelectionStatus::Pending->value)
                    ->update([
                        'status' => DB::raw('CASE WHEN option_id = '.(int) $winnerOptionId.' THEN "'.SelectionStatus::Won->value.'" ELSE "'.SelectionStatus::Lost->value.'" END'),
                        'updated_at' => now(),
                    ]);
            }

            AuditLog::query()->create([
                'actor_user_id' => $actorUserId,
                'action' => 'market.settle',
                'entity_type' => 'market',
                'entity_id' => (int) $lockedMarket->id,
                'payload_json' => [
                    'winner_option_id' => $winnerOptionId,
                    'status' => $lockedMarket->status->value,
                ],
            ]);

            $this->reconciliationService->reconcileTicketsForMarket((int) $lockedMarket->id);
            $this->markMatchCompletedIfAllMarketsSettled((int) $lockedMarket->match_id);

            return $lockedMarket->refresh();
        });
    }

    /**
     * @param  array<int, array{market_id:int, winner_option_id:int|null}>  $marketResults
     */
    public function settleMatch(EsportMatch $match, array $marketResults, ?int $actorUserId = null): void
    {
        DB::transaction(function () use ($match, $marketResults, $actorUserId): void {
            EsportMatch::query()
                ->whereKey($match->id)
                ->lockForUpdate()
                ->firstOrFail();

            foreach ($marketResults as $result) {
                $market = Market::query()
                    ->where('match_id', $match->id)
                    ->whereKey((int) $result['market_id'])
                    ->first();

                if (!$market) {
                    continue;
                }

                $this->settleMarket(
                    market: $market,
                    winnerOptionId: $result['winner_option_id'],
                    actorUserId: $actorUserId
                );
            }

            AuditLog::query()->create([
                'actor_user_id' => $actorUserId,
                'action' => 'match.settle_bulk',
                'entity_type' => 'match',
                'entity_id' => (int) $match->id,
                'payload_json' => ['markets_count' => count($marketResults)],
            ]);
        });
    }

    private function markMatchCompletedIfAllMarketsSettled(int $matchId): void
    {
        $remaining = Market::query()
            ->where('match_id', $matchId)
            ->whereNotIn('status', [MarketStatus::Settled, MarketStatus::Void])
            ->exists();

        if ($remaining) {
            return;
        }

        EsportMatch::query()
            ->whereKey($matchId)
            ->whereNotIn('status', [MatchStatus::Completed, MatchStatus::Cancelled])
            ->update([
                'status' => MatchStatus::Completed,
                'completed_at' => now(),
            ]);
    }
}

