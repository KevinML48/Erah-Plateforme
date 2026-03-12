<?php

use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\App\ShortcutController;
use App\Http\Controllers\Web\Assistant\AssistantConsoleController;
use App\Http\Controllers\Web\Assistant\AssistantConversationController;
use App\Http\Controllers\Web\Assistant\AssistantMessageController;
use App\Http\Controllers\TestConsole\AdminGiftConsoleController;
use App\Http\Controllers\TestConsole\RankingConsoleController;
use App\Http\Controllers\TestConsole\UsersConsoleController;
use App\Http\Controllers\TestConsole\WalletsConsoleController;
use App\Http\Controllers\Web\Admin\AdminMatchController;
use App\Http\Controllers\Web\Admin\AdminDuelResultController;
use App\Http\Controllers\Web\Admin\AdminDashboardController;
use App\Http\Controllers\Web\Admin\AdminLiveCodeController;
use App\Http\Controllers\Web\Admin\AdminMissionController;
use App\Http\Controllers\Web\Admin\AdminOperationsController;
use App\Http\Controllers\Web\Admin\AdminPlatformEventController;
use App\Http\Controllers\Web\Admin\AdminQuizController;
use App\Http\Controllers\Web\Admin\AdminWalletController;
use App\Http\Controllers\Web\Admin\ClipCampaignAdminController;
use App\Http\Controllers\Web\Admin\ClipsAdminController;
use App\Http\Controllers\Web\Admin\GalleryPhotoAdminController;
use App\Http\Controllers\Web\Admin\PublicProfileModerationController;
use App\Http\Controllers\Web\Admin\SupportersAdminController;
use App\Http\Controllers\Marketing\ContactController as MarketingContactController;
use App\Http\Controllers\Marketing\GalleryPhotoPageController;
use App\Http\Controllers\Marketing\PageController as MarketingPageController;
use App\Http\Controllers\Web\BetPageController;
use App\Http\Controllers\Web\AssistantFavoriteController;
use App\Http\Controllers\Web\ClubReviewPageController;
use App\Http\Controllers\Web\ClipSupporterController;
use App\Http\Controllers\Web\ClipsPageController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\HelpAssistantController;
use App\Http\Controllers\Web\DuelLeaderboardPageController;
use App\Http\Controllers\Web\DuelsPageController;
use App\Http\Controllers\Web\GiftPageController;
use App\Http\Controllers\Web\GuidedTourController;
use App\Http\Controllers\Web\HelpCenterController;
use App\Http\Controllers\Web\LiveCodePageController;
use App\Http\Controllers\Web\LeaderboardPageController;
use App\Http\Controllers\Web\MatchPageController;
use App\Http\Controllers\Web\MissionPageController;
use App\Http\Controllers\Web\MissionFocusController;
use App\Http\Controllers\Web\MissionClaimController;
use App\Http\Controllers\Web\NotificationsPageController;
use App\Http\Controllers\Web\OnboardingController;
use App\Http\Controllers\Web\QuizPageController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\ProfileClubReviewController;
use App\Http\Controllers\Web\PublicProfileController;
use App\Http\Controllers\Web\SettingsController;
use App\Http\Controllers\Web\ShopPageController;
use App\Http\Controllers\Web\StripeWebhookController;
use App\Http\Controllers\Web\StatisticsPageController;
use App\Http\Controllers\Web\SupporterConsoleController;
use App\Http\Controllers\Web\SupporterPageController;
use App\Http\Controllers\Web\AchievementPageController;
use App\Http\Controllers\DevConsoleController;
use App\Http\Controllers\Web\WalletPageController;
use App\Http\Controllers\Web\Admin\ClubReviewAdminController;
use App\Http\Controllers\Web\Admin\AdminHelpCenterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::prefix('dev')->middleware(['web', 'local.only'])->group(function (): void {
    Route::get('/', [DevConsoleController::class, 'index'])->name('dev.index');
    Route::get('/routes', [DevConsoleController::class, 'routes'])->name('dev.routes');
    Route::get('/data', [DevConsoleController::class, 'data'])->name('dev.data');
    Route::post('/db/reset', [DevConsoleController::class, 'dbReset'])->name('dev.db.reset');
    Route::post('/seed', [DevConsoleController::class, 'seed'])->name('dev.seed');
    Route::post('/impersonate', [DevConsoleController::class, 'impersonate'])->name('dev.impersonate');
    Route::post('/jobs/dispatch', [DevConsoleController::class, 'dispatchJob'])->name('dev.jobs.dispatch');
    Route::get('/logs', [DevConsoleController::class, 'logs'])->name('dev.logs');
    Route::get('/api', [DevConsoleController::class, 'api'])->name('dev.api');
    Route::post('/api/token', [DevConsoleController::class, 'apiToken'])->name('dev.api.token');
});

