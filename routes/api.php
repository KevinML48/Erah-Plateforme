<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\PointsGrantController;
use App\Http\Controllers\Api\Admin\ClipAdminController;
use App\Http\Controllers\Api\Admin\MatchAdminController;
use App\Http\Controllers\Api\BetController;
use App\Http\Controllers\Api\ClipController;
use App\Http\Controllers\Api\ClipInteractionController;
use App\Http\Controllers\Api\CommunityLeaderboardController;
use App\Http\Controllers\Api\DuelController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\NotificationPreferenceController;
use App\Http\Controllers\Api\PushSubscriptionController;
use App\Http\Controllers\Api\RankingController;
use App\Http\Controllers\Api\UserDeviceController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register'])
    ->middleware('throttle:auth-register');
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:auth-login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me/progress', [RankingController::class, 'meProgress']);

    Route::get('/notifications', [NotificationController::class, 'index'])
        ->middleware('throttle:notifications-read');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'read'])
        ->middleware('throttle:notifications-read');

    Route::get('/me/notification-preferences', [NotificationPreferenceController::class, 'show']);
    Route::put('/me/notification-preferences', [NotificationPreferenceController::class, 'update'])
        ->middleware('throttle:notification-settings');

    Route::post('/me/devices', [UserDeviceController::class, 'store'])
        ->middleware('throttle:devices');
    Route::post('/me/push-subscriptions', [PushSubscriptionController::class, 'store'])
        ->middleware('throttle:devices');
    Route::delete('/me/push-subscriptions', [PushSubscriptionController::class, 'destroy'])
        ->middleware('throttle:devices');

    Route::post('/bets', [BetController::class, 'store'])
        ->middleware('throttle:bets-place');
    Route::get('/bets/me', [BetController::class, 'myBets'])
        ->middleware('throttle:bets-read');

    Route::middleware('throttle:clips-interactions')->group(function () {
        Route::post('/clips/{id}/like', [ClipInteractionController::class, 'like']);
        Route::delete('/clips/{id}/like', [ClipInteractionController::class, 'unlike']);
        Route::post('/clips/{id}/favorite', [ClipInteractionController::class, 'favorite']);
        Route::delete('/clips/{id}/favorite', [ClipInteractionController::class, 'unfavorite']);
        Route::post('/clips/{id}/comments', [ClipInteractionController::class, 'comment']);
        Route::delete('/clips/{clipId}/comments/{commentId}', [ClipInteractionController::class, 'deleteComment']);
        Route::post('/clips/{id}/share', [ClipInteractionController::class, 'share']);
    });

    Route::get('/duels', [DuelController::class, 'index'])
        ->middleware('throttle:duels-read');
    Route::post('/duels', [DuelController::class, 'store'])
        ->middleware('throttle:duels-create');
    Route::post('/duels/{id}/accept', [DuelController::class, 'accept'])
        ->middleware('throttle:duels-actions');
    Route::post('/duels/{id}/refuse', [DuelController::class, 'refuse'])
        ->middleware('throttle:duels-actions');
});

Route::get('/leagues', [RankingController::class, 'leagues'])
    ->middleware('throttle:feed-public');
Route::get('/leagues/{key}/leaderboard', [RankingController::class, 'leaderboard'])
    ->middleware('throttle:feed-public');
Route::get('/community/leaderboards', CommunityLeaderboardController::class)
    ->middleware('throttle:feed-public');

Route::middleware('throttle:clips-feed')->group(function () {
    Route::get('/clips', [ClipController::class, 'index']);
    Route::get('/clips/{slug}', [ClipController::class, 'show']);
});

Route::get('/matches', [MatchController::class, 'index'])
    ->middleware('throttle:matches-read');

Route::middleware(['auth:sanctum', 'admin', 'throttle:points-grant'])->group(function () {
    Route::post('/admin/points/grant', PointsGrantController::class);
});

Route::middleware(['auth:sanctum', 'admin', 'throttle:clips-admin'])->group(function () {
    Route::post('/admin/clips', [ClipAdminController::class, 'store']);
    Route::put('/admin/clips/{id}', [ClipAdminController::class, 'update']);
    Route::delete('/admin/clips/{id}', [ClipAdminController::class, 'destroy']);
    Route::post('/admin/clips/{id}/publish', [ClipAdminController::class, 'publish']);
});

Route::middleware(['auth:sanctum', 'admin', 'throttle:matches-admin'])->group(function () {
    Route::post('/admin/matches', [MatchAdminController::class, 'store']);
    Route::post('/admin/matches/{id}/settle', [MatchAdminController::class, 'settle']);
});
