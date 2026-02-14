<?php

namespace App\Providers;

use App\Models\User;
use App\Models\PointLog;
use App\Models\EsportMatch;
use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Policies\MatchPolicy;
use App\Policies\PointLogPolicy;
use App\Policies\RedemptionPolicy;
use App\Policies\RewardPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use SocialiteProviders\Discord\Provider as DiscordProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(function (SocialiteWasCalled $event): void {
            $event->extendSocialite('discord', DiscordProvider::class);
        });

        Gate::define('manage-points', fn (?User $user): bool => (bool) ($user?->isAdmin()));
        Gate::define('manage-match', fn (?User $user): bool => (bool) ($user?->isAdmin()));
        Gate::define('manage-market', fn (?User $user): bool => (bool) ($user?->isAdmin()));
        Gate::define('manage-rewards', fn (?User $user): bool => (bool) ($user?->isAdmin()));
        Gate::define('manage-redemptions', fn (?User $user): bool => (bool) ($user?->isAdmin()));

        Gate::policy(PointLog::class, PointLogPolicy::class);
        Gate::policy(EsportMatch::class, MatchPolicy::class);
        Gate::policy(Reward::class, RewardPolicy::class);
        Gate::policy(RewardRedemption::class, RedemptionPolicy::class);

        View::composer('*', function ($view): void {
            static $resolvedUser = null;
            static $resolved = false;

            if (!$resolved) {
                $resolvedUser = auth()->user() ?? User::query()->first();
                $resolved = true;
            }

            $displayName = $resolvedUser?->name ?: 'Guest';
            $firstName = explode(' ', trim($displayName))[0] ?? $displayName;

            $view->with('currentUser', $resolvedUser);
            $view->with('currentUserDisplayName', $displayName);
            $view->with('currentUserFirstName', $firstName);
            $view->with('currentUserEmail', $resolvedUser?->email ?: 'no-email@example.com');
            $view->with('currentUserAvatar', $resolvedUser?->avatar_url);
        });
    }
}
