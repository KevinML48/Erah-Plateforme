<?php

namespace Tests\Feature\Supporter;

use App\Models\SupporterPlan;
use App\Models\User;
use App\Services\CreateStripeCustomerPortalSession;
use App\Services\CreateSupporterCheckoutSession;
use App\Services\SyncStripeSubscriptionStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Checkout;
use Mockery;
use Stripe\Checkout\Session;
use Tests\TestCase;

class SupporterPaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_supporter_public_page_renders(): void
    {
        $response = $this->get(route('supporter.show'));

        $response->assertOk();
        $response->assertSee('Supporter ERAH');
    }

    public function test_supporter_console_inactive_state_includes_default_plan_key_for_checkout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('supporter.console'));

        $response->assertOk();
        $response->assertSee('name="plan_key"', false);
        $response->assertSee('value="'.e((string) config('supporter.plan.key')).'"', false);
    }

    public function test_supporter_checkout_requires_authentication(): void
    {
        $this->post(route('supporter.checkout'), [
            'plan_key' => (string) config('supporter.plan.key'),
        ])->assertRedirect(route('login', ['required' => 'participation']));
    }

    public function test_supporter_checkout_redirects_to_stripe_for_valid_plan(): void
    {
        $user = User::factory()->create();
        $this->seedSupporterPlans();

        $checkout = new Checkout($user, Session::constructFrom([
            'id' => 'cs_test_supporter',
            'url' => 'https://checkout.stripe.test/session/cs_test_supporter',
        ]));

        $service = Mockery::mock(CreateSupporterCheckoutSession::class);
        $service->shouldReceive('execute')
            ->once()
            ->withArgs(fn (User $givenUser, string $planKey): bool => $givenUser->is($user) && $planKey === (string) config('supporter.plan.key'))
            ->andReturn($checkout);
        $this->app->instance(CreateSupporterCheckoutSession::class, $service);

        $response = $this->actingAs($user)->post(route('supporter.checkout'), [
            'plan_key' => (string) config('supporter.plan.key'),
        ]);

        $response->assertRedirect('https://checkout.stripe.test/session/cs_test_supporter');
    }

    public function test_supporter_checkout_reports_missing_price_configuration(): void
    {
        $user = User::factory()->create();

        config()->set('supporter.plan.stripe_price_id', null);
        config()->set('supporter.plans', [[
            'key' => 'supporter-erah-monthly',
            'name' => 'Supporter ERAH Mensuel',
            'price_cents' => 500,
            'currency' => 'eur',
            'billing_interval' => 'month',
            'billing_months' => 1,
            'discount_percent' => 0,
            'sort_order' => 1,
            'stripe_price_id' => null,
            'description' => 'Plan sans prix Stripe',
        ]]);

        $response = $this->from(route('supporter.show'))
            ->actingAs($user)
            ->post(route('supporter.checkout'), [
                'plan_key' => 'supporter-erah-monthly',
            ]);

        $response->assertRedirect(route('supporter.show'));
        $response->assertSessionHas('error', 'Le prix Stripe de cette formule supporter n est pas configure.');
    }

    public function test_supporter_success_redirects_back_with_success_message(): void
    {
        $user = User::factory()->create();

        $syncService = Mockery::mock(SyncStripeSubscriptionStatus::class);
        $syncService->shouldReceive('execute')->once()->with($user);
        $this->app->instance(SyncStripeSubscriptionStatus::class, $syncService);

        $response = $this->actingAs($user)->get(route('supporter.success', [
            'session_id' => 'cs_test_supporter',
        ]));

        $response->assertRedirect(route('supporter.show'));
        $response->assertSessionHas('success', 'Le checkout supporter est confirme. Activation en cours via Stripe.');
    }

    public function test_supporter_cancel_redirects_back_with_error_message(): void
    {
        $response = $this->get(route('supporter.cancel'));

        $response->assertRedirect(route('supporter.show'));
        $response->assertSessionHas('error', 'Le checkout supporter a ete annule.');
    }

    public function test_supporter_portal_redirects_to_stripe_billing_portal(): void
    {
        $user = User::factory()->create();

        $service = Mockery::mock(CreateStripeCustomerPortalSession::class);
        $service->shouldReceive('execute')
            ->once()
            ->withArgs(fn (User $givenUser, string $returnUrl): bool => $givenUser->is($user) && $returnUrl === route('supporter.console'))
            ->andReturn('https://billing.stripe.test/session/portal_123');
        $this->app->instance(CreateStripeCustomerPortalSession::class, $service);

        $response = $this->actingAs($user)->post(route('supporter.portal'));

        $response->assertRedirect('https://billing.stripe.test/session/portal_123');
    }

    private function seedSupporterPlans(): void
    {
        collect((array) config('supporter.plans', []))->each(function (array $plan): void {
            SupporterPlan::query()->updateOrCreate(
                ['key' => (string) $plan['key']],
                [
                    'name' => (string) $plan['name'],
                    'price_cents' => (int) $plan['price_cents'],
                    'currency' => (string) $plan['currency'],
                    'billing_interval' => (string) $plan['billing_interval'],
                    'billing_months' => (int) ($plan['billing_months'] ?? 1),
                    'discount_percent' => (float) ($plan['discount_percent'] ?? 0),
                    'sort_order' => (int) ($plan['sort_order'] ?? 1),
                    'description' => (string) ($plan['description'] ?? ''),
                    'stripe_price_id' => $plan['stripe_price_id'] ?? null,
                    'is_active' => true,
                ]
            );
        });
    }
}