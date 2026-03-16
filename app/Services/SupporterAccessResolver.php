<?php

namespace App\Services;

use App\Models\CommunitySupportGoal;
use App\Models\SupporterPlan;
use App\Models\SupporterPublicProfile;
use App\Models\SupporterMonthlyReward;
use App\Models\User;
use App\Models\UserSupportSubscription;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class SupporterAccessResolver
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function configuredPlans(): Collection
    {
        $plans = collect((array) config('supporter.plans', []));

        if ($plans->isEmpty()) {
            $plans = collect([config('supporter.plan', [])]);
        }

        return $plans
            ->values()
            ->map(function (array $plan, int $index): array {
                $billingMonths = max(1, (int) ($plan['billing_months'] ?? 1));

                return [
                    'key' => (string) ($plan['key'] ?? 'supporter-plan-'.$index),
                    'name' => (string) ($plan['name'] ?? 'Supporter ERAH'),
                    'price_cents' => (int) ($plan['price_cents'] ?? 0),
                    'currency' => (string) ($plan['currency'] ?? 'eur'),
                    'billing_interval' => (string) ($plan['billing_interval'] ?? 'month'),
                    'billing_months' => $billingMonths,
                    'discount_percent' => (float) ($plan['discount_percent'] ?? 0),
                    'sort_order' => (int) ($plan['sort_order'] ?? ($index + 1)),
                    'description' => (string) ($plan['description'] ?? ''),
                    'stripe_price_id' => blank($plan['stripe_price_id'] ?? null) ? null : (string) $plan['stripe_price_id'],
                ];
            })
            ->sortBy('sort_order')
            ->values();
    }

    /**
     * @return Collection<int, SupporterPlan>
     */
    public function ensureConfiguredPlans(): Collection
    {
        return $this->configuredPlans()
            ->map(function (array $plan): SupporterPlan {
                return SupporterPlan::query()->updateOrCreate(
                    ['key' => $plan['key']],
                    [
                        'name' => $plan['name'],
                        'price_cents' => $plan['price_cents'],
                        'currency' => $plan['currency'],
                        'billing_interval' => $plan['billing_interval'],
                        'billing_months' => $plan['billing_months'],
                        'discount_percent' => $plan['discount_percent'],
                        'sort_order' => $plan['sort_order'],
                        'description' => $plan['description'],
                        'stripe_price_id' => $plan['stripe_price_id'],
                        'is_active' => true,
                    ]
                );
            })
            ->sortBy('sort_order')
            ->values();
    }

    public function ensureDefaultPlan(): SupporterPlan
    {
        $plans = $this->ensureConfiguredPlans();
        $defaultKey = (string) config('supporter.plan.key');

        return $plans->firstWhere('key', $defaultKey)
            ?: $plans->first()
            ?: SupporterPlan::query()->firstOrFail();
    }

    /**
     * @return Collection<int, SupporterPlan>
     */
    public function activePlans(): Collection
    {
        $this->ensureConfiguredPlans();

        return SupporterPlan::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function resolvePlan(?string $planKey = null): SupporterPlan
    {
        $plans = $this->activePlans();

        if ($planKey !== null) {
            $selected = $plans->firstWhere('key', $planKey);

            if ($selected instanceof SupporterPlan) {
                return $selected;
            }
        }

        return $plans->firstWhere('key', (string) config('supporter.plan.key'))
            ?: $this->ensureDefaultPlan();
    }

    public function planForPriceId(?string $priceId): ?SupporterPlan
    {
        if (blank($priceId)) {
            return null;
        }

        $this->ensureConfiguredPlans();

        return SupporterPlan::query()
            ->active()
            ->where('stripe_price_id', (string) $priceId)
            ->first();
    }

    public function ensureCommunityGoals(): void
    {
        foreach ((array) config('supporter.community_goals', []) as $goal) {
            CommunitySupportGoal::query()->firstOrCreate(
                ['goal_count' => (int) ($goal['goal_count'] ?? 0)],
                [
                    'title' => (string) ($goal['title'] ?? 'Objectif supporter'),
                    'description' => (string) ($goal['description'] ?? ''),
                    'is_unlocked' => false,
                ]
            );
        }
    }

    public function ensurePublicProfile(User $user): SupporterPublicProfile
    {
        return SupporterPublicProfile::query()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'is_visible_on_wall' => true,
                'display_name' => $user->name,
            ]
        );
    }

    public function activeSubscription(?User $user): ?UserSupportSubscription
    {
        if (! $user) {
            return null;
        }

        return $user->activeSupportSubscription();
    }

    public function hasActiveSupport(?User $user): bool
    {
        return $this->activeSubscription($user) !== null;
    }

    public function status(?User $user): string
    {
        if (! $user) {
            return UserSupportSubscription::STATUS_INACTIVE;
        }

        return $user->supporterStatus();
    }

    public function endsAt(?User $user): ?Carbon
    {
        $value = $user?->supporterEndsAt();

        return $value instanceof Carbon ? $value : null;
    }

    public function totalActiveSupporters(): int
    {
        return (int) UserSupportSubscription::query()
            ->active()
            ->distinct('user_id')
            ->count('user_id');
    }

    public function unlockCommunityGoals(): void
    {
        $count = $this->totalActiveSupporters();

        CommunitySupportGoal::query()
            ->where('goal_count', '<=', $count)
            ->where('is_unlocked', false)
            ->update([
                'is_unlocked' => true,
                'unlocked_at' => now(),
            ]);
    }

    public function isFoundingSupporter(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        $earliestSubscription = $user->supportSubscriptions()
            ->whereNotNull('started_at')
            ->orderBy('started_at')
            ->orderBy('id')
            ->first();

        if (! $earliestSubscription) {
            return false;
        }

        $plan = $this->ensureDefaultPlan();
        $planLaunch = $plan->created_at instanceof Carbon ? $plan->created_at->copy() : now();
        $cutoff = $planLaunch->copy()->addDays((int) config('supporter.founder.window_days', 30));

        if ($earliestSubscription->started_at?->lte($cutoff)) {
            return true;
        }

        $supporterRank = UserSupportSubscription::query()
            ->whereNotNull('started_at')
            ->where('started_at', '<=', $earliestSubscription->started_at)
            ->distinct('user_id')
            ->count('user_id');

        return $supporterRank <= (int) config('supporter.founder.max_supporters', 100);
    }

    public function supporterMonths(?User $user): int
    {
        if (! $user) {
            return 0;
        }

        $firstStartedAt = $user->supportSubscriptions()
            ->whereNotNull('started_at')
            ->orderBy('started_at')
            ->value('started_at');

        if (! $firstStartedAt) {
            return 0;
        }

        $start = Carbon::parse($firstStartedAt)->startOfMonth();
        $end = $this->hasActiveSupport($user)
            ? now()->startOfMonth()
            : Carbon::parse(
                $user->supportSubscriptions()->current()->value('ended_at')
                ?: $user->supportSubscriptions()->current()->value('current_period_end')
                ?: now()
            )->startOfMonth();

        return max(0, $start->diffInMonths($end) + 1);
    }

    public function loyaltyBadge(?User $user): ?string
    {
        $months = $this->supporterMonths($user);
        $badge = null;

        foreach ((array) config('supporter.loyalty_badges', []) as $threshold => $label) {
            if ($months >= (int) $threshold) {
                $badge = (string) $label;
            }
        }

        return $badge;
    }

    public function wallMembers(int $limit = 90): Collection
    {
        $users = User::query()
            ->whereHas('supportSubscriptions', fn ($query) => $query->active())
            ->whereHas('supportPublicProfile', fn ($query) => $query->where('is_visible_on_wall', true))
            ->with(['supportPublicProfile'])
            ->orderBy('name')
            ->limit($limit)
            ->get();

        return $users->map(function (User $user): array {
            $profile = $user->supportPublicProfile;

            return [
                'user_id' => $user->id,
                'name' => (string) ($profile?->display_name ?: $user->name),
                'avatar_url' => $user->display_avatar_url,
                'is_founder' => $this->isFoundingSupporter($user),
            ];
        })->values();
    }

    public function summary(?User $user): array
    {
        $subscription = $this->activeSubscription($user);
        $badge = $this->loyaltyBadge($user);

        return [
            'is_active' => $subscription !== null,
            'status' => $this->status($user),
            'ends_at' => $this->endsAt($user),
            'months' => $this->supporterMonths($user),
            'is_founder' => $this->isFoundingSupporter($user),
            'loyalty_badge' => $badge,
            'current_plan_name' => $subscription?->plan?->name,
            'current_plan_key' => $subscription?->plan?->key,
            'profile' => $user ? $this->ensurePublicProfile($user) : null,
            'active_rewards_count' => $user
                ? (int) SupporterMonthlyReward::query()->where('user_id', $user->id)->count()
                : 0,
        ];
    }
}
