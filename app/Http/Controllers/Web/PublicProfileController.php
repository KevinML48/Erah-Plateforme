<?php

namespace App\Http\Controllers\Web;

use App\Application\Actions\Ranking\EnsureUserProgressAction;
use App\Http\Controllers\Controller;
use App\Models\Bet;
use App\Models\Duel;
use App\Models\PointsTransaction;
use App\Models\User;
use Illuminate\View\View;

class PublicProfileController extends Controller
{
    public function __invoke(
        User $user,
        EnsureUserProgressAction $ensureUserProgressAction
    ): View {
        $progress = $ensureUserProgressAction->execute($user)->load('league');

        $stats = [
            'likes' => $user->clipLikes()->count(),
            'comments' => $user->clipComments()->count(),
            'duels' => Duel::query()->forUser($user->id)->count(),
            'bets' => Bet::query()->where('user_id', $user->id)->count(),
        ];

        $recentTransactions = PointsTransaction::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->limit(12)
            ->get();

        return view('pages.profile.public', [
            'userProfile' => $user,
            'progress' => $progress,
            'stats' => $stats,
            'recentTransactions' => $recentTransactions,
            'viewer' => auth()->user(),
        ]);
    }
}

