<?php

namespace App\Http\Controllers\Web;

use App\Application\Actions\Bets\CancelBetAction;
use App\Domain\Betting\Support\MatchMarketCatalog;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\CancelBetRequest;
use App\Models\Bet;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class BetPageController extends Controller
{
    public function index(Request $request, MatchMarketCatalog $matchMarketCatalog): View
    {
        $tab = (string) $request->query('tab', 'active');
        $userId = auth()->id();

        $statusGroups = [
            'active' => [Bet::STATUS_PENDING, Bet::STATUS_PLACED],
            'settled' => [
                Bet::STATUS_WON,
                Bet::STATUS_LOST,
                Bet::STATUS_VOID,
                Bet::STATUS_CANCELLED,
            ],
        ];

        $baseQuery = Bet::query()
            ->where('user_id', $userId);

        $statusCounts = [
            'active' => (clone $baseQuery)->whereIn('status', $statusGroups['active'])->count(),
            'settled' => (clone $baseQuery)->whereIn('status', $statusGroups['settled'])->count(),
            'won' => (clone $baseQuery)->where('status', Bet::STATUS_WON)->count(),
            'cancelled' => (clone $baseQuery)->where('status', Bet::STATUS_CANCELLED)->count(),
        ];

        $summary = [
            'total' => (clone $baseQuery)->count(),
            'stake_total' => (int) (clone $baseQuery)->sum('stake_points'),
            'settlement_total' => (int) (clone $baseQuery)->sum('settlement_points'),
            'active_stake_total' => (int) (clone $baseQuery)->whereIn('status', $statusGroups['active'])->sum('stake_points'),
            'pending_gain_total' => (int) (clone $baseQuery)->whereIn('status', $statusGroups['active'])->sum('potential_payout'),
            'won_total' => (int) (clone $baseQuery)->where('status', Bet::STATUS_WON)->sum('settlement_points'),
        ];

        $query = Bet::query()
            ->where('user_id', $userId)
            ->with(['match.markets.selections', 'settlement'])
            ->orderByDesc('id');

        if ($tab === 'settled') {
            $query->whereIn('status', $statusGroups['settled']);
        } else {
            $tab = 'active';
            $query->whereIn('status', $statusGroups['active']);
        }

        return view('pages.bets.index', [
            'tab' => $tab,
            'bets' => $query->paginate(12)->withQueryString(),
            'matchLabelResolver' => $matchMarketCatalog,
            'statusCounts' => $statusCounts,
            'summary' => $summary,
        ]);
    }

    public function cancel(
        CancelBetRequest $request,
        int $betId,
        CancelBetAction $cancelBetAction
    ): RedirectResponse {
        try {
            $result = $cancelBetAction->execute(
                user: $request->user(),
                betId: $betId,
                idempotencyKey: (string) $request->validated()['idempotency_key'],
            );
        } catch (ModelNotFoundException) {
            return back()->with('error', 'Pari introuvable.');
        } catch (AuthorizationException $exception) {
            return back()->with('error', $exception->getMessage());
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        if ($result['idempotent']) {
            return back()->with('success', 'Pari deja annule.');
        }

        return back()->with('success', 'Pari annule et rembourse.');
    }
}
