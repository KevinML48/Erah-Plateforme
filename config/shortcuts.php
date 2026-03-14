<?php

return [
    'min' => 1,
    'max' => 5,

    'catalog' => [
        'overview' => [
            'label' => 'Vue d\'ensemble',
            'route' => 'marketing.platform',
            'requires_auth' => false,
        ],
        'classement' => [
            'label' => 'Classement',
            'route' => 'app.leaderboards.index',
            'requires_auth' => false,
        ],
        'clips' => [
            'label' => 'Clips',
            'route' => 'app.clips.index',
            'requires_auth' => false,
        ],
        'matchs' => [
            'label' => 'Matchs',
            'route' => 'app.matches.index',
            'requires_auth' => false,
        ],
        'boutique' => [
            'label' => 'Boutique',
            'route' => 'marketing.boutique',
            'requires_auth' => false,
        ],
        'missions' => [
            'label' => 'Missions',
            'route' => 'app.missions.index',
            'requires_auth' => true,
        ],
        'duels' => [
            'label' => 'Duels',
            'route' => 'app.duels.index',
            'requires_auth' => true,
        ],
        'paris' => [
            'label' => 'Paris',
            'route' => 'app.bets.index',
            'requires_auth' => true,
        ],
        'favoris' => [
            'label' => 'Favoris',
            'route' => 'app.clips.favorites',
            'requires_auth' => true,
        ],
        'notifications' => [
            'label' => 'Notifications',
            'route' => 'app.notifications.index',
            'requires_auth' => true,
        ],
        'profil' => [
            'label' => 'Profil',
            'route' => 'app.profile',
            'requires_auth' => true,
        ],
    ],

    'defaults_guest' => [
        'overview',
        'classement',
        'clips',
        'matchs',
    ],

    'defaults_auth' => [
        'missions',
        'duels',
        'paris',
        'classement',
        'profil',
    ],
];