Route::middleware('throttle:social-auth')->group(function () {
    Route::get('/auth/google/redirect', [SocialAuthController::class, 'redirect'])
        ->defaults('provider', 'google');
    Route::get('/auth/google/callback', [SocialAuthController::class, 'callback'])
        ->defaults('provider', 'google');

    Route::get('/auth/discord/redirect', [SocialAuthController::class, 'redirect'])
        ->defaults('provider', 'discord');
    Route::get('/auth/discord/callback', [SocialAuthController::class, 'callback'])
        ->defaults('provider', 'discord');
});

Route::get('/supporter', [SupporterPageController::class, 'show'])->name('supporter.show');
Route::post('/supporter/checkout', [SupporterPageController::class, 'checkout'])
    ->middleware(['auth', 'throttle:supporter-checkout'])
    ->name('supporter.checkout');
Route::get('/supporter/success', [SupporterPageController::class, 'success'])
    ->middleware('auth')
    ->name('supporter.success');
Route::get('/supporter/cancel', [SupporterPageController::class, 'cancel'])->name('supporter.cancel');
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->middleware('throttle:stripe-webhook')
    ->name('stripe.webhook');

Route::prefix('app')->middleware('throttle:feed-public')->group(function () {
    Route::get('/classement', [LeaderboardPageController::class, 'index'])->name('app.leaderboards.index');
    Route::get('/classement/{leagueKey}', [LeaderboardPageController::class, 'show'])
        ->where('leagueKey', '(?!me$)[A-Za-z0-9\-_]+')
        ->name('app.leaderboards.show');
    Route::get('/clips', [ClipsPageController::class, 'index'])->name('app.clips.index');
    Route::get('/clips/{slug}', [ClipsPageController::class, 'show'])
        ->where('slug', '(?!favorites$)[A-Za-z0-9\-]+')
        ->name('app.clips.show');
    Route::get('/matchs', [MatchPageController::class, 'index'])->name('app.matches.index');
    Route::get('/matchs/{matchId}', [MatchPageController::class, 'show'])
        ->whereNumber('matchId')
        ->name('app.matches.show');
    Route::get('/statistics', StatisticsPageController::class)->name('app.statistics.index');
    Route::get('/duels/classement', DuelLeaderboardPageController::class)->name('app.duels.leaderboard');
    Route::get('/cadeaux', [GiftPageController::class, 'index'])->name('app.gifts.index');
    Route::get('/cadeaux/{giftId}', [GiftPageController::class, 'show'])
        ->whereNumber('giftId')
        ->name('app.gifts.show');
});

