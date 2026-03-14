<?php

namespace App\Http\Controllers\Web;

use App\Application\Actions\Ranking\EnsureUserProgressAction;
use App\Http\Controllers\Controller;
use App\Models\Bet;
use App\Models\ClubReview;
use App\Models\Duel;
use App\Models\PointsTransaction;
use App\Models\User;
use App\Models\UserProgress;
use App\Services\ProfileCosmeticService;
use App\Services\SupporterAccessResolver;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class PublicProfileController extends Controller
{
    public function __invoke(
        User $user,
        EnsureUserProgressAction $ensureUserProgressAction,
        SupporterAccessResolver $supporterAccessResolver,
        ProfileCosmeticService $profileCosmeticService
    ): View {
        $viewer = auth()->user();
        $canModerateProfile = $viewer?->role === User::ROLE_ADMIN;
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

        $rankPosition = null;
        if ($progress) {
            $rankPosition = 1 + UserProgress::query()
                ->where(function ($query) use ($progress): void {
                    $query
                        ->where('total_rank_points', '>', (int) $progress->total_rank_points)
                        ->orWhere(function ($nested) use ($progress): void {
                            $nested
                                ->where('total_rank_points', (int) $progress->total_rank_points)
                                ->where('total_xp', '>', (int) $progress->total_xp);
                        })
                        ->orWhere(function ($nested) use ($progress): void {
                            $nested
                                ->where('total_rank_points', (int) $progress->total_rank_points)
                                ->where('total_xp', (int) $progress->total_xp)
                                ->where('user_id', '<', (int) $progress->user_id);
                        });
                })
                ->count();
        }

        $reviewsModuleReady = Schema::hasTable('club_reviews');
        $moderationReview = $reviewsModuleReady && $canModerateProfile
            ? $user->clubReview()->first()
            : null;
        $publishedReview = $moderationReview instanceof ClubReview
            ? ($moderationReview->status === ClubReview::STATUS_PUBLISHED ? $moderationReview : null)
            : ($reviewsModuleReady
                ? $user->clubReview()->where('status', ClubReview::STATUS_PUBLISHED)->first()
                : null);

        return view('pages.profile.public', [
            'userProfile' => $user,
            'progress' => $progress,
            'stats' => $stats,
            'rankPosition' => $rankPosition,
            'recentTransactions' => $recentTransactions,
            'publishedReview' => $publishedReview,
            'moderationReview' => $moderationReview,
            'reviewsModuleReady' => $reviewsModuleReady,
            'canModerateProfile' => $canModerateProfile,
            'supporterSummary' => $supporterAccessResolver->summary($user),
            'viewer' => $viewer,
            'profileCosmetics' => $profileCosmeticService->snapshotFor($user),
        ]);
    }
}
