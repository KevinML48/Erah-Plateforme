<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClipSupporterReaction;
use App\Models\ClipVote;
use App\Models\SupporterMonthlyReward;
use App\Models\User;
use App\Models\UserSupportSubscription;
use App\Services\SupporterAccessResolver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupportersAdminController extends Controller
{
    public function index(Request $request, SupporterAccessResolver $supporterAccessResolver): View
    {
        $supporterAccessResolver->ensureDefaultPlan();
        $supporterAccessResolver->ensureCommunityGoals();
        $supporterAccessResolver->unlockCommunityGoals();

        $search = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', 'all'));
        $latestIdsQuery = UserSupportSubscription::query()
            ->from('user_support_subscriptions as supporter_latest')
            ->selectRaw('MAX(supporter_latest.id)')
            ->groupBy('supporter_latest.user_id');

        $subscriptionsQuery = UserSupportSubscription::query()
            ->whereIn('id', $latestIdsQuery)
            ->with(['user.progress.league', 'user.supportPublicProfile', 'plan'])
            ->orderByDesc('started_at')
            ->orderByDesc('id');

        if ($search !== '') {
            $subscriptionsQuery->whereHas('user', function (Builder $query) use ($search): void {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        }

        if ($status !== '' && $status !== 'all' && in_array($status, UserSupportSubscription::statuses(), true)) {
            $subscriptionsQuery->where('status', $status);
        }

        $subscriptions = $subscriptionsQuery->paginate(20)->withQueryString();

        $countsBase = UserSupportSubscription::query()->whereIn('id', $latestIdsQuery);
        $statusCounts = collect(UserSupportSubscription::statuses())
            ->mapWithKeys(fn (string $subscriptionStatus): array => [
                $subscriptionStatus => (clone $countsBase)->where('status', $subscriptionStatus)->count(),
            ]);

        return view('pages.admin.supporters.index', [
            'subscriptions' => $subscriptions,
            'search' => $search,
            'status' => $status,
            'statusCounts' => $statusCounts,
            'totalSupporters' => $supporterAccessResolver->totalActiveSupporters(),
            'wallVisibleCount' => User::query()
                ->whereHas('supportPublicProfile', fn (Builder $query) => $query->where('is_visible_on_wall', true))
                ->count(),
            'monthlyRewardCount' => SupporterMonthlyReward::query()->count(),
        ]);
    }

    public function show(int $userId, SupporterAccessResolver $supporterAccessResolver): View
    {
        $user = User::query()
            ->with([
                'progress.league',
                'supportPublicProfile',
                'supportSubscriptions.plan',
                'supporterMonthlyRewards',
            ])
            ->findOrFail($userId);

        $supporterAccessResolver->ensurePublicProfile($user);

        return view('pages.admin.supporters.show', [
            'user' => $user,
            'supporterSummary' => $supporterAccessResolver->summary($user),
            'subscriptionHistory' => $user->supportSubscriptions->sortByDesc('id')->values(),
            'monthlyRewards' => $user->supporterMonthlyRewards->sortByDesc('reward_month')->values(),
            'supporterStats' => [
                'votes' => ClipVote::query()->where('user_id', $user->id)->count(),
                'reactions' => ClipSupporterReaction::query()->where('user_id', $user->id)->count(),
                'favorites' => $user->clipFavorites()->count(),
                'supporter_comments' => $user->clipComments()->count(),
            ],
        ]);
    }
}
