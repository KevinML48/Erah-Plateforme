<?php

namespace App\Http\Controllers\Web;

use App\Models\UserSupportSubscription;
use App\Services\SyncStripeSubscriptionStatus;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;
use Symfony\Component\HttpFoundation\Response;

class StripeWebhookController extends CashierWebhookController
{
    public function __construct(
        private readonly SyncStripeSubscriptionStatus $syncStripeSubscriptionStatus
    ) {
        parent::__construct();
    }

    protected function newSubscriptionType(array $payload)
    {
        return (string) config('supporter.plan.subscription_type', 'supporter');
    }

    protected function handleCheckoutSessionCompleted(array $payload)
    {
        $data = $payload['data']['object'] ?? [];

        if (($data['mode'] ?? null) === 'subscription') {
            UserSupportSubscription::query()
                ->where('checkout_session_id', (string) ($data['id'] ?? ''))
                ->update([
                    'provider_customer_id' => $data['customer'] ?? null,
                    'provider_subscription_id' => $data['subscription'] ?? null,
                    'status' => UserSupportSubscription::STATUS_PENDING_CHECKOUT,
                ]);

            $this->syncFromPayload($payload);
        }

        return $this->successMethod();
    }

    protected function handleCustomerSubscriptionCreated(array $payload)
    {
        $response = parent::handleCustomerSubscriptionCreated($payload);
        $this->syncFromPayload($payload);

        return $response;
    }

    protected function handleCustomerSubscriptionUpdated(array $payload)
    {
        $response = parent::handleCustomerSubscriptionUpdated($payload);
        $this->syncFromPayload($payload);

        return $response;
    }

    protected function handleCustomerSubscriptionDeleted(array $payload)
    {
        $response = parent::handleCustomerSubscriptionDeleted($payload);
        $this->syncFromPayload($payload);

        return $response;
    }

    private function syncFromPayload(array $payload): void
    {
        $customerId = (string) data_get($payload, 'data.object.customer', '');

        if ($customerId !== '') {
            $this->syncStripeSubscriptionStatus->executeByStripeCustomerId(
                $customerId,
                (array) data_get($payload, 'data.object', [])
            );
        }
    }
}
