<?php

namespace App\Http\Controllers\Web;

use App\Application\Actions\Bets\PlaceBetAction;
use App\Domain\Betting\Support\MatchMarketCatalog;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\PlaceMatchBetRequest;
use App\Models\Bet;
use App\Models\EsportMatch;
use App\Services\MatchBettingCommunityService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class MatchPageController extends Controller
{
    public function index(Request $request, MatchMarketCatalog $matchMarketCatalog): View
    {
        $tab = $this->resolveTab((string) $request->query('tab', 'upcoming'));
        $game = blank($request->query('game')) ? 'all' : strtolower(trim((string) $request->query('game')));
        $eventType = blank($request->query('event_type')) ? 'all' : strtolower(trim((string) $request->query('event_type')));
        $search = trim((string) $request->query('q', ''));

        $filteredBaseQuery = $this->buildIndexBaseQuery($game, $eventType, $search);

        $tabCounts = [
            'upcoming' => (clone $filteredBaseQuery)
                ->whereIn('status', [EsportMatch::STATUS_SCHEDULED, EsportMatch::STATUS_LOCKED])
                ->count(),
            'live' => (clone $filteredBaseQuery)
                ->where('status', EsportMatch::STATUS_LIVE)
                ->count(),
            'finished' => (clone $filteredBaseQuery)
                ->whereIn('status', [
                    EsportMatch::STATUS_FINISHED,
                    EsportMatch::STATUS_SETTLED,
                    EsportMatch::STATUS_CANCELLED,
                ])
                ->count(),
        ];

        $matchesQuery = clone $filteredBaseQuery;

        if ($tab === 'live') {
            $matchesQuery->where('status', EsportMatch::STATUS_LIVE)
                ->orderByDesc('starts_at');
        } elseif ($tab === 'finished') {
            $matchesQuery->whereIn('status', [
                EsportMatch::STATUS_FINISHED,
                EsportMatch::STATUS_SETTLED,
                EsportMatch::STATUS_CANCELLED,
            ])
                ->orderByDesc('finished_at')
                ->orderByDesc('settled_at')
                ->orderByDesc('id');
        } else {
            $matchesQuery->whereIn('status', [EsportMatch::STATUS_SCHEDULED, EsportMatch::STATUS_LOCKED])
                ->orderBy('starts_at');
        }

        $matches = $matchesQuery->paginate(12)->withQueryString();
        $currentItems = collect($matches->items());
        $windowStart = max(1, $matches->currentPage() - 1);
        $windowEnd = min($matches->lastPage(), $matches->currentPage() + 1);

        return view('pages.matches.index', [
            'tab' => $tab,
            'matches' => $matches,
            'tabCounts' => $tabCounts,
            'totalMatches' => array_sum($tabCounts),
            'game' => $game,
            'eventType' => $eventType,
            'search' => $search,
            'gameOptions' => $matchMarketCatalog->gameOptions(),
            'eventTypeOptions' => $matchMarketCatalog->eventTypeOptions(),
            'matchLabelResolver' => $matchMarketCatalog,
            'sectionedMatches' => [
                'classic' => $currentItems->filter(fn (EsportMatch $match) => $match->isHeadToHead() && $match->game_key !== EsportMatch::GAME_ROCKET_LEAGUE),
                'tournaments' => $currentItems->filter(fn (EsportMatch $match) => $match->isTournamentRun()),
                'rocketLeagueMatches' => $currentItems->filter(fn (EsportMatch $match) => $match->isHeadToHead() && $match->game_key === EsportMatch::GAME_ROCKET_LEAGUE),
            ],
            'windowStart' => $windowStart,
            'windowEnd' => $windowEnd,
        ]);
    }

    public function show(
        Request $request,
        int $matchId,
        MatchMarketCatalog $matchMarketCatalog,
        MatchBettingCommunityService $matchBettingCommunityService
    ): View
    {
        $match = EsportMatch::query()
            ->withCount(['bets', 'childMatches'])
            ->with([
                'parentMatch:id,event_name,compétition_name,compétition_stage,compétition_split,child_matches_unlocked_at,starts_at,status',
                'childMatches' => fn ($query) => $query
                    ->withCount('bets')
                    ->orderBy('starts_at'),
                'markets' => fn ($query) => $query->where('is_active', true)->with('selections'),
                'settlement:id,match_id,payout_total,won_count,lost_count,void_count,processused_at',
            ])
            ->findOrFail($matchId);

        $user = auth()->user();
        $myBetsByMarket = collect();
        $markets = $match->markets;
        $betIsOpen = $this->isBettingOpen($match);

        if ($markets->isEmpty()) {
            $markets = collect($matchMarketCatalog->buildDefaultMarkets($match))
                ->map(function (array $market) {
                    return (object) [
                        'key' => $market['key'],
                        'title' => $market['title'],
                        'is_active' => $market['is_active'],
                        'selections' => collect($market['selections'])->map(fn (array $selection) => (object) $selection),
                    ];
                });
        }

        if ($user) {
            $myBetsByMarket = Bet::query()
                ->where('user_id', $user->id)
                ->where('match_id', $match->id)
                ->latest('id')
                ->get()
                ->keyBy('market_key');
        }

        $betCommunity = $matchBettingCommunityService->build(
            match: $match,
            markets: $markets,
            matchMarketCatalog: $matchMarketCatalog,
            betIsOpen: $betIsOpen,
            marketFilter: (string) $request->query('bettors_market', 'all'),
        );

        $relatedMatches = EsportMatch::query()
            ->whereKeyNot($match->id)
            ->when($match->game_key, fn (Builder $query) => $query->where('game_key', $match->game_key))
            ->withCount('bets')
            ->orderByRaw("case when status in ('scheduled', 'locked', 'live') then 0 else 1 end")
            ->orderBy('starts_at')
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        return view('pages.matches.show', [
            'match' => $match,
            'markets' => $markets,
            'myBetsByMarket' => $myBetsByMarket,
            'walletBalance' => (int) ($user->wallet?->balance ?? config('betting.wallet.initial_balance', 1000)),
            'betIsOpen' => $betIsOpen,
            'betCommunity' => $betCommunity,
            'relatedMatches' => $relatedMatches,
            'gameLabel' => $matchMarketCatalog->labelForGame($match->game_key),
            'eventTypeLabel' => $matchMarketCatalog->labelForEventType($match->event_type),
            'matchLabelResolver' => $matchMarketCatalog,
        ]);
    }

    public function placeBet(
        PlaceMatchBetRequest $request,
        int $matchId,
        PlaceBetAction $placeBetAction
    ): RedirectResponse {
        $validated = $request->validated();

        try {
            $result = $placeBetAction->execute(
                user: $request->user(),
                payload: [
                    'match_id' => $matchId,
                    'market_key' => $validated['market_key'] ?? null,
                    'selection_key' => $validated['selection_key'],
                    'stake_points' => (int) $validated['stake_points'],
                    'idempotency_key' => (string) $validated['idempotency_key'],
                    'meta' => ['source' => 'web'],
                ],
            );
        } catch (ModelNotFoundException) {
            return back()->with('error', 'Match introuvable.');
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        if ($result['idempotent']) {
            return back()->with('success', 'Pari deja enregistre (replay idempotent).');
        }

        return back()->with('success', 'Pari place avec succes.');
    }

    private function isBettingOpen(EsportMatch $match): bool
    {
        if ($match->status !== EsportMatch::STATUS_SCHEDULED || $match->settled_at) {
            return false;
        }

        if ($match->parentMatch && ! $match->parentMatch->hasUnlockedChildMatches()) {
            return false;
        }

        $lockAt = $match->locked_at ?? $match->starts_at;

        return ! $lockAt || now()->lt($lockAt);
    }

    private function resolveTab(string $tab): string
    {
        return in_array($tab, ['upcoming', 'live', 'finished'], true) ? $tab : 'upcoming';
    }

    private function buildIndexBaseQuery(string $game, string $eventType, string $search): Builder
    {
        return EsportMatch::query()
            ->withCount(['bets', 'childMatches'])
            ->with('parentMatch:id,event_name,compétition_name')
            ->when($game !== 'all', fn (Builder $query) => $query->where('game_key', $game))
            ->when($eventType !== 'all', fn (Builder $query) => $query->where('event_type', $eventType))
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $nested) use ($search) {
                    $nested->where('event_name', 'like', '%'.$search.'%')
                        ->orWhere('compétition_name', 'like', '%'.$search.'%')
                        ->orWhere('compétition_stage', 'like', '%'.$search.'%')
                        ->orWhere('team_a_name', 'like', '%'.$search.'%')
                        ->orWhere('team_b_name', 'like', '%'.$search.'%')
                        ->orWhere('home_team', 'like', '%'.$search.'%')
                        ->orWhere('away_team', 'like', '%'.$search.'%');
                });
            });
    }
}
