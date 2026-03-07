<?php

namespace App\Http\Controllers\Web;

use App\Application\Actions\Ranking\EnsureUserProgressAction;
use App\Http\Controllers\Controller;
use App\Models\ClipVoteCampaign;
use App\Models\CommunitySupportGoal;
use App\Models\SupporterPlan;
use App\Services\CreateSupporterCheckoutSession;
use App\Services\SupporterAccessResolver;
use App\Services\SyncStripeSubscriptionStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class SupporterPageController extends Controller
{
    public function show(
        Request $request,
        SupporterAccessResolver $supporterAccessResolver,
        SyncStripeSubscriptionStatus $syncStripeSubscriptionStatus,
        EnsureUserProgressAction $ensureUserProgressAction
    ): View {
        $plans = $supporterAccessResolver->ensureConfiguredPlans();
        $supporterAccessResolver->ensureCommunityGoals();
        $supporterAccessResolver->unlockCommunityGoals();

        $user = $request->user();
        $supporterSummary = null;
        $progress = null;

        if ($user) {
            $syncStripeSubscriptionStatus->execute($user);
            $supporterSummary = $supporterAccessResolver->summary($user);
            $progress = $ensureUserProgressAction->execute($user)->load('league');
        }

        $plan = $plans->firstWhere('key', (string) config('supporter.plan.key'))
            ?: $plans->first()
            ?: $supporterAccessResolver->ensureDefaultPlan();

        $totalSupporters = $supporterAccessResolver->totalActiveSupporters();
        $goals = CommunitySupportGoal::query()
            ->ordered()
            ->get()
            ->map(function (CommunitySupportGoal $goal) use ($totalSupporters): array {
                $percent = $goal->goal_count > 0
                    ? (int) min(100, round(($totalSupporters / $goal->goal_count) * 100))
                    : 100;

                return [
                    'goal_count' => (int) $goal->goal_count,
                    'title' => (string) $goal->title,
                    'description' => (string) ($goal->description ?? ''),
                    'is_unlocked' => (bool) $goal->is_unlocked,
                    'progress_percent' => $percent,
                ];
            })
            ->values();

        $campaigns = ClipVoteCampaign::query()
            ->active()
            ->withCount('votes')
            ->with(['entries.clip'])
            ->orderBy('ends_at')
            ->take(2)
            ->get()
            ->map(fn (ClipVoteCampaign $campaign): array => [
                'id' => (int) $campaign->id,
                'type' => (string) $campaign->type,
                'title' => (string) $campaign->title,
                'ends_at' => $campaign->ends_at,
                'votes_count' => (int) $campaign->votes_count,
                'clips' => $campaign->entries
                    ->filter(fn ($entry) => $entry->clip !== null)
                    ->take(3)
                    ->map(fn ($entry): array => [
                        'id' => (int) $entry->clip->id,
                        'title' => (string) $entry->clip->title,
                        'thumbnail_url' => (string) ($entry->clip->thumbnail_url ?: '/template/assets/img/logo.png'),
                        'url' => route('clips.show', $entry->clip->slug),
                    ])
                    ->values(),
            ])
            ->values();

        return view('pages.supporter.show', [
            'plan' => $plan,
            'planCards' => $this->planCards($plans),
            'totalSupporters' => $totalSupporters,
            'supporterSummary' => $supporterSummary,
            'progress' => $progress,
            'goals' => $goals,
            'wallMembers' => $supporterAccessResolver->wallMembers(),
            'benefitCards' => $this->benefitCards(),
            'campaigns' => $campaigns,
            'loyaltyBadges' => collect((array) config('supporter.loyalty_badges', []))
                ->map(fn (string $label, int|string $threshold): array => [
                    'months' => (int) $threshold,
                    'label' => $label,
                ])
                ->values(),
        ]);
    }

    public function checkout(
        Request $request,
        SupporterAccessResolver $supporterAccessResolver,
        CreateSupporterCheckoutSession $createSupporterCheckoutSession
    ): RedirectResponse {
        $planKeys = $supporterAccessResolver->activePlans()->pluck('key')->all();
        $validated = $request->validate([
            'plan_key' => ['required', 'string', Rule::in($planKeys)],
        ]);

        try {
            $checkout = $createSupporterCheckoutSession->execute($request->user(), (string) $validated['plan_key']);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()->away($checkout->url, 303);
    }

    /**
     * @param Collection<int, SupporterPlan> $plans
     * @return Collection<int, array<string, mixed>>
     */
    private function planCards(Collection $plans): Collection
    {
        $baseMonthly = max(1, (int) config('supporter.plan.base_monthly_price_cents', 500));

        return $plans
            ->sortBy('sort_order')
            ->values()
            ->map(function (SupporterPlan $plan) use ($baseMonthly): array {
                $months = max(1, (int) ($plan->billing_months ?? 1));
                $baseTotal = $baseMonthly * $months;
                $savingsCents = max(0, $baseTotal - (int) $plan->price_cents);
                $monthlyEquivalentCents = (int) round(((int) $plan->price_cents) / $months);

                return [
                    'key' => (string) $plan->key,
                    'name' => (string) $plan->name,
                    'price_label' => number_format(((int) $plan->price_cents) / 100, 2, ',', ' '),
                    'monthly_equivalent_label' => number_format($monthlyEquivalentCents / 100, 2, ',', ' '),
                    'currency' => strtoupper((string) $plan->currency),
                    'months' => $months,
                    'discount_percent' => (float) ($plan->discount_percent ?? 0),
                    'savings_label' => number_format($savingsCents / 100, 2, ',', ' '),
                    'description' => (string) ($plan->description ?? ''),
                    'is_recommended' => $months === 12,
                    'is_default' => $months === 1,
                ];
            });
    }

    public function success(Request $request, SyncStripeSubscriptionStatus $syncStripeSubscriptionStatus): RedirectResponse
    {
        if ($request->user()) {
            $syncStripeSubscriptionStatus->execute($request->user());
        }

        return redirect()
            ->route('supporter.show')
            ->with('success', 'Le checkout supporter est confirme. Activation en cours via Stripe.');
    }

    public function cancel(): RedirectResponse
    {
        return redirect()
            ->route('supporter.show')
            ->with('error', 'Le checkout supporter a ete annule.');
    }

    /**
     * @return Collection<int, array<string, string>>
     */
    private function benefitCards(): Collection
    {
        return collect([
            [
                'label' => 'Plateforme',
                'title' => 'Badge Supporter ERAH et profil mis en avant',
                'excerpt' => 'Votre profil remonte dans les espaces communautaires, avec visibilite renforcee dans les zones supporter.',
            ],
            [
                'label' => 'Missions',
                'title' => 'Missions exclusives et mission hebdomadaire supporter',
                'excerpt' => 'Des objectifs reserves aux supporters avec bonus XP, points de classement et progression fidelite.',
            ],
            [
                'label' => 'Clips',
                'title' => 'Reactions premium, commentaires prioritaires et votes',
                'excerpt' => 'Votez pour le clip de la semaine et l action du mois, avec reactions supporter dediees.',
            ],
            [
                'label' => 'Classements',
                'title' => 'Badge special dans les leaderboards',
                'excerpt' => 'Un marqueur visible dans les classements et sur les experiences communautaires de la plateforme.',
            ],
            [
                'label' => 'Club',
                'title' => 'Reductions merchandising et acces anticipe aux drops',
                'excerpt' => 'Des avantages concrets hors plateforme pour soutenir le club et profiter des sorties en avant-premiere.',
            ],
            [
                'label' => 'En cours',
                'title' => 'Invitations, rencontres joueurs et evenements locaux',
                'excerpt' => 'Le programme supporter ouvre des activations IRL et des moments privilegies avec la communaute ERAH.',
            ],
        ]);
    }
}
