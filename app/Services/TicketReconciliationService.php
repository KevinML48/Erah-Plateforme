<?php
declare(strict_types=1);

namespace App\Services;

use App\Enums\PointTransactionType;
use App\Enums\SelectionStatus;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\TicketSelection;
use Illuminate\Support\Facades\DB;

class TicketReconciliationService
{
    public function __construct(
        private readonly PointService $pointService,
        private readonly OddsService $oddsService
    ) {
    }

    public function reconcileTicket(Ticket $ticket): Ticket
    {
        return DB::transaction(function () use ($ticket): Ticket {
            $lockedTicket = Ticket::query()
                ->with('selections')
                ->whereKey($ticket->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedTicket->status !== TicketStatus::Pending) {
                return $lockedTicket;
            }

            $statuses = $lockedTicket->selections->pluck('status')->all();
            if (in_array(SelectionStatus::Pending, $statuses, true)) {
                return $lockedTicket;
            }

            $hasLost = in_array(SelectionStatus::Lost, $statuses, true);
            $hasWon = in_array(SelectionStatus::Won, $statuses, true);
            $allVoid = !in_array(SelectionStatus::Won, $statuses, true) && !in_array(SelectionStatus::Lost, $statuses, true);

            if ($hasLost) {
                $lockedTicket->status = TicketStatus::Lost;
                $lockedTicket->settled_at = now();
                $lockedTicket->payout_points = 0;
                $lockedTicket->refunded_points = 0;
                $lockedTicket->save();

                return $lockedTicket;
            }

            if ($allVoid) {
                $refund = (int) $lockedTicket->stake_points;

                $lockedTicket->status = TicketStatus::Void;
                $lockedTicket->settled_at = now();
                $lockedTicket->payout_points = 0;
                $lockedTicket->refunded_points = $refund;
                $lockedTicket->save();

                $this->pointService->addPoints(
                    user: $lockedTicket->user()->firstOrFail(),
                    amount: $refund,
                    type: PointTransactionType::TicketRefund->value,
                    description: 'Refund ticket #'.$lockedTicket->id,
                    referenceId: (int) $lockedTicket->id,
                    referenceType: 'ticket',
                    idempotencyKey: 'ticket-refund:'.$lockedTicket->id
                );

                return $lockedTicket;
            }

            if ($hasWon) {
                $wonSelections = $lockedTicket->selections
                    ->where('status', SelectionStatus::Won)
                    ->values();

                $oddsSnapshots = $wonSelections->map(static fn (TicketSelection $selection): array => [
                    'odds_decimal' => (float) $selection->odds_decimal_snapshot,
                    'popularity_weight' => null,
                ])->all();

                $totalOdds = $this->oddsService->computeTotalOdds($oddsSnapshots);
                $payout = $this->oddsService->computePotentialPayout((int) $lockedTicket->stake_points, $totalOdds, 1.0);

                $lockedTicket->status = TicketStatus::Won;
                $lockedTicket->settled_at = now();
                $lockedTicket->payout_points = $payout;
                $lockedTicket->refunded_points = 0;
                $lockedTicket->save();

                $this->pointService->addPoints(
                    user: $lockedTicket->user()->firstOrFail(),
                    amount: $payout,
                    type: PointTransactionType::TicketPayout->value,
                    description: 'Payout ticket #'.$lockedTicket->id,
                    referenceId: (int) $lockedTicket->id,
                    referenceType: 'ticket',
                    idempotencyKey: 'ticket-payout:'.$lockedTicket->id
                );
            }

            return $lockedTicket;
        });
    }

    public function reconcileTicketsForMatch(int $matchId): void
    {
        Ticket::query()
            ->where('match_id', $matchId)
            ->where('status', TicketStatus::Pending)
            ->orderBy('id')
            ->chunkById(100, function ($tickets): void {
                foreach ($tickets as $ticket) {
                    $this->reconcileTicket($ticket);
                }
            });
    }

    public function reconcileTicketsForMarket(int $marketId): void
    {
        Ticket::query()
            ->where('status', TicketStatus::Pending)
            ->whereHas('selections', function ($query) use ($marketId): void {
                $query->where('market_id', $marketId);
            })
            ->orderBy('id')
            ->chunkById(100, function ($tickets): void {
                foreach ($tickets as $ticket) {
                    $this->reconcileTicket($ticket);
                }
            });
    }
}

