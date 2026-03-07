<?php

namespace App\Services;

use App\Models\User;
use RuntimeException;

class CreateStripeCustomerPortalSession
{
    public function __construct(
        private readonly SyncStripeSubscriptionStatus $syncStripeSubscriptionStatus
    ) {
    }

    public function execute(User $user, ?string $returnUrl = null): string
    {
        $this->syncStripeSubscriptionStatus->execute($user);

        if (! $user->stripe_id) {
            throw new RuntimeException('Aucun client Stripe n est associe a ce compte.');
        }

        return $user->billingPortalUrl($returnUrl ?: route('supporter.console'));
    }
}
