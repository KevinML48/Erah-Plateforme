<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\MissionClaimType;
use App\Enums\MissionCompletionRule;
use App\Enums\MissionRecurrence;
use App\Models\Mission;
use Illuminate\Database\Seeder;

class MissionSeeder extends Seeder
{
    public function run(): void
    {
        $missions = [
            [
                'title' => "Onboarding - Decouvrir l'app",
                'slug' => 'ONBOARDING_APP_DISCOVERY',
                'description' => 'Visite les pages cles de la plateforme ERAH.',
                'points_reward' => 250,
                'recurrence' => MissionRecurrence::OneTime,
                'completion_rule' => MissionCompletionRule::All,
                'claim_type' => MissionClaimType::Auto,
                'is_active' => true,
                'steps' => [
                    ['step_key' => 'page_viewed', 'step_value' => 'dashboard', 'label' => 'Ouvrir le dashboard', 'order' => 1],
                    ['step_key' => 'page_viewed', 'step_value' => 'matches', 'label' => 'Consulter les matchs', 'order' => 2],
                    ['step_key' => 'page_viewed', 'step_value' => 'rewards', 'label' => 'Explorer les rewards', 'order' => 3],
                    ['step_key' => 'page_viewed', 'step_value' => 'leaderboard', 'label' => 'Voir le leaderboard', 'order' => 4],
                ],
            ],
            [
                'title' => 'Premier pronostic',
                'slug' => 'FIRST_PREDICTION',
                'description' => 'Place ton premier pronostic.',
                'points_reward' => 120,
                'recurrence' => MissionRecurrence::OneTime,
                'completion_rule' => MissionCompletionRule::All,
                'claim_type' => MissionClaimType::Auto,
                'is_active' => true,
                'steps' => [
                    ['step_key' => 'prediction_created', 'step_value' => null, 'label' => 'Creer un pronostic', 'order' => 1],
                ],
            ],
            [
                'title' => 'Premiere demande reward',
                'slug' => 'FIRST_REWARD_REQUEST',
                'description' => 'Demande une recompense depuis la boutique.',
                'points_reward' => 150,
                'recurrence' => MissionRecurrence::OneTime,
                'completion_rule' => MissionCompletionRule::All,
                'claim_type' => MissionClaimType::Auto,
                'is_active' => true,
                'steps' => [
                    ['step_key' => 'reward_redeemed', 'step_value' => null, 'label' => 'Demander un reward', 'order' => 1],
                ],
            ],
            [
                'title' => 'Login 3 jours',
                'slug' => 'LOGIN_3_DAYS',
                'description' => 'Connecte-toi 3 jours dans la semaine.',
                'points_reward' => 180,
                'recurrence' => MissionRecurrence::Weekly,
                'completion_rule' => MissionCompletionRule::AnyN,
                'any_n' => 3,
                'claim_type' => MissionClaimType::Auto,
                'is_active' => true,
                'steps' => [
                    ['step_key' => 'user_logged_in', 'step_value' => null, 'label' => 'Connexion quotidienne', 'order' => 1],
                ],
            ],
            [
                'title' => 'Login quotidien',
                'slug' => 'DAILY_LOGIN',
                'description' => 'Connecte-toi chaque jour.',
                'points_reward' => 60,
                'recurrence' => MissionRecurrence::Daily,
                'completion_rule' => MissionCompletionRule::All,
                'claim_type' => MissionClaimType::Auto,
                'is_active' => true,
                'steps' => [
                    ['step_key' => 'user_logged_in', 'step_value' => null, 'label' => 'Connexion du jour', 'order' => 1],
                ],
            ],
            [
                'title' => 'Voir matchs x5',
                'slug' => 'VIEW_MATCHES_5',
                'description' => 'Ouvre la page matchs 5 fois dans la semaine.',
                'points_reward' => 120,
                'recurrence' => MissionRecurrence::Weekly,
                'completion_rule' => MissionCompletionRule::AnyN,
                'any_n' => 5,
                'claim_type' => MissionClaimType::Auto,
                'is_active' => true,
                'steps' => [
                    ['step_key' => 'page_viewed', 'step_value' => 'matches', 'label' => 'Vue page matchs', 'order' => 1],
                ],
            ],
            [
                'title' => 'Voir leaderboard x3',
                'slug' => 'VIEW_LEADERBOARD_3',
                'description' => 'Consulte le leaderboard 3 fois dans la semaine.',
                'points_reward' => 100,
                'recurrence' => MissionRecurrence::Weekly,
                'completion_rule' => MissionCompletionRule::AnyN,
                'any_n' => 3,
                'claim_type' => MissionClaimType::Auto,
                'is_active' => true,
                'steps' => [
                    ['step_key' => 'leaderboard_viewed', 'step_value' => null, 'label' => 'Vue leaderboard', 'order' => 1],
                ],
            ],
            [
                'title' => 'Lier Discord',
                'slug' => 'DISCORD_LINK',
                'description' => 'Connecte ton compte Discord.',
                'points_reward' => 140,
                'recurrence' => MissionRecurrence::OneTime,
                'completion_rule' => MissionCompletionRule::All,
                'claim_type' => MissionClaimType::Auto,
                'is_active' => true,
                'steps' => [
                    ['step_key' => 'discord_linked', 'step_value' => null, 'label' => 'Discord lie', 'order' => 1],
                ],
            ],
            [
                'title' => 'Completer 3 missions',
                'slug' => 'COMPLETE_3_MISSIONS',
                'description' => 'Complete 3 missions cette semaine.',
                'points_reward' => 220,
                'recurrence' => MissionRecurrence::Weekly,
                'completion_rule' => MissionCompletionRule::AnyN,
                'any_n' => 3,
                'claim_type' => MissionClaimType::Auto,
                'is_active' => true,
                'steps' => [
                    ['step_key' => 'mission_completed', 'step_value' => null, 'label' => 'Mission completee', 'order' => 1],
                ],
            ],
            [
                'title' => 'Active Week',
                'slug' => 'ACTIVE_WEEK',
                'description' => 'Connecte-toi 7 jours sur la semaine.',
                'points_reward' => 260,
                'recurrence' => MissionRecurrence::Weekly,
                'completion_rule' => MissionCompletionRule::AnyN,
                'any_n' => 7,
                'claim_type' => MissionClaimType::Auto,
                'is_active' => true,
                'steps' => [
                    ['step_key' => 'user_logged_in', 'step_value' => null, 'label' => 'Connexion active', 'order' => 1],
                ],
            ],
            [
                'title' => 'Streak 7 jours',
                'slug' => 'DAILY_STREAK_7',
                'description' => 'Maintiens un streak de 7 connexions consecutives.',
                'points_reward' => 300,
                'recurrence' => MissionRecurrence::Weekly,
                'completion_rule' => MissionCompletionRule::AnyN,
                'any_n' => 7,
                'claim_type' => MissionClaimType::Auto,
                'is_active' => true,
                'steps' => [
                    ['step_key' => 'user_logged_in', 'step_value' => null, 'label' => 'Connexion streak', 'order' => 1],
                ],
            ],
        ];

        foreach ($missions as $payload) {
            $steps = $payload['steps'];
            unset($payload['steps']);

            $mission = Mission::query()->updateOrCreate(
                ['slug' => $payload['slug']],
                $payload
            );

            $mission->steps()->delete();
            $mission->steps()->createMany($steps);
        }
    }
}
