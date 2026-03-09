<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DuelResult;
use App\Models\UserProgress;
use App\Services\LeaderboardService;
use Illuminate\View\View;

class DuelLeaderboardPageController extends Controller
{
    public function __invoke(LeaderboardService $leaderboardService): View
    {
        $user = auth()->user();
        $progress = $user?->progress()->first();

        return view('pages.duels.leaderboard', [
            'duelLeaderboard' => $leaderboardService->duel(30),
            'progress' => $progress,
            'stats' => [
                'players_ranked' => (int) UserProgress::query()
                    ->where(function ($query): void {
                        $query->where('duel_wins', '>', 0)
                            ->orWhere('duel_losses', '>', 0);
                    })
                    ->count(),
                'duels_settled' => (int) DuelResult::query()->count(),
                'best_score' => (int) (UserProgress::query()->max('duel_score') ?? 0),
                'best_streak' => (int) (UserProgress::query()->max('duel_best_streak') ?? 0),
            ],
        ]);
    }
}
