<?php

namespace Database\Seeders;

use App\Models\MissionTemplate;
use App\Services\SupporterAccessResolver;
use Illuminate\Database\Seeder;

class SupporterProgramSeeder extends Seeder
{
    public function run(): void
    {
        $resolver = app(SupporterAccessResolver::class);
        $resolver->ensureConfiguredPlans();
        $resolver->ensureCommunityGoals();

        MissionTemplate::query()->updateOrCreate(
            ['key' => 'supporter-weekly-clips'],
            [
                'title' => 'Reaction supporter de la semaine',
                'description' => 'Ajoutez une reaction supporter a un clip ERAH et votez pour la campagne active.',
                'event_type' => 'supporter_weekly_clip',
                'target_count' => 2,
                'scope' => MissionTemplate::SCOPE_WEEKLY,
                'constraints' => ['supporter_only' => true],
                'rewards' => [
                    'xp' => 60,
                    'rank_points' => 10,
                    'reward_points' => 100,
                    'bet_points' => 0,
                ],
                'is_active' => true,
            ]
        );

        MissionTemplate::query()->updateOrCreate(
            ['key' => 'supporter-exclusive-community'],
            [
                'title' => 'Supporter exclusif communaute',
                'description' => 'Revenez sur la plateforme et completez votre passage mensuel supporter.',
                'event_type' => 'supporter_exclusive_community',
                'target_count' => 1,
                'scope' => MissionTemplate::SCOPE_ONCE,
                'constraints' => ['supporter_only' => true],
                'rewards' => [
                    'xp' => 80,
                    'rank_points' => 12,
                    'reward_points' => 150,
                    'bet_points' => 0,
                ],
                'is_active' => true,
            ]
        );
    }
}
