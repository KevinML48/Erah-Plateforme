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

    'events' => [
        'types' => [
            'head_to_head' => 'Match direct',
            'tournament_run' => 'Parcours en tournoi',
        ],
        'games' => [
            'valorant' => 'Valorant',
            'rocket_league' => 'Rocket League',
            'league_of_legends' => 'League of Legends',
            'cs2' => 'Counter-Strike 2',
        ],
        'statuses' => [
            'scheduled' => 'Ouvert aux predictions',
            'locked' => 'Predictions fermees',
            'live' => 'Match en cours',
            'finished' => 'Termine, en attente du reglement',
            'settled' => 'Pronostics regles',
            'cancelled' => 'Annule',
        ],
        'status_short_labels' => [
            'scheduled' => 'Ouvert',
            'locked' => 'Ferme',
            'live' => 'En cours',
            'finished' => 'Termine',
            'settled' => 'Regle',
            'cancelled' => 'Annule',
        ],
        'status_descriptions' => [
            'scheduled' => 'Le match ou tournoi est visible et les predictions sont encore ouvertes.',
            'locked' => 'L evenement approche. Les predictions ne peuvent plus etre posees.',
            'live' => 'La rencontre ou le tournoi est en train de se jouer.',
            'finished' => 'Le resultat sportif est connu, mais les predictions ne sont pas encore reglees.',
            'settled' => 'Le resultat a ete applique et les gains ont ete calcules.',
            'cancelled' => 'L evenement est annule ou neutralise.',
        ],
        'best_of' => [
            1 => 'BO1',
            3 => 'BO3',
            5 => 'BO5',
            7 => 'BO7',
        ],
    ],

    'markets' => [
        'winner_key' => 'WINNER',
        'exact_score_key' => 'EXACT_SCORE',
        'tournament_finish_key' => 'TOURNAMENT_FINISH',
        'labels' => [
            'WINNER' => 'Vainqueur du match',
            'EXACT_SCORE' => 'Score exact',
            'TOURNAMENT_FINISH' => 'Parcours final',
        ],
    ],

    'odds' => [
        'winner_fixed' => (float) env('BETTING_WINNER_FIXED_ODDS', 2.0),
        'draw_fixed' => (float) env('BETTING_DRAW_FIXED_ODDS', 3.0),
        'rocket_league_finish' => [
            'champion' => 2.200,
            'finale' => 2.800,
            'top_4' => 3.300,
            'top_8' => 3.900,
            'top_16' => 4.500,
            'outside_top_16' => 2.400,
        ],
        'rocket_league_exact_score' => [
            5 => [
                '3_0' => 4.500,
                '3_1' => 5.000,
                '3_2' => 5.600,
                '0_3' => 4.500,
                '1_3' => 5.000,
                '2_3' => 5.600,
            ],
            7 => [
                '4_0' => 5.200,
                '4_1' => 5.700,
                '4_2' => 6.300,
                '4_3' => 6.900,
                '0_4' => 5.200,
                '1_4' => 5.700,
                '2_4' => 6.300,
                '3_4' => 6.900,
            ],
        ],
    ],

    'market_presets' => [
        'labels' => [
            'classic_winner' => 'Vainqueur du match',
            'rocket_league_tournament' => 'Parcours Rocket League',
            'rocket_league_bo5' => 'Rocket League BO5 complet',
            'rocket_league_bo7' => 'Rocket League BO7 complet',
        ],
        'tournament_finish_labels' => [
            'champion' => 'Champion',
            'finale' => 'Finale',
            'top_4' => 'Top 4',
            'top_8' => 'Top 8',
            'top_16' => 'Top 16',
            'outside_top_16' => 'Hors Top 16',
        ],
    ],

    'ranking_bonus' => [
        'rank_points_on_win' => (int) env('BETTING_WIN_RANK_BONUS', 25),
        'xp_on_win' => (int) env('BETTING_WIN_XP_BONUS', 0),
    ],

    'bets' => [
        'statuses' => [
            'placed' => 'Enregistre',
            'cancelled' => 'Annule et rembourse',
            'pending' => 'En attente',
            'won' => 'Gagne',
            'lost' => 'Perdu',
            'void' => 'Rembourse',
        ],
    ],
];
