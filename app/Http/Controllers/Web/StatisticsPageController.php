<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bet;
use App\Models\Clip;
use App\Models\DuelResult;
use App\Models\UserProgress;
use App\Services\LeaderboardService;
use App\Services\RankService;
use Illuminate\View\View;

class StatisticsPageController extends Controller
{
    public function __invoke(LeaderboardService $leaderboardService, RankService $rankService): View
    {
        $user = auth()->user();
        $progress = $user?->progress()->first();

        return view('pages.statistics.index', [
            'xpLeaderboard' => $leaderboardService->xp(10),
            'communityRank' => $user ? $rankService->currentLeague($user) : null,
            'progress' => $progress,
            'stats' => [
                'users' => (int) UserProgress::query()->count(),
                'clips' => (int) Clip::query()->published()->count(),
                'bets_won' => (int) Bet::query()->where('status', Bet::STATUS_WON)->count(),
                'duels_settled' => (int) DuelResult::query()->count(),
            ],
        ]);
    }
}
