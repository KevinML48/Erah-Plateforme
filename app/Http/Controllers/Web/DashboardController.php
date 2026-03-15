<?php

namespace App\Http\Controllers\Web;

use App\Application\Actions\Ranking\EnsureUserProgressAction;
use App\Domain\Ranking\Queries\LeaderboardQuery;
use App\Http\Controllers\Controller;
use App\Models\EsportMatch;
use App\Models\Clip;
use App\Models\ClipFavorite;
use App\Models\ClipLike;
use App\Models\Duel;
use App\Models\MissionTemplate;
use App\Models\Notification;
use App\Models\Bet;
use App\Models\UserMission;
use App\Services\ExperienceService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(
        EnsureUserProgressAction $ensureUserProgressAction,
        LeaderboardQuery $leaderboardQuery,
        ExperienceService $experienceService
    ): View {
        $user = auth()->user();
        $progress = $ensureUserProgressAction->execute($user)->load('league');
        $experience = $experienceService->summaryFor($user);
        $nextLeague = collect(config('community.xp_leagues', []))
            ->first(fn (array $definition): bool => (int) ($definition['xp_threshold'] ?? 0) > (int) $progress->total_xp);
        $progressPercent = (int) $experience['progress_percent'];

        $leaderboard = null;
        $rankPosition = null;
        if ($progress->league?->key) {
            try {
                $leaderboard = $leaderboardQuery->execute($progress->league->key, 50);
                $rankPosition = collect($leaderboard['entries'])
                    ->firstWhere('user_id', $user->id)['position'] ?? null;
            } catch (ModelNotFoundException) {
                $leaderboard = null;
            }
        }

        $pendingDuels = Duel::query()
            ->forUser($user->id)
            ->where('status', Duel::STATUS_PENDING)
            ->with(['challenger:id,name', 'challenged:id,name'])
            ->orderBy('expires_at')
            ->limit(5)
            ->get();

        $upcomingMatches = EsportMatch::query()
            ->whereIn('status', [
                EsportMatch::STATUS_SCHEDULED,
                EsportMatch::STATUS_LOCKED,
                EsportMatch::STATUS_LIVE,
            ])
            ->orderBy('starts_at')
            ->limit(3)
            ->get();

        $dailyMissions = UserMission::query()
            ->where('user_id', $user->id)
            ->whereHas('instance', fn ($query) => $query
                ->where('period_start', '<=', now())
                ->where('period_end', '>=', now()))
            ->whereHas('instance.template', fn ($query) => $query
                ->where('scope', MissionTemplate::SCOPE_DAILY)
                ->where('is_active', true))
            ->with('instance.template')
            ->orderByRaw('CASE WHEN complèted_at IS NULL THEN 0 ELSE 1 END')
            ->orderByDesc('id')
            ->limit(3)
            ->get();

        $recentNotifications = Notification::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $recentBets = Bet::query()
            ->where('user_id', $user->id)
            ->with('match:id,team_a_name,team_b_name,home_team,away_team,starts_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $clips = Clip::query()
            ->published()
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        $clipIds = $clips->pluck('id')->all();
        $likedIds = ClipLike::query()
            ->where('user_id', $user->id)
            ->whereIn('clip_id', $clipIds)
            ->pluck('clip_id')
            ->all();
        $favoriteIds = ClipFavorite::query()
            ->where('user_id', $user->id)
            ->whereIn('clip_id', $clipIds)
            ->pluck('clip_id')
            ->all();

        $rewardWalletBalance = (int) ($user->rewardWallet()->value('balance') ?? 0);
        $betWalletBalance = $rewardWalletBalance;

        return view('pages.dashboard', [
            'user' => $user,
            'progress' => $progress,
            'experience' => $experience,
            'nextLeague' => $nextLeague,
            'progressPercent' => $progressPercent,
            'leaderboard' => $leaderboard,
            'rankPosition' => $rankPosition,
            'pendingDuels' => $pendingDuels,
            'upcomingMatches' => $upcomingMatches,
            'dailyMissions' => $dailyMissions,
            'recentNotifications' => $recentNotifications,
            'recentBets' => $recentBets,
            'clips' => $clips,
            'likedIds' => $likedIds,
            'favoriteIds' => $favoriteIds,
            'rewardWalletBalance' => $rewardWalletBalance,
            'betWalletBalance' => $betWalletBalance,
        ]);
    }
}
