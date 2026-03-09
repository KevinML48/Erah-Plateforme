<?php

namespace Database\Seeders;

use App\Models\LiveCode;
use App\Models\MissionTemplate;
use App\Models\PlatformEvent;
use App\Models\Quiz;
use App\Models\User;
use App\Services\AchievementService;
use App\Services\ShopService;
use Illuminate\Database\Seeder;

class CommunityPlatformSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('role', User::ROLE_ADMIN)->first()
            ?? User::factory()->create([
                'name' => 'ERAH Community Admin',
                'email' => 'community-admin@erah.test',
                'role' => User::ROLE_ADMIN,
            ]);

        app(AchievementService::class)->seedDefaults();
        app(ShopService::class)->seedDefaults();

        $this->seedQuiz($admin);
        $this->seedLiveCode($admin);
        $this->seedEvent();
    }

    private function seedQuiz(User $admin): void
    {
        $missionTemplateId = MissionTemplate::query()
            ->where('event_type', 'quiz.pass')
            ->value('id');

        $quiz = Quiz::query()->updateOrCreate(
            ['slug' => 'quiz-erah-communaute'],
            [
                'title' => 'Quiz ERAH Communaute',
                'description' => 'Quiz de demonstration pour valider le moteur communautaire.',
                'intro' => 'Trois questions rapides sur la plateforme, les missions et les ligues.',
                'pass_score' => 2,
                'max_attempts_per_user' => 3,
                'reward_points' => 80,
                'xp_reward' => 120,
                'is_active' => true,
                'starts_at' => null,
                'ends_at' => null,
                'mission_template_id' => $missionTemplateId,
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ],
        );

        $quiz->questions()->delete();

        $questions = [
            [
                'prompt' => 'Quelle ressource sert a la progression principale ?',
                'answers' => [
                    ['label' => 'Les points boutique', 'is_correct' => false],
                    ['label' => 'L XP', 'is_correct' => true],
                    ['label' => 'Le score duel', 'is_correct' => false],
                ],
            ],
            [
                'prompt' => 'Combien de missions journalieres sont generees chaque jour ?',
                'answers' => [
                    ['label' => '5 missions', 'is_correct' => true],
                    ['label' => '2 missions', 'is_correct' => false],
                    ['label' => '10 missions', 'is_correct' => false],
                ],
            ],
            [
                'prompt' => 'Quel module permet de recuperer des bonus instantanes en direct ?',
                'answers' => [
                    ['label' => 'Les codes live', 'is_correct' => true],
                    ['label' => 'Les favoris', 'is_correct' => false],
                    ['label' => 'Les notifications', 'is_correct' => false],
                ],
            ],
        ];

        foreach ($questions as $questionIndex => $questionPayload) {
            $question = $quiz->questions()->create([
                'prompt' => $questionPayload['prompt'],
                'explanation' => null,
                'sort_order' => $questionIndex + 1,
                'points' => 1,
                'is_active' => true,
            ]);

            foreach ($questionPayload['answers'] as $answerIndex => $answerPayload) {
                $question->answers()->create([
                    'label' => $answerPayload['label'],
                    'is_correct' => $answerPayload['is_correct'],
                    'sort_order' => $answerIndex + 1,
                ]);
            }
        }
    }

    private function seedLiveCode(User $admin): void
    {
        $missionTemplateId = MissionTemplate::query()
            ->where('event_type', 'live_code.redeem')
            ->value('id');

        LiveCode::query()->updateOrCreate(
            ['code' => 'ERAHLIVE'],
            [
                'label' => 'Code live de bienvenue',
                'description' => 'Code de demonstration pour tester les redemptions live.',
                'status' => 'published',
                'reward_points' => 75,
                'bet_points' => 25,
                'xp_reward' => 50,
                'usage_limit' => 250,
                'per_user_limit' => 1,
                'expires_at' => now()->addMonths(6),
                'mission_template_id' => $missionTemplateId,
                'created_by' => $admin->id,
                'meta' => ['seed' => true],
            ],
        );
    }

    private function seedEvent(): void
    {
        PlatformEvent::query()->updateOrCreate(
            ['key' => 'bonus-clips-launch'],
            [
                'title' => 'Semaine bonus clips',
                'description' => 'Les interactions clips profitent d un bonus temporaire.',
                'type' => 'bonus_clips',
                'status' => 'published',
                'is_active' => true,
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addDays(7),
                'config' => [
                    'xp_multiplier' => 1.20,
                    'reward_points_multiplier' => 1.25,
                ],
            ],
        );
    }
}
