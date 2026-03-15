<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSupportSubscription;
use Laravel\Cashier\Checkout;
use RuntimeException;

class CreateSupporterCheckoutSession
{
    public function __construct(
        private readonly SupporterAccessResolver $supporterAccessResolver,
        private readonly SyncStripeSubscriptionStatus $syncStripeSubscriptionStatus
    ) {
    }

    public function execute(User $user, ?string $planKey = null): Checkout
    {
        $existing = $this->syncStripeSubscriptionStatus->execute($user);

        if ($existing?->status === UserSupportSubscription::STATUS_ACTIVE) {
            throw new RuntimeException('Un abonnement supporter actif existe deja pour ce compte.');
        }

        $plan = $this->supporterAccessResolver->resolvePlan($planKey);
        $priceId = trim((string) ($plan->stripe_price_id ?? ''));

        if ($priceId === '') {
            throw new RuntimeException('Le prix Stripe de cette formule supporter n'est pas configure.');
        }

        $this->supporterAccessResolver->ensurePublicProfile($user);
        $this->supporterAccessResolver->ensureConfiguredPlans();
        $this->supporterAccessResolver->ensureCommunityGoals();

        $checkout = $user->newSubscription(
            (string) config('supporter.plan.subscription_type'),
            $priceId
        )->checkout([
            'success_url' => route('supporter.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('supporter.cancel'),
            'subscription_data' => [
                'metadata' => [
                    'supporter_plan_key' => $plan->key,
                    'supporter_user_id' => (string) $user->id,
                ],
            ],
            'metadata' => [
                'supporter_plan_key' => $plan->key,
                'supporter_user_id' => (string) $user->id,
            ],
        ]);

        UserSupportSubscription::query()->updateOrCreate(
            ['checkout_session_id' => (string) $checkout->id],
            [
                'user_id' => $user->id,
                'supporter_plan_id' => $plan->id,
                'status' => UserSupportSubscription::STATUS_PENDING_CHECKOUT,
                'provider' => 'stripe',
                'provider_customer_id' => $user->createOrGetStripeCustomer()->id,
                'provider_price_id' => $priceId,
                'meta' => [
                    'checkout_url' => $checkout->url,
                ],
            ]
        );

        return $checkout;
    }
}
