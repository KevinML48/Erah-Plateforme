<?php

namespace App\Providers;

use App\Models\Clip;
use App\Models\ClipComment;
use App\Policies\ClipPolicy;
use App\Policies\CommentPolicy;
use App\Services\MarketingHomeActivityService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as BladeView;
use Laravel\Cashier\Cashier;
use SocialiteProviders\Discord\Provider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Cashier::ignoreRoutes();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Clip::class, ClipPolicy::class);
        Gate::policy(ClipComment::class, CommentPolicy::class);

        View::composer('marketing.index', function (BladeView $view): void {
            $user = Auth::user();
            $activityData = app(MarketingHomeActivityService::class)->build($user);

            $view->with([
                'homeQuickAccess' => [
                    'quick_stats' => $activityData['quick_stats'],
                ],
                'homeEnCeMoment' => [
                    'activity_items' => $activityData['activity_items'],
                ],
            ]);
        });

        Event::listen(function (SocialiteWasCalled $event): void {
            $event->extendSocialite('discord', Provider::class);
        });

        RateLimiter::for('auth-login', function (Request $request) {
            $email = Str::lower((string) $request->input('email'));

            return Limit::perMinute(10)->by($request->ip().'|'.$email);
        });

        RateLimiter::for('social-auth', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        RateLimiter::for('points-grant', function (Request $request) {
            $identifier = $request->user()?->id ? 'user:'.$request->user()->id : 'ip:'.$request->ip();

            return Limit::perMinute(60)->by($identifier);
        });

        RateLimiter::for('notifications-read', function (Request $request) {
            $identifier = $request->user()?->id ? 'user:'.$request->user()->id : 'ip:'.$request->ip();

            return Limit::perMinute(120)->by($identifier);
        });

        RateLimiter::for('notification-settings', function (Request $request) {
            $identifier = $request->user()?->id ? 'user:'.$request->user()->id : 'ip:'.$request->ip();

            return Limit::perMinute(40)->by($identifier);
        });

        RateLimiter::for('devices', function (Request $request) {
            $identifier = $request->user()?->id ? 'user:'.$request->user()->id : 'ip:'.$request->ip();

            return Limit::perMinute(80)->by($identifier);
        });

        RateLimiter::for('clips-feed', function (Request $request) {
            $identifier = $request->user()?->id ? 'user:'.$request->user()->id : 'ip:'.$request->ip();

            return Limit::perMinute(180)->by($identifier);
        });

        RateLimiter::for('clips-interactions', function (Request $request) {
            $identifier = $request->user()?->id ? 'user:'.$request->user()->id : 'ip:'.$request->ip();

            return Limit::perMinute(120)->by($identifier);
        });

        RateLimiter::for('clips-admin', function (Request $request) {
            $identifier = $request->user()?->id ? 'user:'.$request->user()->id : 'ip:'.$request->ip();

            return Limit::perMinute(80)->by($identifier);
        });

        RateLimiter::for('duels-create', function (Request $request) {
            $identifier = $request->user()?->id ? 'user:'.$request->user()->id : 'ip:'.$request->ip();

            return Limit::perMinute(40)->by($identifier);
        });

        RateLimiter::for('duels-actions', function (Request $request) {
            $identifier = $request->user()?->id ? 'user:'.$request->user()->id : 'ip:'.$request->ip();

            return Limit::perMinute(100)->by($identifier);
        });

        RateLimiter::for('duels-read', function (Request $request) {
            $identifier = $request->user()?->id ? 'user:'.$request->user()->id : 'ip:'.$request->ip();

            return Limit::perMinute(140)->by($identifier);
        });

        RateLimiter::for('matches-read', function (Request $request) {
            $identifier = $request->user()?->id ? 'user:'.$request->user()->id : 'ip:'.$request->ip();

            return Limit::perMinute(160)->by($identifier);
        });

        RateLimiter::for('bets-place', function (Request $request) {
            $identifier = $request->user()?->id ? 'user:'.$request->user()->id : 'ip:'.$request->ip();

            return Limit::perMinute(80)->by($identifier);
        });

        RateLimiter::for('bets-read', function (Request $request) {
            $identifier = $request->user()?->id ? 'user:'.$request->user()->id : 'ip:'.$request->ip();

            return Limit::perMinute(120)->by($identifier);
        });

        RateLimiter::for('gifts-redeem', function (Request $request) {
            $identifier = $request->user()?->id ? 'user:'.$request->user()->id : 'ip:'.$request->ip();

            return Limit::perMinute(20)->by($identifier);
        });

        RateLimiter::for('matches-admin', function (Request $request) {
            $identifier = $request->user()?->id ? 'user:'.$request->user()->id : 'ip:'.$request->ip();

            return Limit::perMinute(60)->by($identifier);
        });

        RateLimiter::for('supporter-checkout', function (Request $request) {
            $identifier = $request->user()?->id ? 'user:'.$request->user()->id : 'ip:'.$request->ip();

            return Limit::perMinute(12)->by($identifier);
        });

        RateLimiter::for('supporter-votes', function (Request $request) {
            $identifier = $request->user()?->id ? 'user:'.$request->user()->id : 'ip:'.$request->ip();

            return Limit::perMinute(30)->by($identifier);
        });

        RateLimiter::for('supporter-reactions', function (Request $request) {
            $identifier = $request->user()?->id ? 'user:'.$request->user()->id : 'ip:'.$request->ip();

            return Limit::perMinute(45)->by($identifier);
        });

        RateLimiter::for('stripe-webhook', function (Request $request) {
            return Limit::perMinute(120)->by($request->ip());
        });
    }
}
