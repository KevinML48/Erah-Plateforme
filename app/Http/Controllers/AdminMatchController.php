<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\MatchResult;
use App\Enums\MatchStatus;
use App\Exceptions\MatchAlreadyCompletedException;
use App\Exceptions\MatchNotOpenException;
use App\Exceptions\MatchResultMissingException;
use App\Http\Requests\SettleMatchRequest;
use App\Http\Requests\CompleteMatchRequest;
use App\Http\Requests\StoreMatchRequest;
use App\Http\Requests\UpdateMatchRequest;
use App\Models\EsportMatch;
use App\Models\Ticket;
use App\Services\MatchService;
use App\Services\SettlementService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class AdminMatchController extends Controller
{
    public function index(): JsonResponse|View
    {
        $this->authorize('manageMatch', EsportMatch::class);

        $matches = EsportMatch::query()
            ->withCount('predictions')
            ->with('creator:id,name')
            ->orderByDesc('starts_at')
            ->paginate(30);

        if (!request()->expectsJson()) {
            return view('pages.admin.matches.index', [
                'title' => 'Admin Matches',
                'matches' => $matches,
                'statuses' => MatchStatus::cases(),
                'results' => MatchResult::cases(),
            ]);
        }

        return response()->json($matches);
    }

    public function store(StoreMatchRequest $request, MatchService $matchService): JsonResponse|RedirectResponse
    {
        $admin = $request->user();
        abort_unless($admin, 401);

        $match = $matchService->createMatch($request->validated(), $admin);

        if (!$request->expectsJson()) {
            return redirect()->route('admin.matches.index')->with('status', 'Match cree avec succes.');
        }

        return response()->json([
            'message' => 'Match created.',
            'match' => $match,
        ], 201);
    }

    public function update(UpdateMatchRequest $request, EsportMatch $match, MatchService $matchService): JsonResponse|RedirectResponse
    {
        try {
            $updated = $matchService->updateMatch($match, $request->validated());
        } catch (MatchAlreadyCompletedException $exception) {
            if (!$request->expectsJson()) {
                return back()->withErrors(['match' => $exception->getMessage()]);
            }

            return response()->json(['message' => $exception->getMessage()], 422);
        }

        if (!$request->expectsJson()) {
            return redirect()->route('admin.matches.index')->with('status', 'Match mis a jour.');
        }

        return response()->json([
            'message' => 'Match updated.',
            'match' => $updated,
        ]);
    }

    public function open(EsportMatch $match, MatchService $matchService): JsonResponse|RedirectResponse
    {
        try {
            $updated = $matchService->openPredictions($match);
        } catch (MatchAlreadyCompletedException $exception) {
            if (!request()->expectsJson()) {
                return back()->withErrors(['match' => $exception->getMessage()]);
            }

            return response()->json(['message' => $exception->getMessage()], 422);
        }

        if (!request()->expectsJson()) {
            return redirect()->route('admin.matches.index')->with('status', 'Pronostics ouverts.');
        }

        return response()->json([
            'message' => 'Predictions opened.',
            'match' => $updated,
        ]);
    }

    public function lock(EsportMatch $match, MatchService $matchService): JsonResponse|RedirectResponse
    {
        try {
            $updated = $matchService->lockPredictions($match);
        } catch (MatchAlreadyCompletedException|MatchNotOpenException $exception) {
            if (!request()->expectsJson()) {
                return back()->withErrors(['match' => $exception->getMessage()]);
            }

            return response()->json(['message' => $exception->getMessage()], 422);
        }

        if (!request()->expectsJson()) {
            return redirect()->route('admin.matches.index')->with('status', 'Pronostics verrouilles.');
        }

        return response()->json([
            'message' => 'Predictions locked.',
            'match' => $updated,
        ]);
    }

    public function complete(
        CompleteMatchRequest $request,
        EsportMatch $match,
        MatchService $matchService
    ): JsonResponse|RedirectResponse {
        try {
            $updated = $matchService->completeMatchWithResult(
                match: $match,
                result: (string) $request->string('result')
            );
        } catch (MatchAlreadyCompletedException|MatchResultMissingException $exception) {
            if (!$request->expectsJson()) {
                return back()->withErrors(['match' => $exception->getMessage()]);
            }

            return response()->json(['message' => $exception->getMessage()], 422);
        }

        if (!$request->expectsJson()) {
            return redirect()->route('admin.matches.index')->with('status', 'Match complete, points distribues.');
        }

        return response()->json([
            'message' => 'Match completed and points awarded.',
            'match' => $updated,
        ]);
    }

    public function settle(SettleMatchRequest $request, EsportMatch $match, SettlementService $settlementService): JsonResponse
    {
        $settlementService->settleMatch(
            match: $match,
            marketResults: (array) $request->input('markets', []),
            actorUserId: (int) $request->user()->id
        );

        return response()->json([
            'message' => 'Match markets settled.',
        ]);
    }

    public function live(EsportMatch $match, MatchService $matchService): JsonResponse|RedirectResponse
    {
        try {
            $updated = $matchService->setLive($match);
        } catch (MatchAlreadyCompletedException $exception) {
            if (!request()->expectsJson()) {
                return back()->withErrors(['match' => $exception->getMessage()]);
            }

            return response()->json(['message' => $exception->getMessage()], 422);
        }

        if (!request()->expectsJson()) {
            return redirect()->route('admin.matches.index')->with('status', 'Match passe en LIVE.');
        }

        return response()->json([
            'message' => 'Match set live.',
            'match' => $updated,
        ]);
    }

    public function cancel(EsportMatch $match, MatchService $matchService): JsonResponse|RedirectResponse
    {
        try {
            $updated = $matchService->cancelMatch($match);
        } catch (MatchAlreadyCompletedException $exception) {
            if (!request()->expectsJson()) {
                return back()->withErrors(['match' => $exception->getMessage()]);
            }

            return response()->json(['message' => $exception->getMessage()], 422);
        }

        if (!request()->expectsJson()) {
            return redirect()->route('admin.matches.index')->with('status', 'Match annule.');
        }

        return response()->json([
            'message' => 'Match cancelled.',
            'match' => $updated,
        ]);
    }

    public function tickets(EsportMatch $match): JsonResponse
    {
        $tickets = Ticket::query()
            ->with(['user:id,name,email', 'selections.option:id,label,key', 'selections.market:id,name,code'])
            ->where('match_id', $match->id)
            ->when(request()->filled('status'), function ($query): void {
                $query->where('status', (string) request()->string('status'));
            })
            ->orderByDesc('id')
            ->paginate(50);

        return response()->json($tickets);
    }
}
