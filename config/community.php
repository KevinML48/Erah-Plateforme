<?php

return [
    'xp_leagues' => [
        ['key' => 'bronze', 'name' => 'Bronze', 'xp_threshold' => 0],
        ['key' => 'argent', 'name' => 'Argent', 'xp_threshold' => 1000],
        ['key' => 'gold', 'name' => 'Gold', 'xp_threshold' => 3000],
        ['key' => 'platine', 'name' => 'Platine', 'xp_threshold' => 7000],
        ['key' => 'diamant', 'name' => 'Diamant', 'xp_threshold' => 15000],
        ['key' => 'champion', 'name' => 'Champion', 'xp_threshold' => 30000],
        ['key' => 'erah-prime', 'name' => 'ERAH Prime', 'xp_threshold' => 60000],
    ],

    'progression' => [
        'level_curve' => [
            'base_xp' => 250,
            'growth_per_level' => 75,
            'max_level' => 200,
        ],
    ],

    'clips' => [
        'daily_limits' => [
            'view' => 10,
            'like' => 15,
            'comment' => 10,
        ],
        'rewards' => [
            'view' => [
                'xp' => 10,
                'points' => 15,
            ],
            'like' => [
                'xp' => 5,
                'points' => 10,
            ],
            'comment' => [
                'xp' => 15,
                'points' => 20,
            ],
        ],
    ],

    'duels' => [
        'daily_limit' => 10,
        'same_opponent_reward_limit' => 3,
        'score' => [
            'win' => 25,
            'loss' => -10,
        ],
        'rewards' => [
            'win' => [
                'xp' => 120,
                'points' => 150,
            ],
            'loss' => [
                'xp' => 30,
                'points' => -150,
            ],
        ],
    ],

    'bets' => [
        'daily_xp_limit' => 20,
        'rewards' => [
            'win' => ['xp' => 60],
            'loss' => ['xp' => 15],
        ],
    ],

    'missions' => [
        'daily_mix' => [
            'simple' => 3,
            'medium' => 1,
            'special' => 1,
        ],
        'daily_completion_bonus' => [
            'xp' => 150,
            'points' => 200,
        ],
    ],

    'streak' => [
        'rewards' => [
            1 => 20,
            7 => 120,
        ],
        'xp_multiplier' => [
            1 => 1.00,
            3 => 1.05,
            7 => 1.10,
            14 => 1.15,
        ],
    ],

    'achievements' => [
        [
            'key' => 'clips.first_view',
            'name' => 'Premier regard',
            'description' => 'Visionner un premier clip.',
            'type' => 'clips',
            'metric' => 'clip_views',
            'threshold' => 1,
            'rewards' => ['xp' => 40, 'points' => 20],
            'badge_label' => 'Clips',
            'sort_order' => 10,
        ],
        [
            'key' => 'clips.community_voice',
            'name' => 'Voix de la commu',
            'description' => 'Publier 5 commentaires sur des clips.',
            'type' => 'communaute',
            'metric' => 'clip_comments',
            'threshold' => 5,
            'rewards' => ['xp' => 60, 'points' => 40],
            'badge_label' => 'Commu',
            'sort_order' => 20,
        ],
        [
            'key' => 'bets.first_win',
            'name' => 'Premier pari valide',
            'description' => 'Remporter un premier pari.',
            'type' => 'paris',
            'metric' => 'bets_won',
            'threshold' => 1,
            'rewards' => ['xp' => 80, 'points' => 30],
            'badge_label' => 'Paris',
            'sort_order' => 30,
        ],
        [
            'key' => 'duels.rival',
            'name' => 'Rival officiel',
            'description' => 'Gagner 3 duels.',
            'type' => 'duels',
            'metric' => 'duels_won',
            'threshold' => 3,
            'rewards' => ['xp' => 120, 'points' => 50],
            'badge_label' => 'Duel',
            'sort_order' => 40,
        ],
        [
            'key' => 'progress.first_kilo',
            'name' => 'Cap des 1 000 XP',
            'description' => 'Atteindre la ligue Argent communautaire.',
            'type' => 'progression',
            'metric' => 'total_xp',
            'threshold' => 1000,
            'rewards' => ['points' => 75],
            'badge_label' => 'XP',
            'sort_order' => 50,
        ],
    ],

    'shop' => [
        'defaults' => [
            [
                'key' => 'badge.community-founder',
                'name' => 'Badge Community Founder',
                'description' => 'Badge profil premium pour les membres historiques.',
                'type' => 'badge',
                'cost_points' => 300,
                'stock' => null,
                'payload' => ['badge' => 'Community Founder'],
                'featured' => true,
                'sort_order' => 10,
            ],
            [
                'key' => 'avatar.red-frame',
                'name' => 'Contour Rouge',
                'description' => 'Contour profil premium pour la plateforme.',
                'type' => 'border',
                'cost_points' => 180,
                'stock' => null,
                'payload' => ['border' => 'red-frame'],
                'featured' => true,
                'sort_order' => 20,
            ],
            [
                'key' => 'boost.xp-week',
                'name' => 'Boost XP 7 jours',
                'description' => 'Boost d XP ajoute aux activites communautaires.',
                'type' => 'boost',
                'cost_points' => 500,
                'stock' => 100,
                'payload' => ['xp_multiplier' => 1.15, 'days' => 7],
                'featured' => false,
                'sort_order' => 30,
            ],
        ],
    ],
];
