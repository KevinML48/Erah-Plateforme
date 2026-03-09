<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\StoreDuelResultRequest;
use App\Models\Duel;
use App\Models\User;
use App\Services\DuelService;
use Illuminate\Http\RedirectResponse;

class AdminDuelResultController extends Controller
{
    public function store(StoreDuelResultRequest $request, int $duelId, DuelService $duelService): RedirectResponse
    {
        $duel = Duel::query()->findOrFail($duelId);
        $winner = User::query()->findOrFail((int) $request->validated('winner_user_id'));

        try {
            $duelService->recordResult(
                actor: $request->user(),
                duel: $duel,
                winner: $winner,
                challengerScore: $request->validated('challenger_score'),
                challengedScore: $request->validated('challenged_score'),
                note: $request->validated('note'),
            );
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Resultat duel enregistre.');
    }
}
