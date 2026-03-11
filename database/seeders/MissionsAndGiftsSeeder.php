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
                'key' => 'daily_login',
                'title' => 'Connexion quotidienne',
                'short_description' => 'Connecte-toi une fois dans la journee.',
                'description' => 'Connecte-toi une fois dans la journee.',
                'long_description' => 'Mission de base pour lancer la progression quotidienne et verifier ton passage sur la plateforme.',
                'category' => 'progression',
                'type' => 'core',
                'difficulty' => 'simple',
                'estimated_minutes' => 2,
                'is_discovery' => true,
                'sort_order' => 5,
                'event_type' => 'login.daily',
                'target_count' => 1,
                'scope' => MissionTemplate::SCOPE_DAILY,
                'constraints' => null,
                'rewards' => ['xp' => 15, 'points' => 25],
                'is_repeatable' => true,
                'is_active' => true,
            ],
            [
                'key' => 'once_complete_profile',
                'title' => 'Completer ton profil',
                'short_description' => 'Ajoute bio + avatar + 1 reseau social.',
                'description' => 'Ajoute bio + avatar + 1 reseau social.',
                'long_description' => 'Complete les elements essentiels de ton profil pour debloquer une meilleure presentation sur la plateforme.',
                'category' => 'onboarding',
                'type' => 'core',
                'difficulty' => 'simple',
                'estimated_minutes' => 12,
                'is_discovery' => true,
                'sort_order' => 10,
                'event_type' => 'profile.completed',
                'target_count' => 1,
                'scope' => MissionTemplate::SCOPE_ONCE,
                'constraints' => ['min_profile_completion' => 75],
                'rewards' => ['xp' => 100, 'points' => 450],
                'is_active' => true,
            ],
            [
                'key' => 'once_first_duel',
                'title' => 'Lancer ton premier duel',
                'short_description' => 'Envoie un premier duel depuis la plateforme.',
                'description' => 'Envoie un premier duel depuis la plateforme.',
                'category' => 'onboarding',
                'type' => 'core',
                'difficulty' => 'medium',
                'estimated_minutes' => 8,
                'sort_order' => 20,
                'event_type' => 'duel.sent',
                'target_count' => 1,
                'scope' => MissionTemplate::SCOPE_ONCE,
                'constraints' => null,
                'rewards' => ['xp' => 40, 'points' => 120],
                'is_active' => true,
            ],
            [
                'key' => 'daily_clip_comments',
                'title' => 'Commenter 3 clips',
                'short_description' => 'Ajoute 3 commentaires sur des clips de la communaute.',
                'description' => 'Ajoute 3 commentaires sur des clips de la communaute.',
                'long_description' => 'Ajoute 3 commentaires utiles sur des clips pour activer ta progression communautaire du jour.',
                'category' => 'clips',
                'type' => 'repeatable',
                'difficulty' => 'simple',
                'estimated_minutes' => 10,
                'is_discovery' => true,
                'sort_order' => 30,
                'event_type' => 'clip.comment',
                'target_count' => 3,
                'scope' => MissionTemplate::SCOPE_DAILY,
                'constraints' => null,
                'rewards' => ['xp' => 50, 'points' => 100],
                'is_repeatable' => true,
                'is_active' => true,
            ],
            [
                'key' => 'daily_clip_likes',
                'title' => 'Liker 5 clips',
                'short_description' => 'Like 5 clips aujourd hui.',
                'description' => 'Like 5 clips aujourd hui.',
                'category' => 'clips',
                'type' => 'repeatable',
                'difficulty' => 'simple',
                'estimated_minutes' => 8,
                'sort_order' => 40,
                'event_type' => 'clip.like',
                'target_count' => 5,
                'scope' => MissionTemplate::SCOPE_DAILY,
                'constraints' => null,
                'rewards' => ['xp' => 30, 'points' => 50],
                'is_repeatable' => true,
                'is_active' => true,
            ],
            [
                'key' => 'daily_clip_share',
                'title' => 'Partager 1 clip',
                'short_description' => 'Partage un clip de ton choix aujourd hui.',
                'description' => 'Partage un clip de ton choix aujourd hui.',
                'category' => 'clips',
                'type' => 'repeatable',
                'difficulty' => 'medium',
                'estimated_minutes' => 6,
                'sort_order' => 50,
                'event_type' => 'clip.share',
                'target_count' => 1,
                'scope' => MissionTemplate::SCOPE_DAILY,
                'constraints' => null,
                'rewards' => ['xp' => 20, 'points' => 40],
                'is_repeatable' => true,
                'is_active' => true,
            ],
            [
                'key' => 'daily_clip_favorite',
                'title' => 'Ajouter 2 favoris',
                'short_description' => 'Ajoute 2 clips a tes favoris.',
                'description' => 'Ajoute 2 clips a tes favoris.',
                'category' => 'clips',
                'type' => 'repeatable',
                'difficulty' => 'simple',
                'estimated_minutes' => 6,
                'sort_order' => 60,
                'event_type' => 'clip.favorite',
                'target_count' => 2,
                'scope' => MissionTemplate::SCOPE_DAILY,
                'constraints' => null,
                'rewards' => ['xp' => 30, 'points' => 60],
                'is_repeatable' => true,
                'is_active' => true,
            ],
            [
                'key' => 'daily_duel_accept',
                'title' => 'Accepter un duel',
                'short_description' => 'Accepte un duel aujourd hui.',
                'description' => 'Accepte un duel aujourd hui.',
                'category' => 'duels',
                'type' => 'repeatable',
                'difficulty' => 'medium',
                'estimated_minutes' => 5,
                'sort_order' => 70,
                'event_type' => 'duel.accepted',
                'target_count' => 1,
                'scope' => MissionTemplate::SCOPE_DAILY,
                'constraints' => null,
                'rewards' => ['xp' => 25, 'points' => 50],
                'is_repeatable' => true,
                'is_active' => true,
            ],
            [
                'key' => 'weekly_bets_placed',
                'title' => 'Parier 3 fois',
                'short_description' => 'Place 3 paris dans la semaine.',
                'description' => 'Place 3 paris dans la semaine.',
                'category' => 'bets',
                'type' => 'repeatable',
                'difficulty' => 'medium',
                'estimated_minutes' => 25,
                'sort_order' => 100,
                'event_type' => 'bet.placed',
                'target_count' => 3,
                'scope' => MissionTemplate::SCOPE_WEEKLY,
                'constraints' => ['min_stake' => 1],
                'rewards' => ['xp' => 60, 'points' => 200],
                'is_repeatable' => true,
                'is_active' => true,
            ],
            [
                'key' => 'weekly_bet_win',
                'title' => 'Gagner 1 pari',
                'short_description' => 'Remporte au moins un pari cette semaine.',
                'description' => 'Remporte au moins un pari cette semaine.',
                'category' => 'bets',
                'type' => 'repeatable',
                'difficulty' => 'special',
                'estimated_minutes' => 35,
                'is_featured' => true,
                'sort_order' => 110,
                'event_type' => 'bet.won',
                'target_count' => 1,
                'scope' => MissionTemplate::SCOPE_WEEKLY,
                'constraints' => ['only_wins' => true],
                'rewards' => ['xp' => 90, 'points' => 300],
                'is_repeatable' => true,
                'is_active' => true,
            ],
            [
                'key' => 'weekly_duels_played',
                'title' => 'Jouer 3 duels',
                'short_description' => 'Participe a 3 duels sur la semaine.',
                'description' => 'Participe a 3 duels sur la semaine.',
                'category' => 'duels',
                'type' => 'repeatable',
                'difficulty' => 'medium',
                'estimated_minutes' => 30,
                'sort_order' => 120,
                'event_type' => 'duel.play',
                'target_count' => 3,
                'scope' => MissionTemplate::SCOPE_WEEKLY,
                'constraints' => null,
                'rewards' => ['xp' => 70, 'points' => 180],
                'is_repeatable' => true,
                'is_active' => true,
            ],
            [
                'key' => 'event_ga_special',
                'title' => 'Special Event: Gamers Assembly',
                'short_description' => 'Realise 8 interactions clips pendant l event.',
                'description' => 'Realise 8 interactions clips pendant l event.',
                'category' => 'events',
                'type' => 'event',
                'difficulty' => 'special',
                'estimated_minutes' => 45,
                'is_featured' => true,
                'sort_order' => 200,
                'event_type' => 'clip.like',
                'target_count' => 8,
                'scope' => MissionTemplate::SCOPE_EVENT_WINDOW,
                'start_at' => now()->subDays(2),
                'end_at' => now()->addDays(10),
                'constraints' => ['event' => 'gamers_assembly'],
                'rewards' => ['xp' => 140, 'points' => 620],
                'requires_claim' => true,
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

        $this->seedMissionProgressForUser($firstUser, 'daily_login', 1, true);
        $this->seedMissionProgressForUser($firstUser, 'once_complete_profile', 1, true);
        $this->seedMissionProgressForUser($firstUser, 'once_first_duel', 0, false);
        $this->seedMissionProgressForUser($firstUser, 'daily_clip_comments', 2, false);
        $this->seedMissionProgressForUser($firstUser, 'daily_clip_likes', 5, true);
        $this->seedMissionProgressForUser($firstUser, 'daily_clip_share', 1, true);
        $this->seedMissionProgressForUser($firstUser, 'daily_clip_favorite', 1, false);
        $this->seedMissionProgressForUser($firstUser, 'daily_duel_accept', 1, true);
        $this->seedMissionProgressForUser($firstUser, 'weekly_bets_placed', 1, false);
        $this->seedMissionProgressForUser($firstUser, 'weekly_bet_win', 1, true);
        $this->seedMissionProgressForUser($firstUser, 'weekly_duels_played', 2, false);
        $this->seedMissionProgressForUser($firstUser, 'event_ga_special', 4, false);
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
