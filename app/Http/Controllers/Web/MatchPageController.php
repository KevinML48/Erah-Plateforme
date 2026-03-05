<?php

namespace App\Http\Controllers\Web;

use App\Application\Actions\Bets\PlaceBetAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\PlaceMatchBetRequest;
use App\Models\Bet;
use App\Models\EsportMatch;
use App\Models\MatchMarket;
use App\Models\MatchSelection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use RuntimeException;

class MatchPageController extends Controller
{
    public function index(Request $request): View
    {
        $tab = (string) $request->query('tab', 'upcoming');

        $tabCounts = [
            'upcoming' => EsportMatch::query()
                ->whereIn('status', [EsportMatch::STATUS_SCHEDULED, EsportMatch::STATUS_LOCKED])
                ->count(),
            'live' => EsportMatch::query()
                ->where('status', EsportMatch::STATUS_LIVE)
                ->count(),
            'finished' => EsportMatch::query()
                ->whereIn('status', [
                    EsportMatch::STATUS_FINISHED,
                    EsportMatch::STATUS_SETTLED,
                    EsportMatch::STATUS_CANCELLED,
                ])
                ->count(),
        ];

        $query = EsportMatch::query()->withCount('bets');

        if ($tab === 'live') {
            $query->where('status', EsportMatch::STATUS_LIVE)
                ->orderByDesc('starts_at');
        } elseif ($tab === 'finished') {
            $query->whereIn('status', [
                EsportMatch::STATUS_FINISHED,
                EsportMatch::STATUS_SETTLED,
                EsportMatch::STATUS_CANCELLED,
            ])
                ->orderByDesc('finished_at')
                ->orderByDesc('settled_at')
                ->orderByDesc('id');
        } else {
            $tab = 'upcoming';
            $query->whereIn('status', [EsportMatch::STATUS_SCHEDULED, EsportMatch::STATUS_LOCKED])
                ->orderBy('starts_at');
        }

        return view('pages.matches.index', [
            'tab' => $tab,
            'matches' => $query->paginate(12)->withQueryString(),
            'tabCounts' => $tabCounts,
            'totalMatches' => array_sum($tabCounts),
        ]);
    }

    public function show(int $matchId): View
    {
        $match = EsportMatch::query()
            ->with([
                'markets' => fn ($query) => $query->where('key', MatchMarket::KEY_WINNER)->with('selections'),
            ])
            ->withCount('bets')
            ->findOrFail($matchId);

        $user = auth()->user();
        $myBet = null;

        if ($user) {
            $myBet = Bet::query()
                ->where('user_id', $user->id)
                ->where('match_id', $match->id)
                ->latest('id')
                ->first();
        }

        $relatedMatches = EsportMatch::query()
            ->whereKeyNot($match->id)
            ->withCount('bets')
            ->orderByRaw("case when status in ('scheduled', 'locked', 'live') then 0 else 1 end")
            ->orderBy('starts_at')
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        return view('pages.matches.show', [
            'match' => $match,
            'options' => $this->resolveWinnerOptions($match),
            'myBet' => $myBet,
            'walletBalance' => (int) ($user->wallet?->balance ?? config('betting.wallet.initial_balance', 1000)),
            'betIsOpen' => $this->isBettingOpen($match),
            'relatedMatches' => $relatedMatches,
        ]);
    }

    public function placeBet(
        PlaceMatchBetRequest $request,
        int $matchId,
        PlaceBetAction $placeBetAction
    ): RedirectResponse {
        $validated = $request->validated();

        $prediction = match ((string) $validated['selection_key']) {
            MatchSelection::KEY_TEAM_A => Bet::PREDICTION_HOME,
            MatchSelection::KEY_TEAM_B => Bet::PREDICTION_AWAY,
            default => Bet::PREDICTION_DRAW,
        };

        try {
            $result = $placeBetAction->execute(
                user: $request->user(),
                payload: [
                    'match_id' => $matchId,
                    'prediction' => $prediction,
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

        $lockAt = $match->locked_at ?? $match->starts_at;

        return ! $lockAt || now()->lt($lockAt);
    }

    /**
     * @return Collection<int, array{key: string, label: string, odds: string}>
     */
    private function resolveWinnerOptions(EsportMatch $match): Collection
    {
        $market = $match->markets->first();
        if ($market && $market->selections->isNotEmpty()) {
            return $market->selections
                ->sortBy('id')
                ->map(fn (MatchSelection $selection) => [
                    'key' => $selection->key,
                    'label' => $selection->label,
                    'odds' => number_format((float) $selection->odds, 3),
                ])
                ->values();
        }

        return collect([
            [
                'key' => MatchSelection::KEY_TEAM_A,
                'label' => (string) ($match->team_a_name ?: $match->home_team),
                'odds' => number_format((float) config('betting.odds.winner_fixed', 2.0), 3),
            ],
            [
                'key' => MatchSelection::KEY_TEAM_B,
                'label' => (string) ($match->team_b_name ?: $match->away_team),
                'odds' => number_format((float) config('betting.odds.winner_fixed', 2.0), 3),
            ],
            [
                'key' => MatchSelection::KEY_DRAW,
                'label' => 'Draw',
                'odds' => number_format((float) config('betting.odds.draw_fixed', 3.0), 3),
            ],
        ]);
    }
}
