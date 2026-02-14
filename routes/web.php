<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminAuditController;
use App\Http\Controllers\AdminMarketController;
use App\Http\Controllers\AdminMatchController;
use App\Http\Controllers\AdminPointsController;
use App\Http\Controllers\AdminRedemptionController;
use App\Http\Controllers\AdminRewardController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\PointActivityController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RankExampleController;
use App\Http\Controllers\RedemptionController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\TicketController;

// dashboard pages
Route::get('/', function () {
    return view('pages.dashboard.ecommerce', ['title' => 'E-commerce Dashboard']);
})->name('dashboard');

// calender pages
Route::get('/calendar', function () {
    return view('pages.calender', ['title' => 'Calendar']);
})->name('calendar');

// profile pages
Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::get('/profile/rank', [RankExampleController::class, 'show'])->name('profile.rank.show');
Route::get('/ranks', function () {
    return view('pages.ranks.index', ['title' => 'Ranks']);
})->name('ranks.index');

// form pages
Route::get('/form-elements', function () {
    return view('pages.form.form-elements', ['title' => 'Form Elements']);
})->name('form-elements');

// tables pages
Route::get('/basic-tables', function () {
    return view('pages.tables.basic-tables', ['title' => 'Basic Tables']);
})->name('basic-tables');

// pages

Route::get('/blank', function () {
    return view('pages.blank', ['title' => 'Blank']);
})->name('blank');

// error pages
Route::get('/error-404', function () {
    return view('pages.errors.error-404', ['title' => 'Error 404']);
})->name('error-404');

// chart pages
Route::get('/line-chart', function () {
    return view('pages.chart.line-chart', ['title' => 'Line Chart']);
})->name('line-chart');

Route::get('/bar-chart', function () {
    return view('pages.chart.bar-chart', ['title' => 'Bar Chart']);
})->name('bar-chart');


// authentication pages
Route::get('/signin', [AuthController::class, 'showSignIn'])->name('signin');
Route::post('/signin', [AuthController::class, 'signIn'])->middleware('throttle:auth')->name('signin.store');

