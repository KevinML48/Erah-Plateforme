<?php

namespace App\Http\Controllers\Web;

use App\Domain\Ranking\Queries\LeaderboardQuery;
use App\Http\Controllers\Controller;
use App\Models\Clip;
use App\Models\League;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function __invoke(LeaderboardQuery $leaderboardQuery): View
    {
        $recentClips = Clip::query()
            ->published()
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        $defaultLeagueKey = League::query()
            ->active()
            ->orderBy('sort_order')
            ->value('key');

        $leaderboard = null;
        if ($defaultLeagueKey) {
            try {
                $leaderboard = $leaderboardQuery->execute($defaultLeagueKey, 5);
            } catch (ModelNotFoundException) {
                $leaderboard = null;
            }
        }

        return view('pages.landing', [
            'recentClips' => $recentClips,
            'leaderboard' => $leaderboard,
        ]);
    }
}
