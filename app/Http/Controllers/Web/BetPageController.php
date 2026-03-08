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

        $query = Bet::query()
            ->where('user_id', auth()->id())
            ->with('match.markets.selections')
            ->orderByDesc('id');

        if ($tab === 'settled') {
            $query->whereIn('status', [
                Bet::STATUS_WON,
                Bet::STATUS_LOST,
                Bet::STATUS_VOID,
                Bet::STATUS_CANCELLED,
            ]);
        } else {
            $tab = 'active';
            $query->whereIn('status', [Bet::STATUS_PENDING, Bet::STATUS_PLACED]);
        }

        return view('pages.bets.index', [
            'tab' => $tab,
            'bets' => $query->paginate(12)->withQueryString(),
            'matchLabelResolver' => $matchMarketCatalog,
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
