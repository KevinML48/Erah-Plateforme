<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
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
