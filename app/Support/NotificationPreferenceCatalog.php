<?php

namespace App\Support;

class NotificationPreferenceCatalog
{
    /**
     * @return array<string, array{label: string, description: string, icon: string, tone: string}>
     */
    public static function categories(): array
    {
        return [
            'duel' => [
                'label' => 'Duels',
                'description' => 'Invitations, reponses et rappels de duel.',
                'icon' => 'fa-solid fa-crosshairs',
                'tone' => 'tone-duel',
            ],
            'clips' => [
                'label' => 'Clips',
                'description' => 'Likes, commentaires, favoris et tendances.',
                'icon' => 'fa-solid fa-clapperboard',
                'tone' => 'tone-clips',
            ],
            'comment' => [
                'label' => 'Commentaires',
                'description' => 'Reponses, nouvelles discussions et suivi des echanges.',
                'icon' => 'fa-solid fa-comments',
                'tone' => 'tone-clips',
            ],
            'mission' => [
                'label' => 'Missions',
                'description' => 'Validation, progression et bonus journaliers.',
                'icon' => 'fa-solid fa-list-check',
                'tone' => 'tone-system',
            ],
            'quiz' => [
                'label' => 'Quiz',
                'description' => 'Ouverture des quiz, tentatives et validations.',
                'icon' => 'fa-solid fa-circle-question',
                'tone' => 'tone-system',
            ],
            'live_code' => [
                'label' => 'Codes live',
                'description' => 'Codes temporaires, redemptions et campagnes live.',
                'icon' => 'fa-solid fa-bolt',
                'tone' => 'tone-system',
            ],
            'achievement' => [
                'label' => 'Succes',
                'description' => 'Deblocages permanents et badges communautaires.',
                'icon' => 'fa-solid fa-medal',
                'tone' => 'tone-system',
            ],
            'event' => [
                'label' => 'Evenements',
                'description' => 'Fenetres bonus, double XP et operations speciales.',
                'icon' => 'fa-solid fa-calendar-days',
                'tone' => 'tone-match',
            ],
            'system' => [
                'label' => 'Systeme',
                'description' => 'Infos compte, securite et annonces plateforme.',
                'icon' => 'fa-solid fa-shield-halved',
                'tone' => 'tone-system',
            ],
            'match' => [
                'label' => 'Matchs',
                'description' => 'Etat des matchs, timing et resultats.',
                'icon' => 'fa-solid fa-trophy',
                'tone' => 'tone-match',
            ],
            'bet' => [
                'label' => 'Paris',
                'description' => 'Placements, annulations et reglements de paris.',
                'icon' => 'fa-solid fa-coins',
                'tone' => 'tone-bet',
            ],
        ];
    }

    /**
     * @return array<string, array{label: string, hint: string, email: array<int, string>, push: array<int, string>, recommended?: bool}>
     */
    public static function presets(): array
    {
        return [
            'recommended' => [
                'label' => 'Reglages recommandes',
                'hint' => 'Equilibre entre suivi utile et reduction du bruit pour la plupart des membres.',
                'email' => ['system', 'mission', 'quiz', 'achievement', 'event', 'match', 'bet'],
                'push' => ['system', 'duel', 'quiz', 'live_code', 'match', 'bet'],
                'recommended' => true,
            ],
            'essential' => [
                'label' => 'Activer seulement l essentiel',
                'hint' => 'Garde les alertes critiques et limite fortement les notifications secondaires.',
                'email' => ['system', 'mission', 'match'],
                'push' => ['system', 'live_code', 'match'],
            ],
            'all_on' => [
                'label' => 'Tout activer',
                'hint' => 'Active Email et Push sur toutes les categories compatibles.',
                'email' => array_keys(self::categories()),
                'push' => array_keys(self::categories()),
            ],
            'all_off' => [
                'label' => 'Tout desactiver',
                'hint' => 'Coupe Email et Push sur toutes les categories modifiables.',
                'email' => [],
                'push' => [],
            ],
        ];
    }
}