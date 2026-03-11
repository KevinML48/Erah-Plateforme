<?php

namespace App\Http\Controllers\TestConsole;

use App\Application\Actions\Ranking\AddPointsAction;
use App\Application\Actions\Ranking\EnsureUserProgressAction;
use App\Domain\Ranking\Queries\LeaderboardQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Console\GrantRankingPointsRequest;
use App\Models\League;
use App\Models\PointsTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class RankingConsoleController extends Controller
{
    public function index(
        Request $request,
        EnsureUserProgressAction $ensureUserProgressAction,
        LeaderboardQuery $leaderboardQuery
    ): View {
        $user = $request->user();
        $progress = $ensureUserProgressAction->execute($user)->load('league');

        $leagues = League::query()->active()->orderBy('sort_order')->get();
        $leagueKey = (string) ($request->query('league') ?: $progress->league?->key ?: $leagues->first()?->key);

        $leaderboard = null;
        if ($leagueKey !== '') {
            try {
                $leaderboard = $leaderboardQuery->execute($leagueKey, 50);
            } catch (ModelNotFoundException) {
                $leaderboard = null;
            }
        }

        $recentTransactions = PointsTransaction::query()
            ->where('user_id', $user->id)
            ->latest('id')
            ->limit(20)
            ->get();

        $grantUsers = collect();
        if ($user->role === 'admin') {
            $grantUsers = User::query()->select(['id', 'name', 'email'])->orderBy('name')->limit(200)->get();
        }

        return view('pages.ranking.index', [
            'progress' => $progress,
            'leagues' => $leagues,
            'leagueKey' => $leagueKey,
            'leaderboard' => $leaderboard,
            'recentTransactions' => $recentTransactions,
            'grantUsers' => $grantUsers,
        ]);
    }

    public function grant(
        GrantRankingPointsRequest $request,
        AddPointsAction $addPointsAction
    ): RedirectResponse {
        $validated = $request->validated();
        $targetUser = User::query()->findOrFail((int) $validated['user_id']);

        try {
            $result = $addPointsAction->execute(
                user: $targetUser,
                kind: (string) $validated['kind'],
                points: (int) $validated['amount'],
                sourceType: (string) $validated['source_type'],
                sourceId: (string) $validated['source_id'],
                actor: $request->user(),
                meta: ['source' => 'console'],
            );
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        if ($result->idempotent) {
            return back()->with('success', 'Cet ajustement a deja ete applique.');
        }

        return back()->with('success', 'L ajustement du classement a bien ete enregistre.');
    }
}
