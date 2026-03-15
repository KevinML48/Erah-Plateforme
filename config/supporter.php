<?php

return [
    'plan' => [
        'key' => env('SUPPORTER_PLAN_KEY', 'supporter-erah-monthly'),
        'name' => env('SUPPORTER_PLAN_NAME', 'Supporter ERAH Mensuel'),
        'price_cents' => (int) env('SUPPORTER_PLAN_PRICE_CENTS', 500),
        'currency' => env('SUPPORTER_PLAN_CURRENCY', 'eur'),
        'billing_interval' => env('SUPPORTER_PLAN_BILLING_INTERVAL', 'month'),
        'base_monthly_price_cents' => (int) env('SUPPORTER_BASE_MONTHLY_PRICE_CENTS', 500),
        'description' => env(
            'SUPPORTER_PLAN_DESCRIPTION',
            "La plateforme reste gratuite. L'abonnement permet de soutenir le club et debloque des avantages supplementaires."
        ),
        'stripe_price_id' => env('STRIPE_SUPPORTER_PRICE_ID'),
        'subscription_type' => env('SUPPORTER_SUBSCRIPTION_TYPE', 'supporter'),
    ],

    'plans' => [
        [
            'key' => env('SUPPORTER_PLAN_KEY', 'supporter-erah-monthly'),
            'name' => env('SUPPORTER_PLAN_NAME', 'Supporter ERAH Mensuel'),
            'price_cents' => (int) env('SUPPORTER_PLAN_PRICE_CENTS', 500),
            'currency' => env('SUPPORTER_PLAN_CURRENCY', 'eur'),
            'billing_interval' => env('SUPPORTER_PLAN_BILLING_INTERVAL', 'month'),
            'billing_months' => 1,
            'discount_percent' => 0,
            'sort_order' => 1,
            'stripe_price_id' => env('STRIPE_SUPPORTER_PRICE_ID'),
            'description' => env(
                'SUPPORTER_PLAN_DESCRIPTION',
                "Paiement mensuel flexible pour soutenir ERAH et debloquer tous les avantages supporter."
            ),
        ],
        [
            'key' => env('SUPPORTER_PLAN_SEMIANNUAL_KEY', 'supporter-erah-6-months'),
            'name' => env('SUPPORTER_PLAN_SEMIANNUAL_NAME', 'Supporter ERAH 6 mois'),
            'price_cents' => (int) env('SUPPORTER_PLAN_SEMIANNUAL_PRICE_CENTS', 2760),
            'currency' => env('SUPPORTER_PLAN_CURRENCY', 'eur'),
            'billing_interval' => '6_months',
            'billing_months' => 6,
            'discount_percent' => (float) env('SUPPORTER_PLAN_SEMIANNUAL_DISCOUNT_PERCENT', 8),
            'sort_order' => 2,
            'stripe_price_id' => env('STRIPE_SUPPORTER_SEMIANNUAL_PRICE_ID'),
            'description' => env(
                'SUPPORTER_PLAN_SEMIANNUAL_DESCRIPTION',
                "Paiement tous les 6 mois avec 8% de reduction sur le total et les memes avantages supporter."
            ),
        ],
        [
            'key' => env('SUPPORTER_PLAN_ANNUAL_KEY', 'supporter-erah-yearly'),
            'name' => env('SUPPORTER_PLAN_ANNUAL_NAME', 'Supporter ERAH Annuel'),
            'price_cents' => (int) env('SUPPORTER_PLAN_ANNUAL_PRICE_CENTS', 5040),
            'currency' => env('SUPPORTER_PLAN_CURRENCY', 'eur'),
            'billing_interval' => 'year',
            'billing_months' => 12,
            'discount_percent' => (float) env('SUPPORTER_PLAN_ANNUAL_DISCOUNT_PERCENT', 16),
            'sort_order' => 3,
            'stripe_price_id' => env('STRIPE_SUPPORTER_ANNUAL_PRICE_ID'),
            'description' => env(
                'SUPPORTER_PLAN_ANNUAL_DESCRIPTION',
                "Paiement annuel en une fois avec 16% de reduction sur le total et acces supporter continu."
            ),
        ],
    ],

    'founder' => [
        'max_supporters' => (int) env('SUPPORTER_FOUNDER_MAX_SUPPORTERS', 100),
        'window_days' => (int) env('SUPPORTER_FOUNDER_WINDOW_DAYS', 30),
    ],

    'community_goals' => [
        ['goal_count' => 50, 'title' => 'Evenement communaute', 'description' => 'Activation d\'un evenement communaute ERAH.'],
        ['goal_count' => 100, 'title' => 'Animation plateforme', 'description' => 'Ajout d\'une animation plateforme reservee aux supporters.'],
        ['goal_count' => 200, 'title' => 'Projet special ERAH', 'description' => 'Lancement d\'un projet special finance par la communaute.'],
    ],

    'loyalty_badges' => [
        1 => 'Supporter actif',
        3 => 'Supporter fidele',
        6 => 'Supporter confirme',
        12 => 'Supporter legende',
    ],

    'monthly_reward' => [
        'mission_scope' => env('SUPPORTER_MONTHLY_MISSION_SCOPE', 'monthly_supporter'),
        'mission_key_prefix' => env('SUPPORTER_MONTHLY_MISSION_KEY_PREFIX', 'supporter-monthly'),
        'mission_title' => env('SUPPORTER_MONTHLY_MISSION_TITLE', 'Mission supporter mensuelle'),
        'mission_description' => env(
            'SUPPORTER_MONTHLY_MISSION_DESCRIPTION',
            'Merci de soutenir ERAH. Reviens chaque mois pour récupérer ton bonus supporter.'
        ),
        'event_type' => env('SUPPORTER_MONTHLY_EVENT_TYPE', 'supporter.monthly'),
        'target_count' => (int) env('SUPPORTER_MONTHLY_TARGET_COUNT', 1),
        'xp_bonus' => (int) env('SUPPORTER_MONTHLY_XP_BONUS', 100),
        'rank_points_bonus' => (int) env('SUPPORTER_MONTHLY_RANK_POINTS_BONUS', 15),
        'reward_points_bonus' => (int) env('SUPPORTER_MONTHLY_REWARD_POINTS_BONUS', 250),
    ],

    'xp_multiplier' => (float) env('SUPPORTER_XP_MULTIPLIER', 1.15),
];
