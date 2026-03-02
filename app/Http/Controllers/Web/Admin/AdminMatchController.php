<?php

namespace App\Http\Controllers\Web\Admin;

use App\Application\Actions\Bets\SettleMatchBetsAction;
use App\Application\Actions\Matches\CreateMatchAction;
use App\Application\Actions\Matches\SetMatchResultAction;
use App\Application\Actions\Matches\UpdateMatchAction;
use App\Application\Actions\Matches\UpdateMatchStatusAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\SetAdminMatchResultRequest;
use App\Http\Requests\Web\Admin\SettleAdminMatchRequest;
use App\Http\Requests\Web\Admin\StoreAdminMatchRequest;
use App\Http\Requests\Web\Admin\UpdateAdminMatchRequest;
use App\Http\Requests\Web\Admin\UpdateAdminMatchStatusRequest;
use App\Models\EsportMatch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class AdminMatchController extends Controller
{
    public function index(Request $request): View
    {
        $status = (string) $request->query('status', 'all');

        $matches = EsportMatch::query()
            ->withCount('bets')
            ->with('settlement:id,match_id,result,processed_at,won_count,lost_count,void_count')
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->orderByDesc('starts_at')
            ->paginate(20)
            ->withQueryString();

        return view('pages.admin.matches.index', [
            'matches' => $matches,
            'status' => $status,
            'statuses' => EsportMatch::statuses(),
            'resultOptions' => $this->resultOptions(),
        ]);
    }

    public function create(): View
    {
        return view('pages.admin.matches.form', [
            'match' => null,
            'action' => route('admin.matches.store'),
            'method' => 'POST',
        ]);
    }

    public function store(StoreAdminMatchRequest $request, CreateMatchAction $createMatchAction): RedirectResponse
    {
        $validated = $request->validated();
        $matchKey = 'mch-'.now()->format('Ymd-His').'-'.strtolower(substr((string) \Illuminate\Support\Str::uuid(), 0, 8));

        try {
            $match = $createMatchAction->execute(
                actor: $request->user(),
                payload: [
                    'match_key' => $matchKey,
                    'game_key' => $validated['game_key'] ?? null,
                    'team_a_name' => $validated['team_a_name'],
                    'team_b_name' => $validated['team_b_name'],
                    'starts_at' => $validated['starts_at'],
                    'locked_at' => $validated['locked_at'] ?? null,
                ],
            );
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.matches.manage', $match->id)
            ->with('success', 'Match cree avec succes.');
    }

    public function edit(int $matchId): View
    {
        $match = EsportMatch::query()->findOrFail($matchId);

        return view('pages.admin.matches.form', [
            'match' => $match,
            'action' => route('admin.matches.update', $match->id),
            'method' => 'PUT',
        ]);
    }

    public function update(
        UpdateAdminMatchRequest $request,
        int $matchId,
        UpdateMatchAction $updateMatchAction
    ): RedirectResponse {
        $match = EsportMatch::query()->findOrFail($matchId);

        try {
            $updateMatchAction->execute(
                actor: $request->user(),
                match: $match,
                payload: $request->validated(),
            );
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.matches.manage', $matchId)
            ->with('success', 'Match mis a jour.');
    }

    public function manage(int $matchId): View
    {
        $match = EsportMatch::query()
            ->withCount('bets')
            ->with([
                'settlement:id,match_id,idempotency_key,result,bets_total,won_count,lost_count,void_count,payout_total,processed_at',
                'bets' => fn ($query) => $query->with('user:id,name')->latest('id')->limit(30),
            ])
            ->findOrFail($matchId);

        return view('pages.admin.matches.manage', [
            'match' => $match,
            'statuses' => EsportMatch::statuses(),
            'resultOptions' => $this->resultOptions(),
        ]);
    }

    public function updateStatus(
        UpdateAdminMatchStatusRequest $request,
        int $matchId,
        UpdateMatchStatusAction $updateMatchStatusAction
    ): RedirectResponse {
        $match = EsportMatch::query()->findOrFail($matchId);

        try {
            $updateMatchStatusAction->execute(
                actor: $request->user(),
                match: $match,
                status: (string) $request->validated()['status'],
            );
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Statut du match mis a jour.');
    }

    public function setResult(
        SetAdminMatchResultRequest $request,
        int $matchId,
        SetMatchResultAction $setMatchResultAction
    ): RedirectResponse {
        $match = EsportMatch::query()->findOrFail($matchId);

        try {
            $setMatchResultAction->execute(
                actor: $request->user(),
                match: $match,
                result: (string) $request->validated()['result'],
            );
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Resultat du match defini.');
    }

    public function settle(
        SettleAdminMatchRequest $request,
        int $matchId,
        SettleMatchBetsAction $settleMatchBetsAction
    ): RedirectResponse {
        $validated = $request->validated();

        try {
            $result = $settleMatchBetsAction->execute(
                actor: $request->user(),
                matchId: $matchId,
                result: (string) $validated['result'],
                idempotencyKey: (string) $validated['idempotency_key'],
            );
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        if ($result['idempotent']) {
            return back()->with('success', 'Settlement deja applique (replay idempotent).');
        }

        return back()->with('success', 'Settlement execute.');
    }

    /**
     * @return array<string, string>
     */
    private function resultOptions(): array
    {
        return [
            EsportMatch::RESULT_TEAM_A => 'Team A',
            EsportMatch::RESULT_TEAM_B => 'Team B',
            EsportMatch::RESULT_DRAW => 'Draw',
            EsportMatch::RESULT_VOID => 'Void',
        ];
    }
}
