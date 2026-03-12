<?php

namespace App\Http\Controllers\Web;

use App\Application\Actions\Ranking\EnsureUserProgressAction;
use App\Domain\Ranking\Queries\LeaderboardQuery;
use App\Http\Controllers\Controller;
use App\Models\League;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\View\View;

class LeaderboardPageController extends Controller
{
    public function me(
        EnsureUserProgressAction $ensureUserProgressAction,
        LeaderboardQuery $leaderboardQuery
    ): View {
        $user = auth()->user();
        $progress = $ensureUserProgressAction->execute($user)->load('league');

        $payload = null;
        if ($progress->league?->key) {
            try {
                $payload = $leaderboardQuery->execute($progress->league->key, 100);
            } catch (ModelNotFoundException) {
                $payload = null;
            }
        }

        $nextLeague = League::query()
            ->active()
            ->where('sort_order', '>', (int) $progress->league?->sort_order)
            ->orderBy('sort_order')
            ->first();

        return view('pages.leaderboards.me', [
            'progress' => $progress,
            'leaderboard' => $payload,
            'nextLeague' => $nextLeague,
            'user' => $user,
        ]);
    }

    public function index(LeaderboardQuery $leaderboardQuery): View
    {
        $leagues = League::query()
            ->active()
            ->orderBy('sort_order')
            ->get();

        $leagueCards = $leagues->map(function (League $league) use ($leaderboardQuery) {
            $payload = $leaderboardQuery->execute($league->key, 100);
            $entries = collect($payload['entries'] ?? []);
            $topEntry = $entries->first();

            return [
                'id' => (int) $league->id,
                'key' => (string) $league->key,
                'name' => (string) $league->name,
                'min_rank_points' => (int) $league->min_rank_points,
                'players_count' => (int) $entries->count(),
                'top_name' => (string) ($topEntry['name'] ?? ''),
                'top_xp' => (int) ($topEntry['total_xp'] ?? 0),
                'top_rank_points' => (int) ($topEntry['total_rank_points'] ?? 0),
                'average_xp' => $entries->count() > 0
                    ? (int) round((float) $entries->avg('total_xp'))
                    : 0,
                'average_rank_points' => $entries->count() > 0
                    ? (int) round((float) $entries->avg('total_rank_points'))
                    : 0,
            ];
        })->values();

        $totalPlayers = (int) $leagueCards->sum('players_count');
        $bestLeague = $leagueCards->sortByDesc('top_xp')->first();
        $averagePlayersPerLeague = $leagueCards->count() > 0
            ? (int) round($totalPlayers / $leagueCards->count())
            : 0;

        return view('pages.leaderboards.index', [
            'leagues' => $leagues,
            'leagueCards' => $leagueCards,
            'totalPlayers' => $totalPlayers,
            'bestLeague' => $bestLeague,
            'averagePlayersPerLeague' => $averagePlayersPerLeague,
        ]);
    }

    public function show(string $leagueKey, LeaderboardQuery $leaderboardQuery): View
    {
        $leagues = League::query()
            ->active()
            ->orderBy('sort_order')
            ->get();

        try {
            $payload = $leaderboardQuery->execute($leagueKey, 100);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return view('pages.leaderboards.show', [
            'leaderboard' => $payload,
            'leagues' => $leagues,
            'leagueKey' => $leagueKey,
            'currentUserId' => auth()->id(),
        ]);
    }
}