Route::get('/signup', [AuthController::class, 'showSignUp'])->name('signup');
Route::post('/signup', [AuthController::class, 'signUp'])->middleware('throttle:auth')->name('signup.store');

Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('auth.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('auth.callback');
Route::post('/logout', [SocialAuthController::class, 'logout'])->name('logout');

// ui elements pages
Route::get('/alerts', function () {
    return view('pages.ui-elements.alerts', ['title' => 'Alerts']);
})->name('alerts');

Route::get('/avatars', function () {
    return view('pages.ui-elements.avatars', ['title' => 'Avatars']);
})->name('avatars');

Route::get('/badge', function () {
    return view('pages.ui-elements.badges', ['title' => 'Badges']);
})->name('badges');

Route::get('/buttons', function () {
    return view('pages.ui-elements.buttons', ['title' => 'Buttons']);
})->name('buttons');

Route::get('/image', function () {
    return view('pages.ui-elements.images', ['title' => 'Images']);
})->name('images');

Route::get('/videos', function () {
    return view('pages.ui-elements.videos', ['title' => 'Videos']);
})->name('videos');

// leaderboard endpoints
Route::get('/leaderboard/all-time', [LeaderboardController::class, 'allTime'])->name('leaderboard.all-time');
Route::get('/leaderboard/weekly', [LeaderboardController::class, 'weekly'])->name('leaderboard.weekly');
Route::get('/leaderboard/monthly', [LeaderboardController::class, 'monthly'])->name('leaderboard.monthly');
Route::get('/rewards', [RewardController::class, 'index'])->name('rewards.index');
Route::get('/rewards/{slug}', [RewardController::class, 'show'])->name('rewards.show');

Route::middleware('auth')->group(function (): void {
    Route::get('/points/activity', [PointActivityController::class, 'index'])->name('points.activity');

    // user matches & predictions
    Route::get('/matches', [MatchController::class, 'index'])->name('matches.index');
    Route::get('/matches/{match}', [MatchController::class, 'show'])->name('matches.show');
    Route::post('/matches/{match}/predictions', [PredictionController::class, 'store'])
        ->middleware('throttle:prediction-create')
        ->name('matches.predictions.store');
    Route::get('/me/predictions', [PredictionController::class, 'me'])->name('me.predictions.index');
    Route::post('/matches/{match}/tickets', [TicketController::class, 'store'])
        ->middleware('throttle:ticket-create')
        ->name('matches.tickets.store');
    Route::get('/me/tickets', [TicketController::class, 'me'])->name('me.tickets.index');
    Route::get('/me/tickets/{ticket}', [TicketController::class, 'show'])->name('me.tickets.show');
    Route::post('/rewards/{reward}/redeem', [RedemptionController::class, 'store'])
        ->middleware('throttle:reward-redeem')
        ->name('rewards.redeem');
    Route::get('/me/redemptions', [RedemptionController::class, 'myIndex'])->name('me.redemptions.index');
    Route::post('/me/redemptions/{redemption}/cancel', [RedemptionController::class, 'cancel'])->name('me.redemptions.cancel');

    // admin matches management
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/points', [AdminPointsController::class, 'index'])->name('points.index');
        Route::post('/points', [AdminPointsController::class, 'store'])
            ->middleware('throttle:admin-critical')
            ->name('points.store');
        Route::get('/points/metrics', [AdminPointsController::class, 'metrics'])->name('points.metrics');
        Route::get('/rewards', [AdminRewardController::class, 'index'])->name('rewards.index');
        Route::post('/rewards', [AdminRewardController::class, 'store'])->name('rewards.store');
        Route::put('/rewards/{reward}', [AdminRewardController::class, 'update'])->name('rewards.update');
        Route::delete('/rewards/{reward}', [AdminRewardController::class, 'destroy'])->name('rewards.delete');
        Route::get('/redemptions', [AdminRedemptionController::class, 'index'])->name('redemptions.index');
        Route::post('/redemptions/{redemption}/approve', [AdminRedemptionController::class, 'approve'])
            ->middleware('throttle:admin-critical')
            ->name('redemptions.approve');
        Route::post('/redemptions/{redemption}/reject', [AdminRedemptionController::class, 'reject'])
            ->middleware('throttle:admin-critical')
            ->name('redemptions.reject');
        Route::post('/redemptions/{redemption}/ship', [AdminRedemptionController::class, 'ship'])
            ->middleware('throttle:admin-critical')
            ->name('redemptions.ship');

        Route::get('/matches', [AdminMatchController::class, 'index'])->name('matches.index');
        Route::post('/matches', [AdminMatchController::class, 'store'])->name('matches.store');
        Route::put('/matches/{match}', [AdminMatchController::class, 'update'])->name('matches.update');
        Route::post('/matches/{match}/open', [AdminMatchController::class, 'open'])->name('matches.open');
        Route::post('/matches/{match}/lock', [AdminMatchController::class, 'lock'])->name('matches.lock');
        Route::post('/matches/{match}/live', [AdminMatchController::class, 'live'])->name('matches.live');
        Route::post('/matches/{match}/cancel', [AdminMatchController::class, 'cancel'])->name('matches.cancel');
        Route::post('/matches/{match}/complete', [AdminMatchController::class, 'complete'])
            ->middleware('throttle:admin-critical')
            ->name('matches.complete');
        Route::post('/matches/{match}/settle', [AdminMatchController::class, 'settle'])
            ->middleware('throttle:admin-critical')
            ->name('matches.settle');
        Route::get('/matches/{match}/tickets', [AdminMatchController::class, 'tickets'])->name('matches.tickets');

        Route::post('/matches/{match}/markets', [AdminMarketController::class, 'store'])->name('markets.store');
        Route::put('/markets/{market}', [AdminMarketController::class, 'update'])->name('markets.update');
        Route::post('/markets/{market}/options', [AdminMarketController::class, 'storeOption'])->name('markets.options.store');
        Route::put('/market-options/{option}', [AdminMarketController::class, 'updateOption'])->name('markets.options.update');
        Route::post('/markets/{market}/lock', [AdminMarketController::class, 'lock'])->name('markets.lock');
        Route::post('/markets/{market}/settle', [AdminMarketController::class, 'settle'])
            ->middleware('throttle:admin-critical')
            ->name('markets.settle');
        Route::get('/audit', [AdminAuditController::class, 'index'])->name('audit.index');
    });
});







