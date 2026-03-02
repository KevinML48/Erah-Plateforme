<?php

namespace Database\Seeders;

use App\Application\Actions\Rewards\ApplyRewardWalletTransactionAction;
use App\Application\Actions\Rewards\EnsureCurrentMissionInstancesAction;
use App\Models\MissionCompletion;
use App\Models\MissionTemplate;
use App\Models\User;
use App\Models\UserMission;
use App\Models\UserRewardWallet;
use App\Models\Gift;
use App\Models\RewardWalletTransaction;
use Illuminate\Database\Seeder;

class MissionsAndGiftsSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedTemplates();
        $this->seedGifts();
        $this->seedWalletsAndProgress();
    }

    private function seedTemplates(): void
    {
        $templates = [
            [
                'key' => 'daily_clip_comments',
                'title' => 'Commenter 3 clips',
                'description' => 'Ajoute 3 commentaires sur des clips de la communaute.',
                'event_type' => 'clip_comment',
                'target_count' => 3,
                'scope' => MissionTemplate::SCOPE_DAILY,
                'constraints' => null,
                'rewards' => [
                    'xp_amount' => 50,
                    'rank_points_amount' => 0,
                    'reward_points_amount' => 100,
                    'bet_points_amount' => 0,
                ],
                'is_active' => true,
            ],
            [
                'key' => 'daily_clip_likes',
                'title' => 'Liker 5 clips',
                'description' => 'Like 5 clips aujourd hui.',
                'event_type' => 'clip_like',
                'target_count' => 5,
                'scope' => MissionTemplate::SCOPE_DAILY,
                'constraints' => null,
                'rewards' => [
                    'xp_amount' => 30,
                    'rank_points_amount' => 0,
                    'reward_points_amount' => 50,
                    'bet_points_amount' => 0,
                ],
                'is_active' => true,
            ],
            [
                'key' => 'weekly_bets_placed',
                'title' => 'Parier 3 fois',
                'description' => 'Place 3 paris dans la semaine.',
                'event_type' => 'bet_placed',
                'target_count' => 3,
                'scope' => MissionTemplate::SCOPE_WEEKLY,
                'constraints' => ['min_stake' => 1],
                'rewards' => [
                    'xp_amount' => 0,
                    'rank_points_amount' => 20,
                    'reward_points_amount' => 200,
                    'bet_points_amount' => 0,
                ],
                'is_active' => true,
            ],
            [
                'key' => 'weekly_bet_win',
                'title' => 'Gagner 1 pari',
                'description' => 'Remporte au moins un pari cette semaine.',
                'event_type' => 'bet_won',
                'target_count' => 1,
                'scope' => MissionTemplate::SCOPE_WEEKLY,
                'constraints' => ['only_wins' => true],
                'rewards' => [
                    'xp_amount' => 0,
                    'rank_points_amount' => 50,
                    'reward_points_amount' => 300,
                    'bet_points_amount' => 0,
                ],
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            MissionTemplate::query()->updateOrCreate(
                ['key' => $template['key']],
                $template
            );
        }
    }

    private function seedGifts(): void
    {
        $gifts = [
            [
                'title' => 'T-shirt ERAH',
                'description' => 'T-shirt officiel ERAH Edition Community.',
                'image_url' => null,
                'cost_points' => 1000,
                'stock' => 10,
                'is_active' => true,
            ],
            [
                'title' => 'Mug ERAH',
                'description' => 'Mug premium pour les sessions VOD.',
                'image_url' => null,
                'cost_points' => 600,
                'stock' => 20,
                'is_active' => true,
            ],
            [
                'title' => 'Ticket event',
                'description' => 'Ticket pour un event communautaire ERAH.',
                'image_url' => null,
                'cost_points' => 2000,
                'stock' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($gifts as $gift) {
            Gift::query()->updateOrCreate(
                ['title' => $gift['title']],
                $gift
            );
        }
    }

    private function seedWalletsAndProgress(): void
    {
        $applyRewardWalletTransactionAction = app(ApplyRewardWalletTransactionAction::class);
        $ensureCurrentMissionInstancesAction = app(EnsureCurrentMissionInstancesAction::class);

        $users = User::query()
            ->where('role', User::ROLE_USER)
            ->orderBy('id')
            ->get();

        foreach ($users as $index => $user) {
            UserRewardWallet::query()->firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => 0]
            );

            $grantAmount = match ($index) {
                0 => 1800,
                1 => 1200,
                2 => 850,
                default => 300,
            };

            $applyRewardWalletTransactionAction->execute(
                user: $user,
                type: RewardWalletTransaction::TYPE_GRANT,
                amount: $grantAmount,
                uniqueKey: 'seed.reward_wallet.grant.user.'.$user->id.'.v1',
                refType: RewardWalletTransaction::REF_TYPE_SYSTEM,
                refId: 'missions-gifts-seeder',
                metadata: ['seed' => self::class],
                initialBalanceIfMissing: 0
            );

            $ensureCurrentMissionInstancesAction->execute($user);
        }

        $firstUser = $users->first();
        if (! $firstUser) {
            return;
        }

        $this->seedMissionProgressForUser($firstUser, 'daily_clip_comments', 2, false);
        $this->seedMissionProgressForUser($firstUser, 'daily_clip_likes', 5, true);
        $this->seedMissionProgressForUser($firstUser, 'weekly_bets_placed', 1, false);
        $this->seedMissionProgressForUser($firstUser, 'weekly_bet_win', 1, true);
    }

    private function seedMissionProgressForUser(User $user, string $templateKey, int $progressCount, bool $completed): void
    {
        $template = MissionTemplate::query()->where('key', $templateKey)->first();
        if (! $template) {
            return;
        }

        $periodStart = match ($template->scope) {
            MissionTemplate::SCOPE_DAILY => now()->startOfDay(),
            MissionTemplate::SCOPE_WEEKLY => now()->startOfWeek(),
            default => now()->startOfDay(),
        };

        $userMission = UserMission::query()
            ->where('user_id', $user->id)
            ->whereHas('instance', function ($query) use ($template, $periodStart): void {
                $query->where('mission_template_id', $template->id)
                    ->whereDate('period_start', $periodStart->toDateString());
            })
            ->with('instance')
            ->first();

        if (! $userMission) {
            return;
        }

        $userMission->progress_count = min($progressCount, (int) $template->target_count);
        $userMission->completed_at = $completed ? now() : null;
        $userMission->save();

        if ($completed) {
            MissionCompletion::query()->firstOrCreate([
                'user_id' => $user->id,
                'user_mission_id' => $userMission->id,
            ], [
                'completed_at' => now(),
                'created_at' => now(),
            ]);
        }
    }
}

