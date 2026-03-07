<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\GrantMonthlySupporterRewards;
use App\Services\SupporterAccessResolver;
use App\Services\SyncStripeSubscriptionStatus;
use App\Models\User;
use Illuminate\Support\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('supporter:grant-monthly-rewards {--month=}', function (GrantMonthlySupporterRewards $grantMonthlySupporterRewards) {
    $month = $this->option('month');
    $processed = $grantMonthlySupporterRewards->execute($month ? Carbon::parse($month) : null);

    $this->info('Supporters traites: '.$processed);
})->purpose('Grant monthly rewards for active supporters.');

Artisan::command('supporter:sync-subscriptions {stripeCustomerId?}', function (
    SyncStripeSubscriptionStatus $syncStripeSubscriptionStatus,
    SupporterAccessResolver $supporterAccessResolver
) {
    $customerId = (string) $this->argument('stripeCustomerId');

    if ($customerId !== '') {
        $subscription = $syncStripeSubscriptionStatus->executeByStripeCustomerId($customerId);
        $this->info($subscription ? 'Subscription synchronisee.' : 'Aucun client Stripe associe.');

        return;
    }

    $count = 0;
    User::query()->whereNotNull('stripe_id')->orderBy('id')->chunkById(100, function ($users) use ($syncStripeSubscriptionStatus, &$count): void {
        foreach ($users as $user) {
            $syncStripeSubscriptionStatus->execute($user);
            $count++;
        }
    });

    $supporterAccessResolver->unlockCommunityGoals();
    $this->info('Users synchronises: '.$count);
})->purpose('Sync supporter subscriptions from Cashier / Stripe state.');

Schedule::command('supporter:grant-monthly-rewards')->monthlyOn(1, '02:10');
