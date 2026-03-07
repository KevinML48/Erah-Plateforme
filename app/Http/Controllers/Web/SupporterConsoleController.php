<?php

namespace App\Http\Controllers\Web;

use App\Application\Actions\Ranking\EnsureUserProgressAction;
use App\Application\Actions\Rewards\EnsureCurrentMissionInstancesAction;
use App\Http\Controllers\Controller;
use App\Models\Clip;
use App\Models\ClipFavorite;
use App\Models\ClipVoteCampaign;
use App\Models\UserMission;
use App\Services\CreateStripeCustomerPortalSession;
use App\Services\SupporterAccessResolver;
use App\Services\SyncStripeSubscriptionStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class SupporterConsoleController extends Controller
{
    public function index(
        Request $request,
        SupporterAccessResolver $supporterAccessResolver,
        SyncStripeSubscriptionStatus $syncStripeSubscriptionStatus,
        EnsureUserProgressAction $ensureUserProgressAction,
        EnsureCurrentMissionInstancesAction $ensureCurrentMissionInstancesAction
    ): View {
        $user = $request->user();

        $syncStripeSubscriptionStatus->execute($user);
        $supporterProfile = $supporterAccessResolver->ensurePublicProfile($user);
        $supporterSummary = $supporterAccessResolver->summary($user);
        $progress = $ensureUserProgressAction->execute($user)->load('league');
        $ensureCurrentMissionInstancesAction->execute($user);

        $exclusiveMissions = UserMission::query()
            ->where('user_id', $user->id)
            ->with(['instance.template'])
            ->latest('updated_at')
            ->get()
            ->filter(fn (UserMission $mission) => (bool) ($mission->instance?->template?->constraints['supporter_only'] ?? false))
            ->take(6)
            ->values();

        $favoriteClipIds = ClipFavorite::query()
            ->where('user_id', $user->id)
            ->latest('id')
            ->limit(6)
            ->pluck('clip_id');

        $favoriteClips = Clip::query()
            ->published()
            ->whereIn('id', $favoriteClipIds)
            ->orderByDesc('published_at')
            ->get();

        $campaigns = ClipVoteCampaign::query()
            ->active()
            ->withCount('votes')
            ->with(['entries.clip'])
            ->orderBy('ends_at')
            ->get();

        return view('pages.supporter.console', [
            'user' => $user,
            'progress' => $progress,
            'supporterSummary' => $supporterSummary,
            'supporterProfile' => $supporterProfile,
            'currentSubscription' => $user->supportSubscriptions()->current()->with('plan')->first(),
            'monthlyRewards' => $user->supporterMonthlyRewards()->latest('reward_month')->latest('id')->limit(12)->get(),
            'exclusiveMissions' => $exclusiveMissions,
            'favoriteClips' => $favoriteClips,
            'campaigns' => $campaigns,
            'reactionOptions' => ClipSupporterController::reactionOptions(),
        ]);
    }

    public function portal(
        Request $request,
        CreateStripeCustomerPortalSession $createStripeCustomerPortalSession
    ): RedirectResponse {
        try {
            $url = $createStripeCustomerPortalSession->execute($request->user(), route('supporter.console'));
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()->away($url, 303);
    }
}
