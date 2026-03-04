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
use Illuminate\Support\Carbon;

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
                'key' => 'daily_clip_share',
                'title' => 'Partager 1 clip',
                'description' => 'Partage un clip de ton choix aujourd hui.',
                'event_type' => 'clip_share',
                'target_count' => 1,
                'scope' => MissionTemplate::SCOPE_DAILY,
                'constraints' => null,
                'rewards' => [
                    'xp_amount' => 20,
                    'rank_points_amount' => 0,
                    'reward_points_amount' => 40,
                    'bet_points_amount' => 0,
                ],
                'is_active' => true,
            ],
            [
                'key' => 'daily_clip_favorite',
                'title' => 'Ajouter 2 favoris',
                'description' => 'Ajoute 2 clips a tes favoris.',
                'event_type' => 'clip_favorite',
                'target_count' => 2,
                'scope' => MissionTemplate::SCOPE_DAILY,
                'constraints' => null,
                'rewards' => [
                    'xp_amount' => 30,
                    'rank_points_amount' => 0,
                    'reward_points_amount' => 60,
                    'bet_points_amount' => 0,
                ],
                'is_active' => true,
            ],
            [
                'key' => 'daily_duel_answer',
                'title' => 'Repondre a 1 duel',
                'description' => 'Accepte ou refuse un duel aujourd hui.',
                'event_type' => 'duel_response',
                'target_count' => 1,
                'scope' => MissionTemplate::SCOPE_DAILY,
                'constraints' => null,
                'rewards' => [
                    'xp_amount' => 25,
                    'rank_points_amount' => 5,
                    'reward_points_amount' => 50,
                    'bet_points_amount' => 0,
                ],
                'is_active' => true,
            ],
            [
                'key' => 'daily_login',
                'title' => 'Connexion quotidienne',
                'description' => 'Connecte-toi une fois dans la journee.',
                'event_type' => 'user_login',
                'target_count' => 1,
                'scope' => MissionTemplate::SCOPE_DAILY,
                'constraints' => null,
                'rewards' => [
                    'xp_amount' => 15,
                    'rank_points_amount' => 0,
                    'reward_points_amount' => 25,
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
            [
                'key' => 'weekly_duels_played',
                'title' => 'Jouer 3 duels',
                'description' => 'Participe a 3 duels sur la semaine.',
                'event_type' => 'duel_played',
                'target_count' => 3,
                'scope' => MissionTemplate::SCOPE_WEEKLY,
                'constraints' => null,
                'rewards' => [
                    'xp_amount' => 70,
                    'rank_points_amount' => 20,
                    'reward_points_amount' => 180,
                    'bet_points_amount' => 0,
                ],
                'is_active' => true,
            ],
            [
                'key' => 'weekly_comments_10',
                'title' => 'Poster 10 commentaires',
                'description' => 'Commente 10 clips dans la semaine.',
                'event_type' => 'clip_comment',
                'target_count' => 10,
                'scope' => MissionTemplate::SCOPE_WEEKLY,
                'constraints' => null,
                'rewards' => [
                    'xp_amount' => 80,
                    'rank_points_amount' => 10,
                    'reward_points_amount' => 220,
                    'bet_points_amount' => 0,
                ],
                'is_active' => true,
            ],
            [
                'key' => 'weekly_favorites_8',
                'title' => 'Ajouter 8 favoris',
                'description' => 'Sauvegarde 8 clips en favoris cette semaine.',
                'event_type' => 'clip_favorite',
                'target_count' => 8,
                'scope' => MissionTemplate::SCOPE_WEEKLY,
                'constraints' => null,
                'rewards' => [
                    'xp_amount' => 55,
                    'rank_points_amount' => 10,
                    'reward_points_amount' => 180,
                    'bet_points_amount' => 0,
                ],
                'is_active' => true,
            ],
            [
                'key' => 'weekly_shares_5',
                'title' => 'Partager 5 clips',
                'description' => 'Partage 5 clips sur la plateforme cette semaine.',
                'event_type' => 'clip_share',
                'target_count' => 5,
                'scope' => MissionTemplate::SCOPE_WEEKLY,
                'constraints' => null,
                'rewards' => [
                    'xp_amount' => 65,
                    'rank_points_amount' => 12,
                    'reward_points_amount' => 200,
                    'bet_points_amount' => 0,
                ],
                'is_active' => true,
            ],
            [
                'key' => 'once_complete_profile',
                'title' => 'Completer ton profil',
                'description' => 'Ajoute bio + avatar + 1 reseau social.',
                'event_type' => 'profile_update',
                'target_count' => 1,
                'scope' => MissionTemplate::SCOPE_ONCE,
                'constraints' => null,
                'rewards' => [
                    'xp_amount' => 100,
                    'rank_points_amount' => 25,
                    'reward_points_amount' => 350,
                    'bet_points_amount' => 100,
                ],
                'is_active' => true,
            ],
            [
                'key' => 'once_connect_discord',
                'title' => 'Connecter Discord',
                'description' => 'Associe ton compte Discord a la plateforme.',
                'event_type' => 'social_connect_discord',
                'target_count' => 1,
                'scope' => MissionTemplate::SCOPE_ONCE,
                'constraints' => null,
                'rewards' => [
                    'xp_amount' => 40,
                    'rank_points_amount' => 10,
                    'reward_points_amount' => 120,
                    'bet_points_amount' => 0,
                ],
                'is_active' => true,
            ],
            [
                'key' => 'event_ga_special',
                'title' => 'Special Event: Gamers Assembly',
                'description' => 'Realise 8 interactions clips pendant l event.',
                'event_type' => 'clip_like',
                'target_count' => 8,
                'scope' => MissionTemplate::SCOPE_EVENT_WINDOW,
                'start_at' => now()->subDays(2),
                'end_at' => now()->addDays(10),
                'constraints' => ['event' => 'gamers_assembly'],
                'rewards' => [
                    'xp_amount' => 140,
                    'rank_points_amount' => 40,
                    'reward_points_amount' => 500,
                    'bet_points_amount' => 120,
                ],
                'is_active' => true,
            ],
            [
                'key' => 'event_weekend_duels',
                'title' => 'Special Event: Weekend Duels',
                'description' => 'Joue 3 duels pendant le weekend event.',
                'event_type' => 'duel_played',
                'target_count' => 3,
                'scope' => MissionTemplate::SCOPE_EVENT_WINDOW,
                'start_at' => now()->subDay(),
                'end_at' => now()->addDays(3),
                'constraints' => ['event' => 'weekend_duels'],
                'rewards' => [
                    'xp_amount' => 120,
                    'rank_points_amount' => 35,
                    'reward_points_amount' => 420,
                    'bet_points_amount' => 100,
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
            [
                'title' => 'Hoodie ERAH',
                'description' => 'Hoodie noir ERAH pour les sessions LAN.',
                'image_url' => null,
                'cost_points' => 1400,
                'stock' => 8,
                'is_active' => true,
            ],
            [
                'title' => 'Casquette ERAH',
                'description' => 'Casquette officielle ERAH edition club.',
                'image_url' => null,
                'cost_points' => 500,
                'stock' => 18,
                'is_active' => true,
            ],
            [
                'title' => 'Mousepad Pro ERAH',
                'description' => 'Grand mousepad competition, surface rapide.',
                'image_url' => null,
                'cost_points' => 750,
                'stock' => 15,
                'is_active' => true,
            ],
            [
                'title' => 'Maillot ERAH 2026',
                'description' => 'Maillot officiel saison 2026.',
                'image_url' => null,
                'cost_points' => 1600,
                'stock' => 10,
                'is_active' => true,
            ],
            [
                'title' => 'Poster Team ERAH',
                'description' => 'Poster A2 de la line-up ERAH.',
                'image_url' => null,
                'cost_points' => 250,
                'stock' => 30,
                'is_active' => true,
            ],
            [
                'title' => 'Sticker Pack ERAH',
                'description' => 'Pack de stickers officiels ERAH.',
                'image_url' => null,
                'cost_points' => 180,
                'stock' => 40,
                'is_active' => true,
            ],
            [
                'title' => 'VIP Viewing Pass',
                'description' => 'Acces VIP pour suivre un match avec le staff.',
                'image_url' => null,
                'cost_points' => 2200,
                'stock' => 4,
                'is_active' => true,
            ],
            [
                'title' => 'Coaching Session 30m',
                'description' => 'Session coaching strategique de 30 minutes.',
                'image_url' => null,
                'cost_points' => 1750,
                'stock' => 6,
                'is_active' => true,
            ],
            [
                'title' => 'Discord Role Elite',
                'description' => 'Role premium sur le Discord ERAH.',
                'image_url' => null,
                'cost_points' => 450,
                'stock' => 999,
                'is_active' => true,
            ],
            [
                'title' => 'Nameplate Profile Gold',
                'description' => 'Badge profile gold pour la plateforme.',
                'image_url' => null,
                'cost_points' => 700,
                'stock' => 999,
                'is_active' => true,
            ],
            [
                'title' => 'Bootcamp Access 1 Day',
                'description' => 'Invite a un bootcamp ERAH sur 1 jour.',
                'image_url' => null,
                'cost_points' => 2600,
                'stock' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Collector Pin ERAH',
                'description' => 'Pin metal collector edition ERAH.',
                'image_url' => null,
                'cost_points' => 320,
                'stock' => 22,
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
        $this->seedMissionProgressForUser($firstUser, 'daily_clip_share', 1, true);
        $this->seedMissionProgressForUser($firstUser, 'daily_clip_favorite', 1, false);
        $this->seedMissionProgressForUser($firstUser, 'daily_duel_answer', 1, true);
        $this->seedMissionProgressForUser($firstUser, 'daily_login', 1, true);
        $this->seedMissionProgressForUser($firstUser, 'weekly_bets_placed', 1, false);
        $this->seedMissionProgressForUser($firstUser, 'weekly_bet_win', 1, true);
        $this->seedMissionProgressForUser($firstUser, 'weekly_duels_played', 2, false);
        $this->seedMissionProgressForUser($firstUser, 'weekly_comments_10', 6, false);
        $this->seedMissionProgressForUser($firstUser, 'weekly_favorites_8', 8, true);
        $this->seedMissionProgressForUser($firstUser, 'weekly_shares_5', 3, false);
        $this->seedMissionProgressForUser($firstUser, 'once_complete_profile', 1, true);
        $this->seedMissionProgressForUser($firstUser, 'once_connect_discord', 0, false);
        $this->seedMissionProgressForUser($firstUser, 'event_ga_special', 4, false);
        $this->seedMissionProgressForUser($firstUser, 'event_weekend_duels', 3, true);
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
            MissionTemplate::SCOPE_ONCE => $template->start_at?->copy()->startOfDay() ?? Carbon::create(2020, 1, 1, 0, 0, 0),
            MissionTemplate::SCOPE_EVENT_WINDOW => $template->start_at?->copy() ?? now()->startOfDay(),
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
