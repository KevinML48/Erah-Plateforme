<?php

return [
    'wallet' => [
        'initial_balance' => (int) env('BETTING_WALLET_INITIAL_BALANCE', 1000),
    ],

    'stake' => [
        'min' => (int) env('BETTING_STAKE_MIN', 1),
        'max' => (int) env('BETTING_STAKE_MAX', 10000),
    ],

    'cancellation' => [
        'window_minutes' => (int) env('BETTING_CANCEL_WINDOW_MINUTES', 60),
    ],

    'match' => [
        'default_lock_offset_minutes' => (int) env('BETTING_LOCK_OFFSET_MINUTES', 5),
        'winner_market_key' => 'WINNER',
    ],

    'odds' => [
        'winner_fixed' => (float) env('BETTING_WINNER_FIXED_ODDS', 2.0),
        'draw_fixed' => (float) env('BETTING_DRAW_FIXED_ODDS', 3.0),
    ],

    'ranking_bonus' => [
        'rank_points_on_win' => (int) env('BETTING_WIN_RANK_BONUS', 25),
        'xp_on_win' => (int) env('BETTING_WIN_XP_BONUS', 0),
    ],
];