Route::prefix('app')->middleware('auth')->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'))->name('marketing.platform');
    Route::post('/clips/{clipId}/comments', [ClipsPageController::class, 'comment'])->name('app.clips.comment');
    Route::delete('/clips/{clipId}/comments/{commentId}', [ClipsPageController::class, 'deleteComment'])->name('app.clips.comment.delete');

    Route::get('/ma-ligue', [LeaderboardPageController::class, 'me'])->name('app.leaderboards.me');
    Route::get('/missions', [MissionPageController::class, 'index'])->name('app.missions.index');
    Route::post('/missions/{template}/focus', [MissionFocusController::class, 'store'])->name('app.missions.focus.store');
    Route::delete('/missions/{template}/focus', [MissionFocusController::class, 'destroy'])->name('app.missions.focus.destroy');
    Route::post('/missions/{mission}/claim', [MissionClaimController::class, 'store'])->name('app.missions.claim');
    Route::get('/quizzes', [QuizPageController::class, 'index'])->name('app.quizzes.index');
    Route::get('/quizzes/{slug}', [QuizPageController::class, 'show'])->name('app.quizzes.show');
    Route::post('/quizzes/{slug}/attempts', [QuizPageController::class, 'attempt'])->name('app.quizzes.attempt');
    Route::get('/live-codes', [LiveCodePageController::class, 'index'])->name('app.live-codes.index');
    Route::post('/live-codes/redeem', [LiveCodePageController::class, 'redeem'])->name('app.live-codes.redeem');
    Route::get('/achievements', AchievementPageController::class)->name('app.achievements.index');
    Route::get('/shop', [ShopPageController::class, 'index'])->name('app.shop.index');
    Route::post('/shop/{shopItemId}/purchase', [ShopPageController::class, 'purchase'])->name('app.shop.purchase');
    Route::get('/duels', [DuelsPageController::class, 'index'])->name('app.duels.index');
    Route::get('/paris', [BetPageController::class, 'index'])->name('app.bets.index');
    Route::delete('/paris/{betId}', [BetPageController::class, 'cancel'])
        ->middleware('throttle:bets-place')
        ->name('app.bets.cancel');
    Route::post('/matchs/{matchId}/paris', [MatchPageController::class, 'placeBet'])
        ->middleware('throttle:bets-place')
        ->name('app.matches.bets.store');
    Route::get('/favoris', [ClipsPageController::class, 'favorites'])->name('app.clips.favorites');
    Route::get('/notifications', [NotificationsPageController::class, 'index'])->name('app.notifications.index');
    Route::post('/notifications/read-all', [NotificationsPageController::class, 'readAll'])->name('app.notifications.read-all');
    Route::post('/notifications/{notificationId}/read', [NotificationsPageController::class, 'read'])->name('app.notifications.read');
    Route::get('/notifications/preferences', [NotificationsPageController::class, 'preferences'])->name('app.notifications.preferences');
    Route::post('/notifications/preferences', [NotificationsPageController::class, 'updatePreferences'])->name('app.notifications.preferences.update');
    Route::get('/profil', ProfileController::class)->name('app.profile');
    Route::get('/profil/transactions', [ProfileController::class, 'transactions'])->name('app.profile.transactions');
    Route::delete('/profil', [ProfileController::class, 'destroy'])->name('app.profile.destroy');
    Route::get('/raccourcis', [ShortcutController::class, 'index'])->name('app.shortcuts.index');
    Route::post('/raccourcis', [ShortcutController::class, 'update'])->name('app.shortcuts.update');
    Route::post('/raccourcis/reset', [ShortcutController::class, 'reset'])->name('app.shortcuts.reset');
});

