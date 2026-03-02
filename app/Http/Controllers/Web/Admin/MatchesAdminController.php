<?php

namespace App\Http\Controllers\Web\Admin;

use App\Application\Actions\Bets\SettleMatchBetsAction;
use App\Application\Actions\Matches\CreateMatchAction;
use App\Http\Controllers\Controller;
use App\Models\EsportMatch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class MatchesAdminController extends Controller
{
    public function index(): View
    {
        $matches = EsportMatch::query()
            ->withCount('bets')
            ->orderByDesc('id')
            ->paginate(20);

        return view('pages.admin.matches.index', [
            'matches' => $matches,
            'results' => EsportMatch::settlementResults(),
        ]);
    }

    public function create(): View
    {
        return view('pages.admin.matches.create');
    }

    public function store(Request $request, CreateMatchAction $createMatchAction): RedirectResponse
    {
        $validated = $request->validate([
            'match_key' => ['required', 'string', 'min:4', 'max:80', 'regex:/^[a-z0-9._:-]+$/'],
            'home_team' => ['required', 'string', 'min:2', 'max:120', 'different:away_team'],
            'away_team' => ['required', 'string', 'min:2', 'max:120'],
            'starts_at' => ['required', 'date'],
        ]);

        $createMatchAction->execute(auth()->user(), $validated);

        return redirect()->route('admin.matches.index')
            ->with('success', 'Match cree.');
    }

    public function settle(
        Request $request,
        int $matchId,
        SettleMatchBetsAction $settleMatchBetsAction
    ): RedirectResponse {
        $validated = $request->validate([
            'result' => ['required', 'string', 'in:home,away,draw,void'],
            'idempotency_key' => ['required', 'string', 'min:8', 'max:120'],
        ]);

        try {
            $settleMatchBetsAction->execute(
                actor: auth()->user(),
                matchId: $matchId,
                result: $validated['result'],
                idempotencyKey: $validated['idempotency_key'],
            );
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Match settle.');
    }
}
