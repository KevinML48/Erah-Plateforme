<?php

namespace App\Services;

use App\Models\SupporterPlan;
use App\Models\User;
use App\Models\UserSupportSubscription;
use Illuminate\Support\Carbon;
use Laravel\Cashier\Subscription as CashierSubscription;

class SyncStripeSubscriptionStatus
{
    public function __construct(
        private readonly SupporterAccessResolver $supporterAccessResolver
    ) {
    }

    public function execute(User $user, ?array $providerData = null): ?UserSupportSubscription
    {
        $this->supporterAccessResolver->ensureConfiguredPlans();
        $cashierSubscription = $user->subscriptions()
            ->where('type', (string) config('supporter.plan.subscription_type'))
            ->orderByDesc('id')
            ->first();

        if (! $cashierSubscription instanceof CashierSubscription) {
            $latest = $user->supportSubscriptions()->current()->first();

            if ($latest && $latest->status === UserSupportSubscription::STATUS_PENDING_CHECKOUT) {
                $latest->status = UserSupportSubscription::STATUS_EXPIRED;
                $latest->ended_at = $latest->ended_at ?: now();
                $latest->save();
            }

            return $latest;
        }

        $status = $this->mapCashierStatus($cashierSubscription);
        $existing = UserSupportSubscription::query()
            ->where('provider_subscription_id', (string) $cashierSubscription->stripe_id)
            ->orWhere(function ($query) use ($user): void {
                $query->where('user_id', $user->id)->where('status', UserSupportSubscription::STATUS_PENDING_CHECKOUT);
            })
            ->orderByDesc('id')
            ->first();

        $currentPeriodStart = isset($providerData['current_period_start'])
            ? Carbon::createFromTimestamp((int) $providerData['current_period_start'])
            : $existing?->current_period_start;
        $currentPeriodEnd = isset($providerData['current_period_end'])
            ? Carbon::createFromTimestamp((int) $providerData['current_period_end'])
            : $existing?->current_period_end;
        $startedAt = isset($providerData['start_date'])
            ? Carbon::createFromTimestamp((int) $providerData['start_date'])
            : ($existing?->started_at ?: $cashierSubscription->created_at);
        $providerPriceId = $providerData['items']['data'][0]['price']['id']
            ?? $cashierSubscription->stripe_price;
        $plan = $this->supporterAccessResolver->planForPriceId((string) $providerPriceId)
            ?: ($existing?->supporter_plan_id ? SupporterPlan::query()->find($existing->supporter_plan_id) : null)
            ?: $this->supporterAccessResolver->ensureDefaultPlan();
        $subscription = UserSupportSubscription::query()->updateOrCreate(
            ['provider_subscription_id' => (string) $cashierSubscription->stripe_id],
            [
                'user_id' => $user->id,
                'supporter_plan_id' => $plan->id,
                'status' => $status,
                'provider' => 'stripe',
                'provider_customer_id' => $user->stripe_id,
                'provider_price_id' => $providerPriceId,
                'checkout_session_id' => $existing?->checkout_session_id,
                'started_at' => $startedAt,
                'current_period_start' => $currentPeriodStart,
                'current_period_end' => $currentPeriodEnd,
                'canceled_at' => $cashierSubscription->canceled() ? $cashierSubscription->updated_at : null,
                'ended_at' => $cashierSubscription->ended() ? ($cashierSubscription->ends_at ?: now()) : null,
                'meta' => [
                    'cashier_subscription_id' => $cashierSubscription->id,
                    'stripe_status' => $cashierSubscription->stripe_status,
                ],
            ]
        );

        UserSupportSubscription::query()
            ->where('user_id', $user->id)
            ->where('id', '!=', $subscription->id)
            ->whereIn('status', [
                UserSupportSubscription::STATUS_ACTIVE,
                UserSupportSubscription::STATUS_PENDING_CHECKOUT,
                UserSupportSubscription::STATUS_PAST_DUE,
            ])
            ->update([
                'status' => UserSupportSubscription::STATUS_EXPIRED,
                'ended_at' => now(),
            ]);

        $this->supporterAccessResolver->ensurePublicProfile($user);
        $this->supporterAccessResolver->ensureCommunityGoals();
        $this->supporterAccessResolver->unlockCommunityGoals();

        return $subscription;
    }

    public function executeByStripeCustomerId(string $providerCustomerId, ?array $providerData = null): ?UserSupportSubscription
    {
        $user = User::query()->where('stripe_id', $providerCustomerId)->first();

        return $user ? $this->execute($user, $providerData) : null;
    }

    private function mapCashierStatus(CashierSubscription $subscription): string
    {
        if ($subscription->ended()) {
            return UserSupportSubscription::STATUS_EXPIRED;
        }

        if ($subscription->canceled()) {
            return UserSupportSubscription::STATUS_CANCELED;
        }

        if ($subscription->pastDue()) {
            return UserSupportSubscription::STATUS_PAST_DUE;
        }

        if ($subscription->incomplète()) {
            return UserSupportSubscription::STATUS_PENDING_CHECKOUT;
        }

        if ($subscription->active() || $subscription->onTrial()) {
            return UserSupportSubscription::STATUS_ACTIVE;
        }

        return UserSupportSubscription::STATUS_INACTIVE;
    }
}