Route::prefix('console')->middleware('throttle:feed-public')->group(function () {
    Route::get('/matches', [MatchPageController::class, 'index'])->name('matches.index');
    Route::get('/matches/{matchId}', [MatchPageController::class, 'show'])
        ->whereNumber('matchId')
        ->name('matches.show');
    Route::get('/clips', [ClipsPageController::class, 'index'])->name('clips.index');
    Route::get('/clips/{slug}', [ClipsPageController::class, 'show'])
        ->where('slug', '(?!favorites$)[A-Za-z0-9\-]+')
        ->name('clips.show');
    Route::get('/leaderboards', [LeaderboardPageController::class, 'index'])->name('leaderboards.index');
    Route::get('/leaderboards/{leagueKey}', [LeaderboardPageController::class, 'show'])
        ->where('leagueKey', '(?!me$)[A-Za-z0-9\-_]+')
        ->name('leaderboards.show');
    Route::get('/statistics', StatisticsPageController::class)->name('statistics.index');
    Route::get('/duels/classement', DuelLeaderboardPageController::class)->name('duels.leaderboard');
    Route::get('/help', [HelpCenterController::class, 'console'])->name('console.help');
    Route::get('/help/assistant', function (Request $request) {
        return redirect()->route('assistant.index', array_filter([
            'article' => $request->string('article')->toString(),
            'prompt' => $request->string('prompt')->toString(),
        ]));
    })->name('console.help.assistant');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('dashboard'));
    Route::get('/notifications/live', [NotificationsPageController::class, 'live'])->name('notifications.live');
    Route::post('/assistant/favorites', [AssistantFavoriteController::class, 'store'])->name('assistant.favorites.store');
    Route::delete('/assistant/favorites/{favorite}', [AssistantFavoriteController::class, 'destroy'])->name('assistant.favorites.destroy');

    Route::prefix('console')->group(function () {
        Route::get('/', fn () => redirect()->route('dashboard'));

        Route::get('/dashboard', DashboardController::class)->name('dashboard');
        Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding');
        Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');
        Route::get('/assistant', AssistantConsoleController::class)->name('assistant.index');
        Route::get('/guided-tour', [GuidedTourController::class, 'show'])->name('guided-tour.show');
        Route::post('/guided-tour/start', [GuidedTourController::class, 'start'])->name('guided-tour.start');
        Route::post('/guided-tour/restart', [GuidedTourController::class, 'restart'])->name('guided-tour.restart');
        Route::patch('/guided-tour', [GuidedTourController::class, 'update'])->name('guided-tour.update');
        Route::post('/assistant/messages', [AssistantMessageController::class, 'store'])
            ->middleware('throttle:assistant-chat')
            ->name('assistant.messages.store');
        Route::post('/assistant/messages/stream', [AssistantMessageController::class, 'stream'])
            ->middleware('throttle:assistant-chat')
            ->name('assistant.messages.stream');
        Route::patch('/assistant/conversations/{conversation}', [AssistantConversationController::class, 'update'])
            ->name('assistant.conversations.update');
        Route::delete('/assistant/conversations/{conversation}', [AssistantConversationController::class, 'destroy'])
            ->name('assistant.conversations.destroy');

        Route::get('/users', [UsersConsoleController::class, 'index'])->name('users.index');
        Route::post('/users/role', [UsersConsoleController::class, 'updateRole'])
            ->middleware('admin')
            ->name('users.role.update');

        Route::get('/ranking', [RankingConsoleController::class, 'index'])->name('ranking.index');
        Route::post('/ranking/grant', [RankingConsoleController::class, 'grant'])
            ->middleware(['admin', 'throttle:points-grant'])
            ->name('ranking.grant');

        Route::get('/wallets', [WalletsConsoleController::class, 'index'])->name('wallets.index');
        Route::post('/wallets/grant-bet', [WalletsConsoleController::class, 'grantBet'])
            ->middleware(['admin', 'throttle:points-grant'])
            ->name('wallets.grant-bet');
        Route::post('/wallets/grant-reward', [WalletsConsoleController::class, 'grantReward'])
            ->middleware(['admin', 'throttle:points-grant'])
            ->name('wallets.grant-reward');

        Route::post('/matches/{matchId}/bets', [MatchPageController::class, 'placeBet'])
            ->middleware('throttle:bets-place')
            ->name('matches.bets.store');

        Route::get('/bets', [BetPageController::class, 'index'])->name('bets.index');
        Route::delete('/bets/{betId}', [BetPageController::class, 'cancel'])
            ->middleware('throttle:bets-place')
            ->name('bets.cancel');

        Route::get('/wallet', [WalletPageController::class, 'index'])->name('wallet.index');
        Route::get('/supporter', [SupporterConsoleController::class, 'index'])->name('supporter.console');
        Route::post('/supporter/portal', [SupporterConsoleController::class, 'portal'])->name('supporter.portal');

        Route::get('/clips/favorites', [ClipsPageController::class, 'favorites'])->name('clips.favorites');
        Route::post('/clips/{clipId}/like', [ClipsPageController::class, 'like'])->name('clips.like');
        Route::post('/clips/{clipId}/unlike', [ClipsPageController::class, 'unlike'])->name('clips.unlike');
        Route::post('/clips/{clipId}/favorite', [ClipsPageController::class, 'favorite'])->name('clips.favorite');
        Route::post('/clips/{clipId}/unfavorite', [ClipsPageController::class, 'unfavorite'])->name('clips.unfavorite');
        Route::post('/clips/{clipId}/comments', [ClipsPageController::class, 'comment'])->name('clips.comment');
        Route::delete('/clips/{clipId}/comments/{commentId}', [ClipsPageController::class, 'deleteComment'])->name('clips.comment.delete');
        Route::post('/clips/{clipId}/share', [ClipsPageController::class, 'share'])->name('clips.share');
        Route::post('/clips/{clipId}/supporter-reaction', [ClipSupporterController::class, 'storeReaction'])
            ->middleware(['supporter.active', 'throttle:supporter-reactions'])
            ->name('clips.supporter-reactions.store');
        Route::delete('/clips/{clipId}/supporter-reaction/{reactionKey}', [ClipSupporterController::class, 'destroyReaction'])
            ->middleware(['supporter.active', 'throttle:supporter-reactions'])
            ->name('clips.supporter-reactions.destroy');
        Route::post('/clips/campaigns/{campaignId}/vote', [ClipSupporterController::class, 'vote'])
            ->middleware(['supporter.active', 'throttle:supporter-votes'])
            ->name('clips.campaigns.vote');

        Route::get('/leaderboards/me', [LeaderboardPageController::class, 'me'])->name('leaderboards.me');

        Route::get('/missions', [MissionPageController::class, 'index'])->name('missions.index');
        Route::post('/missions/{template}/focus', [MissionFocusController::class, 'store'])->name('missions.focus.store');
        Route::delete('/missions/{template}/focus', [MissionFocusController::class, 'destroy'])->name('missions.focus.destroy');
        Route::post('/missions/{mission}/claim', [MissionClaimController::class, 'store'])->name('missions.claim');
        Route::post('/missions/generate/daily', [AdminMissionController::class, 'generateDaily'])
            ->middleware('admin')
            ->name('missions.generate.daily');
        Route::post('/missions/generate/weekly', [AdminMissionController::class, 'generateWeekly'])
            ->middleware('admin')
            ->name('missions.generate.weekly');
        Route::post('/missions/generate/event-window', [AdminMissionController::class, 'generateEventWindow'])
            ->middleware('admin')
            ->name('missions.generate.event-window');
        Route::post('/missions/repair', [AdminMissionController::class, 'repair'])
            ->middleware('admin')
            ->name('missions.repair');

        Route::get('/quizzes', [QuizPageController::class, 'index'])->name('quizzes.index');
        Route::get('/quizzes/{slug}', [QuizPageController::class, 'show'])->name('quizzes.show');
        Route::post('/quizzes/{slug}/attempts', [QuizPageController::class, 'attempt'])->name('quizzes.attempt');

        Route::get('/live-codes', [LiveCodePageController::class, 'index'])->name('live-codes.index');
        Route::post('/live-codes/redeem', [LiveCodePageController::class, 'redeem'])->name('live-codes.redeem');

        Route::get('/achievements', AchievementPageController::class)->name('achievements.index');
        Route::get('/shop', [ShopPageController::class, 'index'])->name('shop.index');
        Route::post('/shop/{shopItemId}/purchase', [ShopPageController::class, 'purchase'])->name('shop.purchase');

        Route::get('/gifts', [GiftPageController::class, 'index'])->name('gifts.index');
        Route::get('/gifts/redemptions', [GiftPageController::class, 'redemptions'])->name('gifts.redemptions');
        Route::get('/gifts/redemptions/{redemptionId}', [GiftPageController::class, 'redemption'])
            ->whereNumber('redemptionId')
            ->name('gifts.redemptions.show');
        Route::get('/gifts/cart', [GiftPageController::class, 'cart'])->name('gifts.cart');
        Route::post('/gifts/cart/{giftId}', [GiftPageController::class, 'addToCart'])
            ->whereNumber('giftId')
            ->name('gifts.cart.add');
        Route::patch('/gifts/cart/items/{itemId}', [GiftPageController::class, 'updateCartItem'])
            ->whereNumber('itemId')
            ->name('gifts.cart.update');
        Route::delete('/gifts/cart/items/{itemId}', [GiftPageController::class, 'removeCartItem'])
            ->whereNumber('itemId')
            ->name('gifts.cart.remove');
        Route::post('/gifts/cart/checkout', [GiftPageController::class, 'checkoutCart'])
            ->middleware('throttle:gifts-redeem')
            ->name('gifts.cart.checkout');
        Route::get('/gifts/favorites', [GiftPageController::class, 'favorites'])->name('gifts.favorites');
        Route::post('/gifts/{giftId}/favorites/toggle', [GiftPageController::class, 'toggleFavorite'])
            ->whereNumber('giftId')
            ->name('gifts.favorites.toggle');
        Route::get('/reward-wallet', [GiftPageController::class, 'wallet'])->name('gifts.wallet');
        Route::get('/gifts/{giftId}', [GiftPageController::class, 'show'])->name('gifts.show');
        Route::post('/gifts/{giftId}/redeem', [GiftPageController::class, 'redeem'])
            ->middleware('throttle:gifts-redeem')
            ->name('gifts.redeem');

        Route::get('/notifications', [NotificationsPageController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/read-all', [NotificationsPageController::class, 'readAll'])->name('notifications.read-all');
        Route::post('/notifications/{notificationId}/read', [NotificationsPageController::class, 'read'])->name('notifications.read');
        Route::get('/notifications/preferences', [NotificationsPageController::class, 'preferences'])->name('notifications.preferences');
        Route::post('/notifications/preferences', [NotificationsPageController::class, 'updatePreferences'])->name('notifications.preferences.update');

        Route::get('/duels', [DuelsPageController::class, 'index'])->name('duels.index');
        Route::get('/duels/create', [DuelsPageController::class, 'create'])->name('duels.create');
        Route::post('/duels', [DuelsPageController::class, 'store'])->name('duels.store');
        Route::post('/duels/{duelId}/accept', [DuelsPageController::class, 'accept'])->name('duels.accept');
        Route::post('/duels/{duelId}/refuse', [DuelsPageController::class, 'refuse'])->name('duels.refuse');

        Route::get('/profile', ProfileController::class)->name('profile.show');
        Route::get('/profile/transactions', [ProfileController::class, 'transactions'])->name('profile.transactions');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::post('/profile/review', [ProfileClubReviewController::class, 'store'])->name('profile.reviews.store');
        Route::put('/profile/review', [ProfileClubReviewController::class, 'update'])->name('profile.reviews.update');
        Route::delete('/profile/review', [ProfileClubReviewController::class, 'destroy'])->name('profile.reviews.destroy');
        Route::get('/settings', SettingsController::class)->name('settings.index');

        Route::middleware('admin')->prefix('admin')->group(function () {
            Route::get('/dashboard', AdminDashboardController::class)->name('admin.dashboard');
            Route::get('/operations', AdminDashboardController::class)->name('admin.operations');
            Route::get('/dashboard/live', [AdminOperationsController::class, 'live'])->name('admin.dashboard.live');
            Route::post('/operations/gifts/{giftId}/status', [AdminOperationsController::class, 'updateGiftStatus'])->name('admin.operations.gifts.status');
            Route::post('/operations/gifts/{giftId}/stock', [AdminOperationsController::class, 'updateGiftStock'])->name('admin.operations.gifts.stock');
            Route::post('/operations/shop-items/{shopItemId}/status', [AdminOperationsController::class, 'updateShopItemStatus'])->name('admin.operations.shop-items.status');
            Route::post('/operations/shop-items/{shopItemId}/stock', [AdminOperationsController::class, 'updateShopItemStock'])->name('admin.operations.shop-items.stock');
            Route::put('/users/{user}/public-profile', [PublicProfileModerationController::class, 'update'])->name('admin.users.public-profile.update');
            Route::delete('/users/{user}/public-profile', [PublicProfileModerationController::class, 'destroy'])->name('admin.users.public-profile.destroy');
            Route::get('/supporters', [SupportersAdminController::class, 'index'])->name('admin.supporters.index');
            Route::get('/supporters/{userId}', [SupportersAdminController::class, 'show'])->name('admin.supporters.show');
            Route::get('/reviews', [ClubReviewAdminController::class, 'index'])->name('admin.reviews.index');
            Route::put('/reviews/{review}', [ClubReviewAdminController::class, 'update'])->name('admin.reviews.update');
            Route::delete('/reviews/{review}', [ClubReviewAdminController::class, 'destroy'])->name('admin.reviews.destroy');

            Route::get('/clips', [ClipsAdminController::class, 'index'])->name('admin.clips.index');
            Route::get('/clips/create', [ClipsAdminController::class, 'create'])->name('admin.clips.create');
            Route::post('/clips', [ClipsAdminController::class, 'store'])->name('admin.clips.store');
            Route::get('/clips/{clipId}/edit', [ClipsAdminController::class, 'edit'])->name('admin.clips.edit');
            Route::put('/clips/{clipId}', [ClipsAdminController::class, 'update'])->name('admin.clips.update');
            Route::post('/clips/{clipId}/publish', [ClipsAdminController::class, 'publish'])->name('admin.clips.publish');
            Route::post('/clips/{clipId}/unpublish', [ClipsAdminController::class, 'unpublish'])->name('admin.clips.unpublish');
            Route::delete('/clips/{clipId}', [ClipsAdminController::class, 'destroy'])->name('admin.clips.destroy');
            Route::get('/clips/campaigns', [ClipCampaignAdminController::class, 'index'])->name('admin.clips.campaigns.index');
            Route::post('/clips/campaigns', [ClipCampaignAdminController::class, 'store'])->name('admin.clips.campaigns.store');
            Route::put('/clips/campaigns/{campaignId}', [ClipCampaignAdminController::class, 'update'])->name('admin.clips.campaigns.update');
            Route::post('/clips/campaigns/{campaignId}/close', [ClipCampaignAdminController::class, 'close'])->name('admin.clips.campaigns.close');
            Route::post('/clips/campaigns/{campaignId}/settle', [ClipCampaignAdminController::class, 'settle'])->name('admin.clips.campaigns.settle');

            Route::get('/matches', [AdminMatchController::class, 'index'])->name('admin.matches.index');
            Route::get('/matches/create', [AdminMatchController::class, 'create'])->name('admin.matches.create');
            Route::post('/matches', [AdminMatchController::class, 'store'])
                ->middleware('throttle:matches-admin')
                ->name('admin.matches.store');
            Route::get('/matches/{matchId}/edit', [AdminMatchController::class, 'edit'])->name('admin.matches.edit');
            Route::put('/matches/{matchId}', [AdminMatchController::class, 'update'])
                ->middleware('throttle:matches-admin')
                ->name('admin.matches.update');
            Route::get('/matches/{matchId}/manage', [AdminMatchController::class, 'manage'])->name('admin.matches.manage');
            Route::post('/matches/{matchId}/status', [AdminMatchController::class, 'updateStatus'])
                ->middleware('throttle:matches-admin')
                ->name('admin.matches.status');
            Route::post('/matches/{matchId}/unlock-child-matches', [AdminMatchController::class, 'unlockChildMatches'])
                ->middleware('throttle:matches-admin')
                ->name('admin.matches.unlock-child-matches');
            Route::post('/matches/{matchId}/result', [AdminMatchController::class, 'setResult'])
                ->middleware('throttle:matches-admin')
                ->name('admin.matches.result');
            Route::post('/matches/{matchId}/settle', [AdminMatchController::class, 'settle'])
                ->middleware('throttle:matches-admin')
                ->name('admin.matches.settle');

            Route::get('/wallets/grant', [AdminWalletController::class, 'create'])->name('admin.wallets.grant.create');
            Route::post('/wallets/grant', [AdminWalletController::class, 'store'])
                ->middleware('throttle:points-grant')
                ->name('admin.wallets.grant.store');

            Route::get('/gifts', [AdminGiftConsoleController::class, 'index'])->name('admin.gifts.index');
            Route::post('/gifts', [AdminGiftConsoleController::class, 'store'])->name('admin.gifts.store');
            Route::put('/gifts/{giftId}', [AdminGiftConsoleController::class, 'update'])->name('admin.gifts.update');
            Route::delete('/gifts/{giftId}', [AdminGiftConsoleController::class, 'destroy'])->name('admin.gifts.destroy');
            Route::get('/redemptions/{redemptionId}', [AdminGiftConsoleController::class, 'showRedemption'])
                ->whereNumber('redemptionId')
                ->name('admin.redemptions.show');
            Route::post('/redemptions/{redemptionId}/approve', [AdminGiftConsoleController::class, 'approve'])->name('admin.redemptions.approve');
            Route::post('/redemptions/{redemptionId}/reject', [AdminGiftConsoleController::class, 'reject'])->name('admin.redemptions.reject');
            Route::post('/redemptions/{redemptionId}/ship', [AdminGiftConsoleController::class, 'ship'])->name('admin.redemptions.ship');
            Route::post('/redemptions/{redemptionId}/deliver', [AdminGiftConsoleController::class, 'deliver'])->name('admin.redemptions.deliver');
            Route::post('/redemptions/{redemptionId}/note', [AdminGiftConsoleController::class, 'note'])->name('admin.redemptions.note');
            Route::post('/redemptions/{redemptionId}/refund', [AdminGiftConsoleController::class, 'refund'])->name('admin.redemptions.refund');

            Route::get('/missions', [AdminMissionController::class, 'index'])->name('admin.missions.index');
            Route::post('/missions', [AdminMissionController::class, 'storeTemplate'])->name('admin.missions.store');
            Route::put('/missions/{templateId}', [AdminMissionController::class, 'updateTemplate'])->name('admin.missions.update');
            Route::delete('/missions/{templateId}', [AdminMissionController::class, 'destroyTemplate'])->name('admin.missions.destroy');
            Route::post('/quizzes', [AdminQuizController::class, 'store'])->name('admin.quizzes.store');
            Route::put('/quizzes/{quizId}', [AdminQuizController::class, 'update'])->name('admin.quizzes.update');
            Route::delete('/quizzes/{quizId}', [AdminQuizController::class, 'destroy'])->name('admin.quizzes.destroy');
            Route::post('/live-codes', [AdminLiveCodeController::class, 'store'])->name('admin.live-codes.store');
            Route::put('/live-codes/{liveCodeId}', [AdminLiveCodeController::class, 'update'])->name('admin.live-codes.update');
            Route::delete('/live-codes/{liveCodeId}', [AdminLiveCodeController::class, 'destroy'])->name('admin.live-codes.destroy');
            Route::post('/events', [AdminPlatformEventController::class, 'store'])->name('admin.events.store');
            Route::put('/events/{eventId}', [AdminPlatformEventController::class, 'update'])->name('admin.events.update');
            Route::delete('/events/{eventId}', [AdminPlatformEventController::class, 'destroy'])->name('admin.events.destroy');
            Route::post('/duels/{duelId}/result', [AdminDuelResultController::class, 'store'])->name('admin.duels.result.store');

            Route::get('/help', [AdminHelpCenterController::class, 'index'])->name('admin.help.index');
            Route::post('/help/categories', [AdminHelpCenterController::class, 'storeCategory'])->name('admin.help.categories.store');
            Route::put('/help/categories/{category}', [AdminHelpCenterController::class, 'updateCategory'])->name('admin.help.categories.update');
            Route::delete('/help/categories/{category}', [AdminHelpCenterController::class, 'destroyCategory'])->name('admin.help.categories.destroy');
            Route::post('/help/articles', [AdminHelpCenterController::class, 'storeArticle'])->name('admin.help.articles.store');
            Route::put('/help/articles/{article}', [AdminHelpCenterController::class, 'updateArticle'])->name('admin.help.articles.update');
            Route::delete('/help/articles/{article}', [AdminHelpCenterController::class, 'destroyArticle'])->name('admin.help.articles.destroy');
            Route::post('/help/glossary', [AdminHelpCenterController::class, 'storeGlossary'])->name('admin.help.glossary.store');
            Route::put('/help/glossary/{term}', [AdminHelpCenterController::class, 'updateGlossary'])->name('admin.help.glossary.update');
            Route::delete('/help/glossary/{term}', [AdminHelpCenterController::class, 'destroyGlossary'])->name('admin.help.glossary.destroy');
            Route::post('/help/tour-steps', [AdminHelpCenterController::class, 'storeTourStep'])->name('admin.help.tour-steps.store');
            Route::put('/help/tour-steps/{tourStep}', [AdminHelpCenterController::class, 'updateTourStep'])->name('admin.help.tour-steps.update');
            Route::delete('/help/tour-steps/{tourStep}', [AdminHelpCenterController::class, 'destroyTourStep'])->name('admin.help.tour-steps.destroy');

            Route::get('/gallery-photos', [GalleryPhotoAdminController::class, 'index'])->name('admin.gallery-photos.index');
            Route::post('/gallery-photos', [GalleryPhotoAdminController::class, 'store'])->name('admin.gallery-photos.store');
            Route::put('/gallery-photos/{photoId}', [GalleryPhotoAdminController::class, 'update'])->name('admin.gallery-photos.update');
            Route::delete('/gallery-photos/{photoId}', [GalleryPhotoAdminController::class, 'destroy'])->name('admin.gallery-photos.destroy');
            Route::post('/gallery-photos/{photoId}/toggle', [GalleryPhotoAdminController::class, 'toggle'])->name('admin.gallery-photos.toggle');
            Route::post('/gallery-photos/{photoId}/reorder', [GalleryPhotoAdminController::class, 'reorder'])->name('admin.gallery-photos.reorder');
        });
    });
});

Route::get('/aide', [HelpCenterController::class, 'index'])->name('help.index');
Route::get('/aide/assistant', [HelpCenterController::class, 'assistant'])->name('help.assistant.page');
Route::post('/aide/assistant', HelpAssistantController::class)
    ->middleware('throttle:20,1')
    ->name('help.assistant.ask');
Route::get('/aide/categorie/{slug}', [HelpCenterController::class, 'category'])->name('help.categories.show');
Route::get('/aide/article/{slug}', [HelpCenterController::class, 'article'])->name('help.articles.show');
Route::get('/u/{user}', PublicProfileController::class)->name('users.public');
Route::get('/avis', [ClubReviewPageController::class, 'index'])->name('reviews.index');
Route::get('/', function () {
    return response()
        ->view('marketing.index')
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
})->name('marketing.index');
Route::get('/index.html', function () {
    return response()
        ->view('marketing.index')
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
});
Route::get('/faq', fn () => redirect()->to(route('help.index').'#faq-center'))->name('marketing.faq');
Route::get('/faq.html', fn () => redirect()->to(route('help.index').'#faq-center'));
Route::get('/galerie-photos', GalleryPhotoPageController::class)->name('marketing.gallery-photos');
Route::get('/galerie-photos.html', GalleryPhotoPageController::class);
Route::get('/contact', [MarketingContactController::class, 'show'])->name('marketing.contact');
Route::post('/contact', [MarketingContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('marketing.contact.submit');

Route::get('/{slug}.html', MarketingPageController::class)
    ->where('slug', '(?!app$|console$|dev$|api$|index$|contact$)[A-Za-z0-9\-]+')
    ->name('marketing.page.html');

Route::get('/{slug}', MarketingPageController::class)
    ->where('slug', '(?!app$|console$|dev$|api$|index$|contact$)[A-Za-z0-9\-]+')
    ->name('marketing.page');
