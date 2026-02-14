<?php
declare(strict_types=1);

namespace App\Services;

use App\Enums\MarketStatus;
use App\Enums\PointTransactionType;
use App\Enums\TicketStatus;
use App\Exceptions\InvalidTicketSelectionException;
use App\Exceptions\MatchLockedException;
use App\Exceptions\MatchNotOpenException;
use App\Exceptions\StakeLimitException;
use App\Exceptions\TicketAlreadyExistsException;
use App\Models\EsportMatch;
use App\Models\MarketOption;
use App\Models\Ticket;
use App\Models\TicketSelection;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TicketService
{
    public function __construct(
        private readonly PointService $pointService,
        private readonly OddsService $oddsService
    ) {
    }

    /**
     * @param  array<int, int|string>  $selections
     */
    public function createTicket(User $user, EsportMatch $match, int $stake, array $selections): Ticket
    {
        $this->assertStakeWithinLimits($stake);

        if (!$match->isOpen()) {
            throw new MatchNotOpenException();
        }

        if ($match->isBetLockPassed()) {
            throw new MatchLockedException();
        }

        $optionIds = collect($selections)->map(fn ($id): int => (int) $id)->filter()->values();
        if ($optionIds->isEmpty()) {
            throw new InvalidTicketSelectionException('Au moins une selection est requise.');
        }

        /** @var Collection<int, MarketOption> $options */
        $options = MarketOption::query()
            ->with('market:id,match_id,status')
            ->whereIn('id', $optionIds->all())
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        if ($options->count() !== $optionIds->count()) {
            throw new InvalidTicketSelectionException('Une ou plusieurs options sont introuvables.');
        }

        $marketIds = [];
        $snapshots = [];
        foreach ($optionIds as $optionId) {
            $option = $options->get($optionId);
            if ($option === null || $option->market === null) {
                throw new InvalidTicketSelectionException('Selection invalide.');
            }

            if ((int) $option->market->match_id !== (int) $match->id) {
                throw new InvalidTicketSelectionException('Les selections doivent appartenir au meme match.');
            }

            if ($option->market->status !== MarketStatus::Open) {
                throw new InvalidTicketSelectionException('Certains markets ne sont plus ouverts.');
            }

            $marketId = (int) $option->market_id;
            if (in_array($marketId, $marketIds, true)) {
                throw new InvalidTicketSelectionException('Une seule selection par market est autorisee.');
            }

            $marketIds[] = $marketId;
            $snapshots[] = [
                'option_id' => (int) $option->id,
                'market_id' => $marketId,
                'odds_decimal' => (float) $option->odds_decimal,
                'popularity_weight' => $option->popularity_weight !== null ? (float) $option->popularity_weight : null,
            ];
        }

        $totalOdds = $this->oddsService->computeTotalOdds($snapshots);
        $popFactor = $this->oddsService->computePopularityFactor($snapshots);
        $potentialPayout = $this->oddsService->computePotentialPayout($stake, $totalOdds, $popFactor);

        try {
            /** @var Ticket $ticket */
            $ticket = DB::transaction(function () use ($user, $match, $stake, $snapshots, $totalOdds, $potentialPayout): Ticket {
                $lockedMatch = EsportMatch::query()->whereKey($match->id)->lockForUpdate()->firstOrFail();
                if (!$lockedMatch->isOpen()) {
                    throw new MatchNotOpenException();
                }

                if ($lockedMatch->isBetLockPassed()) {
                    throw new MatchLockedException();
                }

                if ((bool) config('betting.one_ticket_per_match', true)) {
                    $exists = Ticket::query()
                        ->where('user_id', $user->id)
                        ->where('match_id', $lockedMatch->id)
                        ->exists();

                    if ($exists) {
                        throw new TicketAlreadyExistsException();
                    }
                }

                $this->pointService->removePoints(
                    user: $user,
                    amount: $stake,
                    type: PointTransactionType::TicketStake->value,
                    description: 'Mise ticket match #'.$lockedMatch->id,
                    referenceId: (int) $lockedMatch->id,
                    referenceType: 'match',
                    idempotencyKey: 'ticket-stake:user-'.$user->id.':match-'.$lockedMatch->id
                );

                $ticket = Ticket::query()->create([
                    'user_id' => $user->id,
                    'match_id' => $lockedMatch->id,
                    'stake_points' => $stake,
                    'total_odds_decimal' => $totalOdds,
                    'potential_payout_points' => $potentialPayout,
                    'status' => TicketStatus::Pending,
                    'locked_at' => now(),
                ]);

                foreach ($snapshots as $snapshot) {
                    TicketSelection::query()->create([
                        'ticket_id' => $ticket->id,
                        'market_id' => $snapshot['market_id'],
                        'option_id' => $snapshot['option_id'],
                        'odds_decimal_snapshot' => $snapshot['odds_decimal'],
                    ]);
                }

                return $ticket->load(['selections.option', 'selections.market']);
            });
        } catch (QueryException $exception) {
            if (str_contains(strtolower((string) $exception->getMessage()), 'tickets_user_id_match_id_unique')) {
                throw new TicketAlreadyExistsException();
            }

            throw $exception;
        }

        return $ticket;
    }

    private function assertStakeWithinLimits(int $stake): void
    {
        $min = (int) config('betting.stake_min', 10);
        $max = (int) config('betting.stake_max', 20000);

        if ($stake < $min || $stake > $max) {
            throw new StakeLimitException('La mise doit etre comprise entre '.$min.' et '.$max.' points.');
        }
    }
}

