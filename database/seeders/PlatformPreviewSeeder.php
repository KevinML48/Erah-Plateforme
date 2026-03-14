<?php

namespace Database\Seeders;

use Carbon\CarbonImmutable;
use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Application\Actions\Bets\PlaceBetAction;
use App\Application\Actions\Bets\SettleMatchBetsAction;
use App\Application\Actions\Duels\AcceptDuelAction;
use App\Application\Actions\Duels\CreateDuelAction;
use App\Application\Actions\Duels\ExpireDuelAction;
use App\Application\Actions\Duels\RefuseDuelAction;
use App\Application\Actions\Ranking\AddPointsAction;
use App\Application\Actions\Rewards\EnsureCurrentMissionInstancesAction;
use App\Models\Achievement;
use App\Models\ActivityEvent;
use App\Models\AssistantConversation;
use App\Models\AssistantFavorite;
use App\Models\AssistantMessage;
use App\Models\AuditLog;
use App\Models\Bet;
use App\Models\Clip;
use App\Models\ClipComment;
use App\Models\ClipFavorite;
use App\Models\ClipLike;
use App\Models\ClipShare;
use App\Models\ClipSupporterReaction;
use App\Models\ClipView;
use App\Models\ClipVote;
use App\Models\ClipVoteCampaign;
use App\Models\ClipVoteEntry;
use App\Models\ClubReview;
use App\Models\Duel;
use App\Models\EsportMatch;
use App\Models\GalleryPhoto;
use App\Models\Gift;
use App\Models\GiftCartItem;
use App\Models\GiftFavorite;
use App\Models\GiftRedemption;
use App\Models\GiftRedemptionEvent;
use App\Models\HelpArticle;
use App\Models\LiveCode;
use App\Models\LiveCodeRedemption;
use App\Models\MissionCompletion;
use App\Models\MissionInstance;
use App\Models\MissionTemplate;
use App\Models\PlatformEvent;
use App\Models\PointsTransaction;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\RewardWalletTransaction;
use App\Models\ShopItem;
use App\Models\SupporterMonthlyReward;
use App\Models\SupporterPlan;
use App\Models\SupporterPublicProfile;
use App\Models\User;
use App\Models\UserLoginStreak;
use App\Models\UserMission;
use App\Models\UserMissionFocus;
use App\Models\UserPurchase;
use App\Models\UserGuidedTour;
use App\Models\UserSupportSubscription;
use App\Services\AchievementService;
use App\Services\DuelService;
use App\Services\GalleryPhotoImportService;
use App\Services\GuidedTour\PlatformGuidedTourService;
use App\Services\LiveCodeService;
use App\Services\MissionEngine;
use App\Services\MissionFocusService;
use App\Services\PlatformPointService;
use App\Services\RewardGrantService;
use App\Services\ShortcutService;
use App\Services\ShopService;
use App\Services\SupporterAccessResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;

class PlatformPreviewSeeder extends Seeder
{
    private const DEMO_PASSWORD = '12345678';

    private const PREVIEW_KEY = 'platform-preview-v2';

    private CarbonImmutable $now;

    private User $admin;

    /** @var array<string, User> */
    private array $users = [];

    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->warn('PlatformPreviewSeeder est bloque en production.');

            return;
        }

        // Anchor timestamps to a stable daytime within the current day so
        // replaying demo:seed remains deterministic during the same day.
        $this->now = CarbonImmutable::today()->setTime(12, 0, 0);

        $this->call([
            AdminUserSeeder::class,
            LeagueSeeder::class,
        ]);

        if (User::query()->count() < 8) {
            $this->call([DemoDataSeeder::class]);
        }

        $this->call([BettingBaseSeeder::class]);

        if (MissionTemplate::query()->count() === 0 || Gift::query()->count() === 0) {
            $this->call([MissionsAndGiftsSeeder::class]);
        }

        if (SupporterPlan::query()->count() === 0) {
            $this->call([SupporterProgramSeeder::class]);
        }

        if (ClubReview::query()->count() === 0) {
            $this->call([ClubReviewSeeder::class]);
        }

        if (Quiz::query()->count() === 0 || LiveCode::query()->count() === 0 || Achievement::query()->count() === 0) {
            $this->call([CommunityPlatformSeeder::class]);
        }

        if (HelpArticle::query()->count() === 0) {
            $this->call([HelpCenterSeeder::class]);
        }

        $this->seedUsers();
        $this->seedAssistantAndProfileExperience();
        $this->seedProgressAndWallets();
        $this->seedSupporters();
        $this->seedMissionUniverse();
        $this->seedClipsUniverse();
        $this->seedGiftUniverse();
        $this->seedShopUniverse();
        $this->seedMatchesAndBetsUniverse();
        $this->seedDuelsUniverse();
        $this->seedQuizUniverse();
        $this->seedLiveCodeUniverse();
        $this->seedAchievements();
        $this->seedReviewsAndGallery();
        $this->seedPlatformEventsUniverse();
        $this->seedActivityFeed();
        $this->seedAuditFeed();
    }

    private function seedUsers(): void
    {
        $aliases = $this->aliasUsersFromEmails([
            'admin@erah.local' => 'admin',
            'player.one@erah.local' => 'player_one',
            'noah.blitz@erah.local' => 'noah',
            'lina.rush@erah.local' => 'lina',
            'marco.ace@erah.local' => 'marco',
            'zoe.void@erah.local' => 'zoe',
            'yuna.strike@erah.local' => 'yuna',
            'ryan.pulse@erah.local' => 'ryan',
            'maya.nova@erah.local' => 'maya',
            'aiden.wolf@erah.local' => 'aiden',
            'sara.drift@erah.local' => 'sara',
            'leo.zenith@erah.local' => 'leo',
            'nina.flux@erah.local' => 'nina',
            'tom.viper@erah.local' => 'tom',
            'clara.echo@erah.local' => 'clara',
            'hugo.prime@erah.local' => 'hugo',
            'ines.spark@erah.local' => 'ines',
            'alex.comet@erah.local' => 'alex',
            'jade.orbit@erah.local' => 'jade',
            'nora.blaze@erah.local' => 'nora',
            'isaac.volt@erah.local' => 'isaac',
            'paul.rift@erah.local' => 'paul',
            'emma.pulse@erah.local' => 'emma',
            'yanis.crow@erah.local' => 'yanis',
            'chloe.mist@erah.local' => 'chloe',
        ]);

        $this->users = $aliases;
        $this->admin = $this->mustHaveUser('admin');

        $this->upsertUser('admin_demo', [
            'name' => 'ERAH Demo Admin',
            'email' => 'admin.demo@erah.local',
            'role' => User::ROLE_ADMIN,
            'password' => self::DEMO_PASSWORD,
            'bio' => 'Compte admin dedie aux demonstrations locales et staging.',
            'avatar_path' => 'https://picsum.photos/seed/erah-admin-demo/240/240',
            'created_at' => $this->now->subMonths(9),
        ]);

        $this->upsertUser('admin_gmail', [
            'name' => 'Admin Gmail',
            'email' => 'admin@gmail.com',
            'role' => User::ROLE_ADMIN,
            'password' => self::DEMO_PASSWORD,
            'bio' => 'Compte admin de demonstration.',
            'avatar_path' => 'https://picsum.photos/seed/erah-admin-gmail/240/240',
            'created_at' => $this->now->subMonths(8),
        ]);

        $this->upsertUser('member_active', [
            'name' => 'Kylian Frost',
            'email' => 'kylian.frost@erah.local',
            'role' => User::ROLE_USER,
            'password' => self::DEMO_PASSWORD,
            'bio' => 'Membre tres actif sur clips, missions, paris, duels et cadeaux.',
            'avatar_path' => 'https://picsum.photos/seed/erah-member-active/240/240',
            'twitter_url' => 'https://x.com/kylianfrost_erah',
            'created_at' => $this->now->subMonths(6),
        ]);

        $this->upsertUser('member_medium', [
            'name' => 'Lea Circuit',
            'email' => 'lea.circuit@erah.local',
            'role' => User::ROLE_USER,
            'password' => self::DEMO_PASSWORD,
            'bio' => 'Membre regulier, surtout missions, quiz et cadeaux.',
            'avatar_path' => 'https://picsum.photos/seed/erah-member-medium/240/240',
            'instagram_url' => 'https://instagram.com/lea.circuit',
            'created_at' => $this->now->subMonths(4),
        ]);

        $this->upsertUser('member_new', [
            'name' => 'Nolan Spark',
            'email' => 'nolan.spark@erah.local',
            'role' => User::ROLE_USER,
            'password' => self::DEMO_PASSWORD,
            'bio' => 'Nouveau membre en decouverte de la plateforme.',
            'avatar_path' => 'https://picsum.photos/seed/erah-member-new/240/240',
            'created_at' => $this->now->subDays(7),
        ]);

        $this->upsertUser('supporter_alpha', [
            'name' => 'Sonia Vector',
            'email' => 'sonia.vector@erah.local',
            'role' => User::ROLE_USER,
            'password' => self::DEMO_PASSWORD,
            'bio' => 'Supporter active, vote clips, reactions et missions exclusives.',
            'avatar_path' => 'https://picsum.photos/seed/erah-supporter-alpha/240/240',
            'discord_url' => 'https://discord.gg/erah-supporters',
            'created_at' => $this->now->subMonths(5),
        ]);

        $this->upsertUser('supporter_beta', [
            'name' => 'Mathis Nova',
            'email' => 'mathis.nova@erah.local',
            'role' => User::ROLE_USER,
            'password' => self::DEMO_PASSWORD,
            'bio' => 'Supporter regulier, actif sur quiz et live codes.',
            'avatar_path' => 'https://picsum.photos/seed/erah-supporter-beta/240/240',
            'created_at' => $this->now->subMonths(3),
        ]);

        $this->upsertUser('supporter_gamma', [
            'name' => 'Camille Striker',
            'email' => 'camille.striker@erah.local',
            'role' => User::ROLE_USER,
            'password' => self::DEMO_PASSWORD,
            'bio' => 'Supporter orientee engagement communaute et clips.',
            'avatar_path' => 'https://picsum.photos/seed/erah-supporter-gamma/240/240',
            'created_at' => $this->now->subMonths(2),
        ]);

        $this->upsertUser('member_streamer', [
            'name' => 'Eliot Pulse',
            'email' => 'eliot.pulse@erah.local',
            'role' => User::ROLE_USER,
            'password' => self::DEMO_PASSWORD,
            'bio' => 'Createur de clips avec forte activite communautaire.',
            'avatar_path' => 'https://picsum.photos/seed/erah-member-streamer/240/240',
            'tiktok_url' => 'https://tiktok.com/@eliot.pulse',
            'created_at' => $this->now->subMonths(5),
        ]);
    }

    private function seedAssistantAndProfileExperience(): void
    {
        if (
            ! Schema::hasTable('assistant_conversations')
            || ! Schema::hasTable('assistant_messages')
            || ! Schema::hasTable('assistant_favorites')
            || ! Schema::hasTable('user_shortcuts')
            || ! Schema::hasTable('user_guided_tours')
        ) {
            return;
        }

        $shortcutService = app(ShortcutService::class);

        $shortcutProfiles = [
            'member_active' => ['missions', 'paris', 'duels', 'clips', 'profil'],
            'member_medium' => ['missions', 'classement', 'clips', 'favoris', 'profil'],
            'member_new' => ['overview', 'missions', 'clips', 'matchs', 'profil'],
            'supporter_alpha' => ['clips', 'missions', 'duels', 'notifications', 'profil'],
            'supporter_beta' => ['missions', 'paris', 'classement', 'notifications', 'profil'],
            'member_streamer' => ['clips', 'favoris', 'missions', 'classement', 'profil'],
        ];

        foreach ($shortcutProfiles as $alias => $keys) {
            if (! isset($this->users[$alias])) {
                continue;
            }

            $shortcutService->saveForUser($this->users[$alias], $keys);
        }

        $conversationBlueprints = [
            [
                'alias' => 'member_active',
                'title' => 'Optimiser mes missions focus',
                'messages' => [
                    [
                        'role' => AssistantMessage::ROLE_USER,
                        'content' => 'Comment optimiser mes 3 missions focus pour gagner plus vite cette semaine ?',
                        'tokens' => 68,
                        'minutes_ago' => 420,
                    ],
                    [
                        'role' => AssistantMessage::ROLE_ASSISTANT,
                        'content' => 'Priorise 1 mission quotidienne rapide + 1 mission hebdo a forte valeur + 1 mission decouverte que tu peux valider en session courte.',
                        'tokens' => 94,
                        'minutes_ago' => 418,
                    ],
                ],
            ],
            [
                'alias' => 'member_medium',
                'title' => 'Comprendre le suivi cadeaux',
                'messages' => [
                    [
                        'role' => AssistantMessage::ROLE_USER,
                        'content' => 'Quand une demande cadeau passe en shipped, ou je vois le tracking ?',
                        'tokens' => 42,
                        'minutes_ago' => 280,
                    ],
                    [
                        'role' => AssistantMessage::ROLE_ASSISTANT,
                        'content' => 'Depuis Mes commandes cadeaux, ouvre le detail de la demande: le bloc expedition affiche transporteur, tracking et lien si disponible.',
                        'tokens' => 78,
                        'minutes_ago' => 278,
                    ],
                ],
            ],
            [
                'alias' => 'member_new',
                'title' => 'Par ou commencer sur ERAH',
                'messages' => [
                    [
                        'role' => AssistantMessage::ROLE_USER,
                        'content' => 'Je viens d arriver, quel est le meilleur parcours pour progresser sans me perdre ?',
                        'tokens' => 46,
                        'minutes_ago' => 110,
                    ],
                    [
                        'role' => AssistantMessage::ROLE_ASSISTANT,
                        'content' => 'Commence par 2 missions decouverte, puis consulte matchs/paris et termine par 1 clip ou 1 duel pour activer plusieurs modules en une session.',
                        'tokens' => 81,
                        'minutes_ago' => 108,
                    ],
                ],
            ],
            [
                'alias' => 'supporter_alpha',
                'title' => 'Supporter et avantages actifs',
                'messages' => [
                    [
                        'role' => AssistantMessage::ROLE_USER,
                        'content' => 'Quels avantages supporter sont actifs sur mon compte ce mois-ci ?',
                        'tokens' => 33,
                        'minutes_ago' => 64,
                    ],
                    [
                        'role' => AssistantMessage::ROLE_ASSISTANT,
                        'content' => 'Tes avantages actifs: bonus progression supporter, reactions supporter sur clips et missions reservees supporters.',
                        'tokens' => 63,
                        'minutes_ago' => 62,
                    ],
                ],
            ],
        ];

        foreach ($conversationBlueprints as $blueprint) {
            $alias = (string) ($blueprint['alias'] ?? '');
            if ($alias === '' || ! isset($this->users[$alias])) {
                continue;
            }

            /** @var User $user */
            $user = $this->users[$alias];
            $title = (string) ($blueprint['title'] ?? 'Conversation demo');

            $conversation = AssistantConversation::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'title' => $title,
                ],
                [
                    'provider' => 'preview',
                    'model' => 'preview-assistant-v1',
                ],
            );

            $lastMessageAt = $this->now->subDays(1);

            foreach ((array) ($blueprint['messages'] ?? []) as $message) {
                $minutesAgo = max(1, (int) ($message['minutes_ago'] ?? 1));
                $createdAt = $this->now->subMinutes($minutesAgo);
                $role = (string) ($message['role'] ?? AssistantMessage::ROLE_ASSISTANT);
                $tokens = max(1, (int) ($message['tokens'] ?? 1));

                AssistantMessage::query()->updateOrCreate(
                    [
                        'assistant_conversation_id' => $conversation->id,
                        'role' => $role,
                        'content' => (string) ($message['content'] ?? ''),
                    ],
                    [
                        'provider' => 'preview',
                        'model' => 'preview-assistant-v1',
                        'prompt_tokens' => $role === AssistantMessage::ROLE_USER ? $tokens : null,
                        'completion_tokens' => $role === AssistantMessage::ROLE_ASSISTANT ? $tokens : null,
                        'metadata' => ['seed' => self::PREVIEW_KEY, 'alias' => $alias],
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ],
                );

                if ($createdAt->greaterThan($lastMessageAt)) {
                    $lastMessageAt = $createdAt;
                }
            }

            $conversation->forceFill([
                'provider' => 'preview',
                'model' => 'preview-assistant-v1',
                'last_message_at' => $lastMessageAt,
            ])->save();
        }

        $favoriteBlueprints = [
            [
                'alias' => 'member_active',
                'question' => 'Quelle routine me conseilles-tu pour convertir mes points en cadeaux ?',
                'answer' => 'Combine missions rapides + un pari raisonnable + verification du stock cadeaux avant checkout pour eviter les ruptures.',
                'details' => ['module' => 'gifts', 'priority' => 'high'],
                'sources' => ['missions', 'gifts', 'wallet'],
                'next_steps' => ['Ouvrir Missions', 'Verifier mon solde points', 'Consulter Mes commandes cadeaux'],
            ],
            [
                'alias' => 'member_medium',
                'question' => 'Comment suivre simplement toutes mes commandes cadeaux ?',
                'answer' => 'Passe par Mes commandes cadeaux: tu as le statut, la timeline et les infos transport sur chaque detail de demande.',
                'details' => ['module' => 'gift_redemptions', 'priority' => 'medium'],
                'sources' => ['gift_redemptions', 'gift_redemption_events'],
                'next_steps' => ['Filtrer sur shipped', 'Ouvrir la commande la plus recente'],
            ],
            [
                'alias' => 'supporter_alpha',
                'question' => 'Comment profiter au mieux de mes avantages supporter ?',
                'answer' => 'Utilise les missions reservees supporters puis active les reactions supporter sur clips pour maximiser progression et visibilite.',
                'details' => ['module' => 'supporter', 'priority' => 'high'],
                'sources' => ['supporter_plans', 'missions', 'clips'],
                'next_steps' => ['Verifier statut supporter', 'Completer mission supporter active'],
            ],
        ];

        foreach ($favoriteBlueprints as $favorite) {
            $alias = (string) ($favorite['alias'] ?? '');
            if ($alias === '' || ! isset($this->users[$alias])) {
                continue;
            }

            /** @var User $user */
            $user = $this->users[$alias];
            $question = (string) ($favorite['question'] ?? '');
            $fingerprint = hash('sha256', self::PREVIEW_KEY.'|'.$alias.'|'.$question);

            AssistantFavorite::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'fingerprint' => $fingerprint,
                ],
                [
                    'question' => $question,
                    'answer' => (string) ($favorite['answer'] ?? ''),
                    'details' => (array) ($favorite['details'] ?? []),
                    'sources' => (array) ($favorite['sources'] ?? []),
                    'next_steps' => (array) ($favorite['next_steps'] ?? []),
                ],
            );
        }

        $tourStates = [
            'member_active' => [
                'status' => UserGuidedTour::STATUS_COMPLETED,
                'current_step_index' => 5,
                'is_paused' => true,
                'started_at' => $this->now->subDays(20),
                'last_seen_at' => $this->now->subDays(2),
                'completed_at' => $this->now->subDays(2),
            ],
            'member_medium' => [
                'status' => UserGuidedTour::STATUS_IN_PROGRESS,
                'current_step_index' => 3,
                'is_paused' => true,
                'started_at' => $this->now->subDays(6),
                'last_seen_at' => $this->now->subDays(1),
                'completed_at' => null,
            ],
            'member_new' => [
                'status' => UserGuidedTour::STATUS_IN_PROGRESS,
                'current_step_index' => 1,
                'is_paused' => false,
                'started_at' => $this->now->subDays(1),
                'last_seen_at' => $this->now->subHours(4),
                'completed_at' => null,
            ],
        ];

        foreach ($tourStates as $alias => $state) {
            if (! isset($this->users[$alias])) {
                continue;
            }

            UserGuidedTour::query()->updateOrCreate(
                [
                    'user_id' => $this->users[$alias]->id,
                    'tour_key' => PlatformGuidedTourService::TOUR_KEY,
                ],
                $state,
            );
        }
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function upsertUser(string $alias, array $payload): void
    {
        $email = (string) $payload['email'];
        $password = (string) ($payload['password'] ?? self::DEMO_PASSWORD);

        $user = User::query()->where('email', $email)->first();
        if (! $user) {
            $user = new User();
            $user->email = $email;
            $user->password = $password;
            $user->created_at = $payload['created_at'] ?? $this->now;
        }

        $user->name = (string) ($payload['name'] ?? $user->name);
        $user->role = (string) ($payload['role'] ?? User::ROLE_USER);
        $user->bio = $payload['bio'] ?? $user->bio;
        $user->avatar_path = $payload['avatar_path'] ?? $user->avatar_path;
        $user->twitter_url = $payload['twitter_url'] ?? null;
        $user->instagram_url = $payload['instagram_url'] ?? null;
        $user->tiktok_url = $payload['tiktok_url'] ?? null;
        $user->discord_url = $payload['discord_url'] ?? null;
        $user->email_verified_at = $user->email_verified_at ?: $this->now;
        $user->password = $password;
        $user->save();

        $this->users[$alias] = $user->fresh();
    }

    /**
     * @param array<string, string> $emails
     * @return array<string, User>
     */
    private function aliasUsersFromEmails(array $emails): array
    {
        $rows = User::query()
            ->whereIn('email', array_keys($emails))
            ->get()
            ->keyBy('email');

        $mapped = [];
        foreach ($emails as $email => $alias) {
            $user = $rows->get($email);
            if (! $user) {
                continue;
            }

            $mapped[$alias] = $user;
        }

        return $mapped;
    }

    private function seedProgressAndWallets(): void
    {
        $addPoints = app(AddPointsAction::class);
        $platformPoints = app(PlatformPointService::class);

        $profiles = [
            'member_active' => ['xp' => 48200, 'rank' => 36500, 'wallet' => 12500, 'streak' => 18, 'best' => 24],
            'player_one' => ['xp' => 36400, 'rank' => 24800, 'wallet' => 9400, 'streak' => 14, 'best' => 19],
            'noah' => ['xp' => 29200, 'rank' => 21000, 'wallet' => 8200, 'streak' => 11, 'best' => 16],
            'lina' => ['xp' => 22600, 'rank' => 16500, 'wallet' => 7600, 'streak' => 9, 'best' => 14],
            'marco' => ['xp' => 17200, 'rank' => 12300, 'wallet' => 6800, 'streak' => 7, 'best' => 11],
            'zoe' => ['xp' => 12800, 'rank' => 9200, 'wallet' => 5900, 'streak' => 6, 'best' => 9],
            'member_medium' => ['xp' => 7600, 'rank' => 6100, 'wallet' => 5200, 'streak' => 5, 'best' => 8],
            'yuna' => ['xp' => 5300, 'rank' => 4100, 'wallet' => 4400, 'streak' => 4, 'best' => 7],
            'ryan' => ['xp' => 3600, 'rank' => 2800, 'wallet' => 3600, 'streak' => 3, 'best' => 5],
            'member_new' => ['xp' => 900, 'rank' => 700, 'wallet' => 2400, 'streak' => 2, 'best' => 3],
            'supporter_alpha' => ['xp' => 19400, 'rank' => 13600, 'wallet' => 7300, 'streak' => 8, 'best' => 12],
            'supporter_beta' => ['xp' => 11200, 'rank' => 8400, 'wallet' => 5400, 'streak' => 5, 'best' => 7],
            'supporter_gamma' => ['xp' => 6900, 'rank' => 5100, 'wallet' => 4700, 'streak' => 4, 'best' => 6],
            'member_streamer' => ['xp' => 15800, 'rank' => 11900, 'wallet' => 6900, 'streak' => 10, 'best' => 15],
        ];

        foreach ($profiles as $alias => $stats) {
            if (! isset($this->users[$alias])) {
                continue;
            }

            $user = $this->users[$alias];

            $addPoints->execute(
                user: $user,
                kind: PointsTransaction::KIND_XP,
                points: (int) $stats['xp'],
                sourceType: 'seed.preview.xp',
                sourceId: self::PREVIEW_KEY.'.'.$alias,
                actor: $this->admin,
                meta: ['seed' => self::PREVIEW_KEY],
            );

            $addPoints->execute(
                user: $user,
                kind: PointsTransaction::KIND_RANK,
                points: (int) $stats['rank'],
                sourceType: 'seed.preview.rank',
                sourceId: self::PREVIEW_KEY.'.'.$alias,
                actor: $this->admin,
                meta: ['seed' => self::PREVIEW_KEY],
            );

            $platformPoints->credit(
                user: $user,
                amount: (int) $stats['wallet'],
                type: RewardWalletTransaction::TYPE_GRANT,
                uniqueKey: 'seed.preview.wallet.'.self::PREVIEW_KEY.'.'.$alias,
                meta: ['seed' => self::PREVIEW_KEY],
                refType: RewardWalletTransaction::REF_TYPE_SYSTEM,
                refId: $alias,
                initialBalanceIfMissing: 0,
            );

            UserLoginStreak::query()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'current_streak' => (int) $stats['streak'],
                    'longest_streak' => (int) $stats['best'],
                    'last_login_on' => $this->now->toDateString(),
                    'current_multiplier' => $stats['streak'] >= 14 ? 1.15 : ($stats['streak'] >= 7 ? 1.10 : 1.05),
                    'last_reward_points' => (int) max(20, ((int) $stats['streak']) * 10),
                    'streak_started_at' => $this->now->subDays((int) $stats['streak']),
                ],
            );
        }
    }

    private function seedSupporters(): void
    {
        $resolver = app(SupporterAccessResolver::class);
        $resolver->ensureConfiguredPlans();
        $resolver->ensureCommunityGoals();

        $plans = SupporterPlan::query()->orderBy('sort_order')->orderBy('id')->get();
        $planMonthly = $plans->get(0) ?: $plans->first();
        $planHalf = $plans->get(1) ?: $planMonthly;
        $planYear = $plans->get(2) ?: $planHalf;

        if (! $planMonthly) {
            throw new RuntimeException('Aucun plan supporter disponible pour le seed preview.');
        }

        $subscriptions = [
            'supporter_alpha' => ['plan' => $planYear, 'status' => UserSupportSubscription::STATUS_ACTIVE, 'months' => 8],
            'supporter_beta' => ['plan' => $planHalf, 'status' => UserSupportSubscription::STATUS_ACTIVE, 'months' => 5],
            'supporter_gamma' => ['plan' => $planMonthly, 'status' => UserSupportSubscription::STATUS_ACTIVE, 'months' => 2],
            'member_active' => ['plan' => $planMonthly, 'status' => UserSupportSubscription::STATUS_PAST_DUE, 'months' => 1],
            'member_medium' => ['plan' => $planMonthly, 'status' => UserSupportSubscription::STATUS_CANCELED, 'months' => 1],
        ];

        foreach ($subscriptions as $alias => $data) {
            if (! isset($this->users[$alias])) {
                continue;
            }

            /** @var SupporterPlan $plan */
            $plan = $data['plan'];
            $user = $this->users[$alias];
            $startedAt = $this->now->subMonths((int) $data['months'])->startOfMonth();
            $currentPeriodStart = $this->now->startOfMonth();
            $currentPeriodEnd = $currentPeriodStart->addMonths((int) ($plan->billing_months ?: 1));
            $status = (string) $data['status'];

            UserSupportSubscription::query()->updateOrCreate(
                ['provider_subscription_id' => 'preview-sub-'.self::PREVIEW_KEY.'-'.$alias],
                [
                    'user_id' => $user->id,
                    'supporter_plan_id' => $plan->id,
                    'status' => $status,
                    'provider' => 'demo',
                    'provider_customer_id' => 'preview-customer-'.$user->id,
                    'provider_price_id' => $plan->stripe_price_id ?: $plan->key,
                    'checkout_session_id' => null,
                    'started_at' => $startedAt,
                    'current_period_start' => $currentPeriodStart,
                    'current_period_end' => $status === UserSupportSubscription::STATUS_ACTIVE ? $currentPeriodEnd : $this->now->subDay(),
                    'canceled_at' => $status === UserSupportSubscription::STATUS_CANCELED ? $this->now->subDays(10) : null,
                    'ended_at' => $status === UserSupportSubscription::STATUS_CANCELED ? $this->now->subDays(2) : null,
                    'meta' => ['seed' => self::PREVIEW_KEY],
                ],
            );

            SupporterPublicProfile::query()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'is_visible_on_wall' => true,
                    'display_name' => $user->name,
                ],
            );

            for ($i = 0; $i < 3; $i++) {
                $rewardMonth = $this->now->subMonths($i)->startOfMonth()->toDateString();
                $grantedAt = $this->now->subMonths($i)->startOfMonth()->addDays(1);

                $existingReward = SupporterMonthlyReward::query()
                    ->where('user_id', $user->id)
                    ->whereDate('reward_month', $rewardMonth)
                    ->where('reward_key', 'monthly_progress')
                    ->first();

                if ($existingReward) {
                    $existingReward->granted_at = $grantedAt;
                    $existingReward->save();
                    continue;
                }

                SupporterMonthlyReward::query()->create([
                    'user_id' => $user->id,
                    'reward_month' => $rewardMonth,
                    'reward_key' => 'monthly_progress',
                    'granted_at' => $grantedAt,
                ]);
            }
        }

        $resolver->unlockCommunityGoals();
    }

    private function seedMissionUniverse(): void
    {
        $ensureInstances = app(EnsureCurrentMissionInstancesAction::class);
        $missionEngine = app(MissionEngine::class);
        $missionFocusService = app(MissionFocusService::class);
        $eventWindowStart = $this->now->subDays(2)->startOfDay();
        $eventWindowEnd = $this->now->addDays(10)->endOfDay();

        // Keep event windows deterministic on replay to avoid creating new
        // mission instances from drifting timestamps on every seed run.
        $eventWindowTemplates = MissionTemplate::query()
            ->where('scope', MissionTemplate::SCOPE_EVENT_WINDOW)
            ->get(['id']);

        MissionTemplate::query()
            ->whereIn('id', $eventWindowTemplates->pluck('id'))
            ->update([
                'start_at' => $eventWindowStart,
                'end_at' => $eventWindowEnd,
            ]);

        if ($eventWindowTemplates->isNotEmpty()) {
            MissionInstance::query()
                ->whereIn('mission_template_id', $eventWindowTemplates->pluck('id'))
                ->where(function ($query) use ($eventWindowStart, $eventWindowEnd): void {
                    $query
                        ->where('period_start', '!=', $eventWindowStart)
                        ->orWhere('period_end', '!=', $eventWindowEnd);
                })
                ->delete();
        }

        foreach ($this->users as $user) {
            if ($user->role !== User::ROLE_USER) {
                continue;
            }

            $ensureInstances->execute($user);
        }

        $signals = [
            'member_active' => ['login.daily' => 1, 'clip.like' => 6, 'clip.comment' => 4, 'clip.favorite' => 3, 'clip.share' => 2, 'bet.placed' => 4, 'bet.won' => 2, 'duel.sent' => 2, 'duel.accepted' => 1, 'duel.play' => 3, 'duel.win' => 1, 'quiz.attempt' => 2, 'quiz.pass' => 1, 'shop.purchase' => 2, 'live_code.redeem' => 1],
            'member_medium' => ['login.daily' => 1, 'clip.like' => 4, 'clip.comment' => 2, 'clip.favorite' => 2, 'bet.placed' => 2, 'duel.play' => 1, 'quiz.attempt' => 1, 'shop.purchase' => 1],
            'member_new' => ['login.daily' => 1, 'clip.like' => 2, 'clip.comment' => 1, 'quiz.attempt' => 1],
            'supporter_alpha' => ['login.daily' => 1, 'clip.like' => 5, 'clip.comment' => 3, 'supporter.monthly' => 1, 'duel.play' => 1, 'live_code.redeem' => 1],
            'supporter_beta' => ['login.daily' => 1, 'clip.like' => 3, 'supporter.monthly' => 1, 'quiz.pass' => 1],
        ];

        foreach ($signals as $alias => $events) {
            if (! isset($this->users[$alias])) {
                continue;
            }

            $user = $this->users[$alias];
            foreach ($events as $eventType => $count) {
                for ($i = 1; $i <= $count; $i++) {
                    $missionEngine->recordEvent($user, $eventType, 1, [
                        'event_key' => self::PREVIEW_KEY.'.mission.'.$alias.'.'.$eventType.'.'.$i,
                        'subject_type' => User::class,
                        'subject_id' => (string) $user->id,
                        'seed' => self::PREVIEW_KEY,
                    ]);
                }
            }
        }

        $focusByUser = [
            'member_active' => ['launch.daily-login', 'launch.bet-read-the-game', 'launch.community-pulse'],
            'member_medium' => ['launch.daily-login', 'launch.active-support', 'launch.weekly-routine'],
            'member_new' => ['launch.daily-login', 'launch.first-duel', 'launch.mission-of-the-moment'],
        ];

        foreach ($focusByUser as $alias => $templateKeys) {
            if (! isset($this->users[$alias])) {
                continue;
            }

            $user = $this->users[$alias];
            UserMissionFocus::query()->where('user_id', $user->id)->delete();

            foreach ($templateKeys as $index => $templateKey) {
                $template = MissionTemplate::query()->where('key', $templateKey)->where('is_active', true)->first();
                if (! $template) {
                    continue;
                }

                try {
                    $focus = $missionFocusService->add($user, $template);
                    if ((int) $focus->sort_order !== ($index + 1)) {
                        $focus->sort_order = $index + 1;
                        $focus->save();
                    }
                } catch (Throwable) {
                    // Ignore focus collisions in replay mode.
                }
            }
        }

        $newUser = $this->users['member_new'] ?? null;
        if ($newUser) {
            $inProgress = UserMission::query()
                ->where('user_id', $newUser->id)
                ->whereHas('instance.template', fn ($query) => $query->where('key', 'launch.community-voice'))
                ->latest('id')
                ->first();

            if ($inProgress) {
                $inProgress->progress_count = min(1, (int) ($inProgress->instance?->template?->target_count ?? 1));
                $inProgress->completed_at = null;
                $inProgress->rewarded_at = null;
                $inProgress->claimed_at = null;
                $inProgress->save();
            }
        }

        $activeUser = $this->users['member_active'] ?? null;
        if ($activeUser) {
            $completed = UserMission::query()
                ->where('user_id', $activeUser->id)
                ->whereHas('instance.template', fn ($query) => $query->where('key', 'launch.first-duel'))
                ->latest('id')
                ->first();

            if ($completed) {
                $completed->progress_count = max(1, (int) ($completed->instance?->template?->target_count ?? 1));
                $completed->completed_at = $completed->completed_at ?: $this->now->subHours(6);
                $completed->rewarded_at = $completed->rewarded_at ?: $completed->completed_at;
                $completed->claimed_at = $completed->claimed_at ?: $completed->completed_at;
                $completed->save();

                MissionCompletion::query()->updateOrCreate(
                    ['user_id' => $activeUser->id, 'user_mission_id' => $completed->id],
                    ['completed_at' => $completed->completed_at, 'created_at' => $completed->completed_at],
                );
            }
        }
    }

    private function seedClipsUniverse(): void
    {
        $clipDefinitions = [
            ['slug' => 'preview-ace-night', 'title' => 'Ace Night Highlight', 'is_published' => true, 'days_ago' => 1, 'creator' => 'member_streamer'],
            ['slug' => 'preview-rl-air-dribble', 'title' => 'Rocket League Air Dribble', 'is_published' => true, 'days_ago' => 2, 'creator' => 'supporter_alpha'],
            ['slug' => 'preview-mid-control-call', 'title' => 'Mid Control Team Call', 'is_published' => true, 'days_ago' => 3, 'creator' => 'member_active'],
            ['slug' => 'preview-double-entry-bo3', 'title' => 'Double Entry BO3', 'is_published' => true, 'days_ago' => 4, 'creator' => 'noah'],
            ['slug' => 'preview-retake-utility', 'title' => 'Retake Utility Sequence', 'is_published' => true, 'days_ago' => 5, 'creator' => 'lina'],
            ['slug' => 'preview-final-round-focus', 'title' => 'Final Round Focus', 'is_published' => true, 'days_ago' => 6, 'creator' => 'marco'],
            ['slug' => 'preview-sniper-angles', 'title' => 'Sniper Angle Masterclass', 'is_published' => true, 'days_ago' => 8, 'creator' => 'zoe'],
            ['slug' => 'preview-support-smokes', 'title' => 'Support Smokes Layering', 'is_published' => true, 'days_ago' => 10, 'creator' => 'supporter_beta'],
            ['slug' => 'preview-draft-content-1', 'title' => 'Draft Content - Strat Notes', 'is_published' => false, 'days_ago' => null, 'creator' => 'admin'],
            ['slug' => 'preview-draft-content-2', 'title' => 'Draft Content - Match Debrief', 'is_published' => false, 'days_ago' => null, 'creator' => 'admin_demo'],
        ];

        $clips = collect();

        foreach ($clipDefinitions as $index => $definition) {
            $creator = $this->users[$definition['creator']] ?? $this->admin;
            $publishedAt = $definition['is_published'] && is_int($definition['days_ago'])
                ? $this->now->subDays($definition['days_ago'])->setTime(20, 15)
                : null;

            $clip = Clip::query()->updateOrCreate(
                ['slug' => $definition['slug']],
                [
                    'title' => $definition['title'],
                    'description' => 'Clip preview '.($index + 1).' pour enrichir le feed communautaire.',
                    'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
                    'thumbnail_url' => 'https://picsum.photos/seed/'.urlencode('clip-'.$definition['slug']).'/1280/720',
                    'is_published' => $definition['is_published'],
                    'published_at' => $publishedAt,
                    'created_by' => $creator->id,
                    'updated_by' => $this->admin->id,
                ],
            );

            $clips->push($clip->fresh());
        }

        $publishedClips = $clips->where('is_published', true)->values();

        $participants = collect([
            'member_active',
            'member_medium',
            'member_new',
            'player_one',
            'noah',
            'lina',
            'marco',
            'zoe',
            'yuna',
            'supporter_alpha',
            'supporter_beta',
            'member_streamer',
        ])
            ->filter(fn (string $alias): bool => isset($this->users[$alias]))
            ->map(fn (string $alias): User => $this->users[$alias])
            ->values();

        foreach ($publishedClips as $index => $clip) {
            $likeUsers = $participants->take(4 + ($index % 5));
            foreach ($likeUsers as $user) {
                ClipLike::query()->firstOrCreate([
                    'clip_id' => $clip->id,
                    'user_id' => $user->id,
                ]);
            }

            $favoriteUsers = $participants->slice($index % max(1, $participants->count() - 3), 3);
            foreach ($favoriteUsers as $user) {
                ClipFavorite::query()->firstOrCreate([
                    'clip_id' => $clip->id,
                    'user_id' => $user->id,
                ]);
            }

            for ($commentIndex = 1; $commentIndex <= 2; $commentIndex++) {
                $commentAuthor = $participants->get(($index + $commentIndex) % max(1, $participants->count()));
                if (! $commentAuthor) {
                    continue;
                }

                $comment = ClipComment::query()->firstOrCreate(
                    [
                        'clip_id' => $clip->id,
                        'user_id' => $commentAuthor->id,
                        'body' => 'Preview commentaire '.$commentIndex.' sur '.$clip->title.'.',
                    ],
                    [
                        'parent_id' => null,
                        'status' => ClipComment::STATUS_PUBLISHED,
                        'moderated_at' => null,
                    ],
                );

                if ($commentIndex === 1) {
                    $replyAuthor = $participants->get(($index + $commentIndex + 2) % max(1, $participants->count()));
                    if ($replyAuthor) {
                        ClipComment::query()->firstOrCreate(
                            [
                                'clip_id' => $clip->id,
                                'parent_id' => $comment->id,
                                'user_id' => $replyAuthor->id,
                                'body' => 'Reponse preview: excellent rythme sur ce clip.',
                            ],
                            [
                                'status' => ClipComment::STATUS_PUBLISHED,
                                'moderated_at' => null,
                            ],
                        );
                    }
                }
            }

            $channels = ['link', 'discord', 'copy'];
            foreach ($participants->take(3) as $shareIndex => $user) {
                ClipShare::query()->firstOrCreate([
                    'clip_id' => $clip->id,
                    'user_id' => $user->id,
                    'channel' => $channels[$shareIndex] ?? 'link',
                    'shared_url' => url('/clips/'.$clip->slug),
                ]);
            }

            foreach ($participants->take(8) as $viewIndex => $user) {
                ClipView::query()->firstOrCreate(
                    [
                        'clip_id' => $clip->id,
                        'user_id' => $user->id,
                        'session_id' => 'preview-session-'.$clip->id.'-'.$user->id,
                    ],
                    [
                        'ip_hash' => sha1('preview-ip-'.$user->id),
                        'meta' => ['seed' => self::PREVIEW_KEY, 'view_index' => $viewIndex + 1],
                        'viewed_at' => $this->now->subMinutes(20 + ($index * 4) + $viewIndex),
                    ],
                );
            }

            foreach (['supporter_alpha', 'supporter_beta', 'supporter_gamma'] as $supporterAlias) {
                if (! isset($this->users[$supporterAlias])) {
                    continue;
                }

                ClipSupporterReaction::query()->firstOrCreate([
                    'clip_id' => $clip->id,
                    'user_id' => $this->users[$supporterAlias]->id,
                    'reaction_key' => $index % 2 === 0 ? 'fire' : 'gg',
                ]);
            }
        }

        $campaignActive = ClipVoteCampaign::query()->updateOrCreate(
            ['title' => 'Preview Vote Hebdo Active'],
            [
                'type' => ClipVoteCampaign::TYPE_WEEKLY,
                'starts_at' => $this->now->subDays(1),
                'ends_at' => $this->now->addDays(4),
                'status' => ClipVoteCampaign::STATUS_ACTIVE,
                'winner_clip_id' => null,
            ],
        );

        $campaignClosed = ClipVoteCampaign::query()->updateOrCreate(
            ['title' => 'Preview Vote Hebdo Closed'],
            [
                'type' => ClipVoteCampaign::TYPE_WEEKLY,
                'starts_at' => $this->now->subDays(12),
                'ends_at' => $this->now->subDays(5),
                'status' => ClipVoteCampaign::STATUS_CLOSED,
                'winner_clip_id' => null,
            ],
        );

        $winnerClip = $publishedClips->first();
        $campaignSettled = ClipVoteCampaign::query()->updateOrCreate(
            ['title' => 'Preview Vote Mensuel Settled'],
            [
                'type' => ClipVoteCampaign::TYPE_MONTHLY,
                'starts_at' => $this->now->subMonths(1)->startOfMonth(),
                'ends_at' => $this->now->subMonths(1)->endOfMonth(),
                'status' => ClipVoteCampaign::STATUS_SETTLED,
                'winner_clip_id' => $winnerClip?->id,
            ],
        );

        $campaigns = collect([$campaignActive, $campaignClosed, $campaignSettled]);
        foreach ($campaigns as $campaign) {
            foreach ($publishedClips->take(5) as $clip) {
                ClipVoteEntry::query()->firstOrCreate([
                    'campaign_id' => $campaign->id,
                    'clip_id' => $clip->id,
                ]);
            }

            $entries = ClipVoteEntry::query()
                ->where('campaign_id', $campaign->id)
                ->orderBy('id')
                ->get();
            if ($entries->isEmpty()) {
                continue;
            }

            foreach ($participants->take(10) as $voteIndex => $user) {
                $entry = $entries->get($voteIndex % $entries->count());
                if (! $entry) {
                    continue;
                }

                ClipVote::query()->updateOrCreate(
                    [
                        'campaign_id' => $campaign->id,
                        'user_id' => $user->id,
                    ],
                    ['clip_id' => $entry->clip_id],
                );
            }
        }

        foreach ($clips as $clip) {
            Clip::query()->whereKey($clip->id)->update([
                'likes_count' => ClipLike::query()->where('clip_id', $clip->id)->count(),
                'favorites_count' => ClipFavorite::query()->where('clip_id', $clip->id)->count(),
                'comments_count' => ClipComment::query()->where('clip_id', $clip->id)->count(),
                'updated_by' => $this->admin->id,
                'updated_at' => $this->now,
            ]);
        }
    }

    private function seedGiftUniverse(): void
    {
        $catalog = [
            ['title' => 'Hoodie ERAH', 'cost_points' => 1400, 'stock' => 8, 'is_active' => true, 'is_featured' => true, 'sort_order' => 10],
            ['title' => 'Casquette ERAH', 'cost_points' => 500, 'stock' => 0, 'is_active' => true, 'is_featured' => true, 'sort_order' => 20],
            ['title' => 'Mug ERAH', 'cost_points' => 600, 'stock' => 12, 'is_active' => true, 'is_featured' => false, 'sort_order' => 30],
            ['title' => 'Coaching Session 30m', 'cost_points' => 1750, 'stock' => 3, 'is_active' => true, 'is_featured' => true, 'sort_order' => 40],
            ['title' => 'Discord Role Elite', 'cost_points' => 450, 'stock' => 999, 'is_active' => true, 'is_featured' => false, 'sort_order' => 50],
            ['title' => 'Bootcamp Access 1 Day', 'cost_points' => 2600, 'stock' => 1, 'is_active' => true, 'is_featured' => true, 'sort_order' => 60],
            ['title' => 'Collector Pin ERAH', 'cost_points' => 320, 'stock' => 16, 'is_active' => true, 'is_featured' => false, 'sort_order' => 70],
            ['title' => 'Starter Pack Stickers', 'cost_points' => 180, 'stock' => 24, 'is_active' => true, 'is_featured' => false, 'sort_order' => 80],
            ['title' => 'VIP Arena Experience', 'cost_points' => 3200, 'stock' => 2, 'is_active' => true, 'is_featured' => true, 'sort_order' => 90],
            ['title' => 'Legacy Reward - Archive', 'cost_points' => 2000, 'stock' => 0, 'is_active' => false, 'is_featured' => false, 'sort_order' => 100],
        ];

        foreach ($catalog as $index => $item) {
            Gift::query()->updateOrCreate(
                ['title' => $item['title']],
                [
                    'description' => 'Article cadeau preview #'.($index + 1).' pour tester catalogue, stock et fulfilment.',
                    'image_url' => 'https://picsum.photos/seed/'.urlencode('gift-'.$item['title']).'/720/720',
                    'cost_points' => $item['cost_points'],
                    'stock' => $item['stock'],
                    'is_active' => $item['is_active'],
                    'is_featured' => $item['is_featured'],
                    'sort_order' => $item['sort_order'],
                ],
            );
        }

        $favorites = [
            'member_active' => ['Hoodie ERAH', 'Bootcamp Access 1 Day', 'VIP Arena Experience'],
            'member_medium' => ['Mug ERAH', 'Coaching Session 30m'],
            'member_new' => ['Starter Pack Stickers', 'Collector Pin ERAH'],
            'supporter_alpha' => ['Discord Role Elite', 'Hoodie ERAH'],
            'supporter_beta' => ['Mug ERAH', 'Collector Pin ERAH'],
        ];

        foreach ($favorites as $alias => $giftTitles) {
            if (! isset($this->users[$alias])) {
                continue;
            }

            $user = $this->users[$alias];
            foreach ($giftTitles as $title) {
                $gift = $this->findGift($title);
                if (! $gift) {
                    continue;
                }

                GiftFavorite::query()->updateOrCreate(
                    ['user_id' => $user->id, 'gift_id' => $gift->id],
                    ['updated_at' => $this->now, 'created_at' => $this->now->subDays(5)],
                );
            }
        }

        $cartRows = [
            'member_active' => [['title' => 'Coaching Session 30m', 'qty' => 1], ['title' => 'Mug ERAH', 'qty' => 2]],
            'member_medium' => [['title' => 'Hoodie ERAH', 'qty' => 1]],
            'supporter_beta' => [['title' => 'Collector Pin ERAH', 'qty' => 3], ['title' => 'Starter Pack Stickers', 'qty' => 2]],
        ];

        foreach ($cartRows as $alias => $items) {
            if (! isset($this->users[$alias])) {
                continue;
            }

            $user = $this->users[$alias];
            foreach ($items as $lineIndex => $item) {
                $gift = $this->findGift($item['title']);
                if (! $gift) {
                    continue;
                }

                GiftCartItem::query()->updateOrCreate(
                    ['user_id' => $user->id, 'gift_id' => $gift->id],
                    [
                        'quantity' => (int) $item['qty'],
                        'added_at' => $this->now->subHours(2 + $lineIndex),
                    ],
                );
            }
        }

        $redemptionScenarios = [
            [
                'key' => 'pending-old',
                'user' => 'member_new',
                'gift' => 'Collector Pin ERAH',
                'status' => GiftRedemption::STATUS_PENDING,
                'requested_at' => $this->now->subHours(78),
            ],
            [
                'key' => 'approved',
                'user' => 'member_medium',
                'gift' => 'Mug ERAH',
                'status' => GiftRedemption::STATUS_APPROVED,
                'requested_at' => $this->now->subHours(40),
                'approved_at' => $this->now->subHours(36),
            ],
            [
                'key' => 'shipped-no-tracking',
                'user' => 'member_active',
                'gift' => 'Hoodie ERAH',
                'status' => GiftRedemption::STATUS_SHIPPED,
                'requested_at' => $this->now->subHours(34),
                'approved_at' => $this->now->subHours(30),
                'shipped_at' => $this->now->subHours(22),
            ],
            [
                'key' => 'shipped-with-tracking',
                'user' => 'supporter_alpha',
                'gift' => 'Coaching Session 30m',
                'status' => GiftRedemption::STATUS_SHIPPED,
                'requested_at' => $this->now->subDays(3),
                'approved_at' => $this->now->subDays(2)->subHours(18),
                'shipped_at' => $this->now->subDays(2)->subHours(6),
                'tracking_code' => 'TRK-PREVIEW-483920',
                'tracking_carrier' => 'Colissimo',
                'shipping_note' => 'Depart entrepot Paris',
            ],
            [
                'key' => 'delivered',
                'user' => 'supporter_beta',
                'gift' => 'Discord Role Elite',
                'status' => GiftRedemption::STATUS_DELIVERED,
                'requested_at' => $this->now->subDays(9),
                'approved_at' => $this->now->subDays(8),
                'shipped_at' => $this->now->subDays(7),
                'delivered_at' => $this->now->subDays(6),
                'tracking_code' => 'TRK-PREVIEW-100920',
                'tracking_carrier' => 'Chronopost',
            ],
            [
                'key' => 'rejected',
                'user' => 'member_streamer',
                'gift' => 'VIP Arena Experience',
                'status' => GiftRedemption::STATUS_REJECTED,
                'requested_at' => $this->now->subDays(5),
                'rejected_at' => $this->now->subDays(4),
                'reason' => 'Adresse de livraison incompl ete',
            ],
            [
                'key' => 'refunded',
                'user' => 'noah',
                'gift' => 'Bootcamp Access 1 Day',
                'status' => GiftRedemption::STATUS_REFUNDED,
                'requested_at' => $this->now->subDays(7),
                'approved_at' => $this->now->subDays(6),
                'shipped_at' => $this->now->subDays(5),
                'reason' => 'Remboursement exceptionnel admin',
            ],
            [
                'key' => 'cancelled',
                'user' => 'lina',
                'gift' => 'Mug ERAH',
                'status' => GiftRedemption::STATUS_CANCELLED,
                'requested_at' => $this->now->subDays(2),
                'approved_at' => $this->now->subDays(2)->addHours(4),
                'reason' => 'Annulation demandee par le membre',
            ],
            [
                'key' => 'pending-today',
                'user' => 'member_active',
                'gift' => 'Starter Pack Stickers',
                'status' => GiftRedemption::STATUS_PENDING,
                'requested_at' => $this->now->subHours(2),
            ],
        ];

        $platformPoints = app(PlatformPointService::class);

        foreach ($redemptionScenarios as $scenario) {
            if (! isset($this->users[$scenario['user']])) {
                continue;
            }

            $user = $this->users[$scenario['user']];
            $gift = $this->findGift($scenario['gift']);
            if (! $gift) {
                continue;
            }

            $requestedAt = $scenario['requested_at'] instanceof CarbonImmutable
                ? $scenario['requested_at']
                : $this->now->subDay();
            $approvedAt = $scenario['approved_at'] ?? null;
            $rejectedAt = $scenario['rejected_at'] ?? null;
            $shippedAt = $scenario['shipped_at'] ?? null;
            $deliveredAt = $scenario['delivered_at'] ?? null;

            $redemption = $this->upsertRedemption(
                'gift-'.$scenario['key'],
                [
                    'user_id' => $user->id,
                    'gift_id' => $gift->id,
                    'cost_points_snapshot' => (int) $gift->cost_points,
                    'status' => $scenario['status'],
                    'reason' => $scenario['reason'] ?? null,
                    'tracking_code' => $scenario['tracking_code'] ?? null,
                    'tracking_carrier' => $scenario['tracking_carrier'] ?? null,
                    'shipping_note' => $scenario['shipping_note'] ?? null,
                    'requested_at' => $requestedAt,
                    'approved_at' => $approvedAt,
                    'rejected_at' => $rejectedAt,
                    'shipped_at' => $shippedAt,
                    'delivered_at' => $deliveredAt,
                    'internal_note' => 'Preview fulfilment scenario: '.$scenario['key'],
                ],
            );

            $platformPoints->debit(
                user: $user,
                amount: (int) $gift->cost_points,
                type: RewardWalletTransaction::TYPE_GIFT_PURCHASE,
                uniqueKey: 'seed.preview.gift.cost.'.self::PREVIEW_KEY.'.'.$scenario['key'],
                meta: ['redemption_id' => $redemption->id, 'scenario' => $scenario['key']],
                refType: RewardWalletTransaction::REF_TYPE_GIFT,
                refId: (string) $redemption->id,
                initialBalanceIfMissing: 0,
            );

            if (in_array($scenario['status'], [
                GiftRedemption::STATUS_REJECTED,
                GiftRedemption::STATUS_REFUNDED,
                GiftRedemption::STATUS_CANCELLED,
            ], true)) {
                $platformPoints->credit(
                    user: $user,
                    amount: (int) $gift->cost_points,
                    type: RewardWalletTransaction::TYPE_REDEEM_REFUND,
                    uniqueKey: 'seed.preview.gift.refund.'.self::PREVIEW_KEY.'.'.$scenario['key'],
                    meta: ['redemption_id' => $redemption->id, 'scenario' => $scenario['key']],
                    refType: RewardWalletTransaction::REF_TYPE_GIFT,
                    refId: (string) $redemption->id,
                    initialBalanceIfMissing: 0,
                );
            }

            $this->upsertRedemptionEvent(
                $redemption,
                'redeem_requested',
                $user,
                $requestedAt,
                ['gift_id' => $gift->id, 'cost_points' => (int) $gift->cost_points],
            );

            if ($approvedAt instanceof CarbonImmutable) {
                $this->upsertRedemptionEvent($redemption, 'admin_approved', $this->admin, $approvedAt, ['status' => 'approved']);
            }
            if ($rejectedAt instanceof CarbonImmutable) {
                $this->upsertRedemptionEvent($redemption, 'admin_rejected', $this->admin, $rejectedAt, ['reason' => $scenario['reason'] ?? null]);
            }
            if ($shippedAt instanceof CarbonImmutable) {
                $this->upsertRedemptionEvent(
                    $redemption,
                    'admin_shipped',
                    $this->admin,
                    $shippedAt,
                    [
                        'tracking_code' => $scenario['tracking_code'] ?? null,
                        'tracking_carrier' => $scenario['tracking_carrier'] ?? null,
                        'shipping_note' => $scenario['shipping_note'] ?? null,
                    ],
                );
            }
            if ($deliveredAt instanceof CarbonImmutable) {
                $this->upsertRedemptionEvent($redemption, 'admin_delivered', $this->admin, $deliveredAt, ['status' => 'delivered']);
            }
            if ($scenario['status'] === GiftRedemption::STATUS_CANCELLED) {
                $this->upsertRedemptionEvent($redemption, 'admin_cancelled', $this->admin, $requestedAt->addHours(8), ['reason' => $scenario['reason'] ?? null]);
            }
            if ($scenario['status'] === GiftRedemption::STATUS_REFUNDED) {
                $this->upsertRedemptionEvent($redemption, 'admin_refunded', $this->admin, $requestedAt->addDays(3), ['reason' => $scenario['reason'] ?? null]);
            }
        }
    }

    private function seedShopUniverse(): void
    {
        $shopService = app(ShopService::class);
        $shopService->seedDefaults();

        $extras = [
            [
                'key' => 'merch.mousepad-xl',
                'name' => 'Mousepad XL ERAH',
                'description' => 'Mousepad XL edition preview pour sessions rankees.',
                'type' => 'merch',
                'cost_points' => 950,
                'stock' => 6,
                'payload' => ['size' => 'xl', 'edition' => 'preview'],
                'is_featured' => true,
                'sort_order' => 45,
            ],
            [
                'key' => 'coaching.vod-review',
                'name' => 'VOD Review 45m',
                'description' => 'Session VOD premium avec notes personnalisees.',
                'type' => 'service',
                'cost_points' => 2100,
                'stock' => 4,
                'payload' => ['duration_min' => 45],
                'is_featured' => true,
                'sort_order' => 46,
            ],
            [
                'key' => 'token.raffle-entry',
                'name' => 'Raffle Entry Token',
                'description' => 'Ticket de participation aux tirages communautaires.',
                'type' => 'token',
                'cost_points' => 120,
                'stock' => 200,
                'payload' => ['token' => 'raffle-entry'],
                'is_featured' => false,
                'sort_order' => 47,
            ],
            [
                'key' => 'hardware.streamdeck-mini',
                'name' => 'Stream Deck Mini',
                'description' => 'Stock presque vide pour test alertes admin.',
                'type' => 'hardware',
                'cost_points' => 3900,
                'stock' => 1,
                'payload' => ['warranty_months' => 12],
                'is_featured' => true,
                'sort_order' => 48,
            ],
            [
                'key' => 'hardware.headset-zero',
                'name' => 'Headset Pro Out',
                'description' => 'Article en rupture pour cas admin.',
                'type' => 'hardware',
                'cost_points' => 2600,
                'stock' => 0,
                'payload' => ['out_of_stock' => true],
                'is_featured' => false,
                'sort_order' => 49,
            ],
        ];

        foreach ($extras as $item) {
            ShopItem::query()->updateOrCreate(
                ['key' => $item['key']],
                [
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'type' => $item['type'],
                    'cost_points' => $item['cost_points'],
                    'stock' => $item['stock'],
                    'payload' => $item['payload'],
                    'is_active' => true,
                    'is_featured' => $item['is_featured'],
                    'sort_order' => $item['sort_order'],
                ],
            );
        }

        $purchaseScenarios = [
            ['alias' => 'member_active', 'item' => 'badge.community-founder', 'key' => 'member-active-founder'],
            ['alias' => 'member_active', 'item' => 'coaching.vod-review', 'key' => 'member-active-vod'],
            ['alias' => 'member_medium', 'item' => 'avatar.red-frame', 'key' => 'member-medium-frame'],
            ['alias' => 'member_medium', 'item' => 'token.raffle-entry', 'key' => 'member-medium-raffle'],
            ['alias' => 'member_new', 'item' => 'token.raffle-entry', 'key' => 'member-new-raffle'],
            ['alias' => 'player_one', 'item' => 'boost.xp-week', 'key' => 'player-one-boost'],
            ['alias' => 'noah', 'item' => 'merch.mousepad-xl', 'key' => 'noah-mousepad'],
            ['alias' => 'lina', 'item' => 'badge.community-founder', 'key' => 'lina-founder'],
            ['alias' => 'supporter_alpha', 'item' => 'hardware.streamdeck-mini', 'key' => 'supporter-alpha-streamdeck'],
            ['alias' => 'supporter_beta', 'item' => 'avatar.red-frame', 'key' => 'supporter-beta-frame'],
            ['alias' => 'member_streamer', 'item' => 'boost.xp-week', 'key' => 'member-streamer-boost'],
        ];

        foreach ($purchaseScenarios as $scenario) {
            if (! isset($this->users[$scenario['alias']])) {
                continue;
            }

            $user = $this->users[$scenario['alias']];
            $item = $this->findShopItem($scenario['item']);
            if (! $item || ! $item->is_active) {
                continue;
            }

            try {
                $shopService->purchase(
                    $user,
                    $item,
                    'seed.preview.shop.'.self::PREVIEW_KEY.'.'.$scenario['key'],
                );
            } catch (Throwable) {
                // Ignore stock and idempotency collisions in replay mode.
            }
        }
    }

    private function seedMatchesAndBetsUniverse(): void
    {
        $openMatch = $this->upsertMatch('preview-open-1', [
            'game_key' => 'valorant',
            'event_type' => EsportMatch::EVENT_TYPE_HEAD_TO_HEAD,
            'event_name' => null,
            'competition_name' => 'Preview Open League',
            'competition_stage' => 'Regular Season',
            'competition_split' => 'Week 7',
            'best_of' => 3,
            'parent_match_id' => null,
            'team_a_name' => 'ERAH Titans',
            'team_b_name' => 'Vector Wolves',
            'home_team' => 'ERAH Titans',
            'away_team' => 'Vector Wolves',
            'starts_at' => $this->now->addHours(4),
            'locked_at' => $this->now->addHours(4)->subMinutes(5),
            'ends_at' => null,
            'status' => EsportMatch::STATUS_SCHEDULED,
            'result' => null,
            'finished_at' => null,
            'team_a_score' => null,
            'team_b_score' => null,
            'child_matches_unlocked_at' => null,
            'settled_at' => null,
            'meta' => ['seed' => self::PREVIEW_KEY],
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        $liveMatch = $this->upsertMatch('preview-live-1', [
            'game_key' => 'cs2',
            'event_type' => EsportMatch::EVENT_TYPE_HEAD_TO_HEAD,
            'event_name' => null,
            'competition_name' => 'Preview Live Cup',
            'competition_stage' => 'Quarterfinal',
            'competition_split' => 'Day 2',
            'best_of' => 3,
            'parent_match_id' => null,
            'team_a_name' => 'North Prime',
            'team_b_name' => 'Solar Echo',
            'home_team' => 'North Prime',
            'away_team' => 'Solar Echo',
            'starts_at' => $this->now->subMinutes(35),
            'locked_at' => $this->now->subMinutes(40),
            'ends_at' => null,
            'status' => EsportMatch::STATUS_LIVE,
            'result' => null,
            'finished_at' => null,
            'team_a_score' => null,
            'team_b_score' => null,
            'child_matches_unlocked_at' => null,
            'settled_at' => null,
            'meta' => ['seed' => self::PREVIEW_KEY],
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        $awaitingSettlement = $this->upsertMatch('preview-awaiting-settlement', [
            'game_key' => 'lol',
            'event_type' => EsportMatch::EVENT_TYPE_HEAD_TO_HEAD,
            'event_name' => null,
            'competition_name' => 'Preview Waiting Settlement',
            'competition_stage' => 'Semifinal',
            'competition_split' => 'Week 8',
            'best_of' => 3,
            'parent_match_id' => null,
            'team_a_name' => 'Crimson Arc',
            'team_b_name' => 'Blue Vertex',
            'home_team' => 'Crimson Arc',
            'away_team' => 'Blue Vertex',
            'starts_at' => $this->now->addHours(2),
            'locked_at' => $this->now->addHours(2)->subMinutes(5),
            'ends_at' => null,
            'status' => EsportMatch::STATUS_SCHEDULED,
            'result' => null,
            'finished_at' => null,
            'team_a_score' => null,
            'team_b_score' => null,
            'child_matches_unlocked_at' => null,
            'settled_at' => null,
            'meta' => ['seed' => self::PREVIEW_KEY],
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        $settledHome = $this->upsertMatch('preview-settled-home', [
            'game_key' => 'valorant',
            'event_type' => EsportMatch::EVENT_TYPE_HEAD_TO_HEAD,
            'event_name' => null,
            'competition_name' => 'Preview Settled Home',
            'competition_stage' => 'Final',
            'competition_split' => 'Week 6',
            'best_of' => 5,
            'parent_match_id' => null,
            'team_a_name' => 'Pulse Squad',
            'team_b_name' => 'Orbit Five',
            'home_team' => 'Pulse Squad',
            'away_team' => 'Orbit Five',
            'starts_at' => $this->now->addHours(3),
            'locked_at' => $this->now->addHours(3)->subMinutes(5),
            'ends_at' => null,
            'status' => EsportMatch::STATUS_SCHEDULED,
            'result' => null,
            'finished_at' => null,
            'team_a_score' => null,
            'team_b_score' => null,
            'child_matches_unlocked_at' => null,
            'settled_at' => null,
            'meta' => ['seed' => self::PREVIEW_KEY],
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        $settledVoid = $this->upsertMatch('preview-settled-void', [
            'game_key' => 'rocket_league',
            'event_type' => EsportMatch::EVENT_TYPE_HEAD_TO_HEAD,
            'event_name' => null,
            'competition_name' => 'Preview Settled Void',
            'competition_stage' => 'Swiss',
            'competition_split' => 'Week 5',
            'best_of' => 5,
            'parent_match_id' => null,
            'team_a_name' => 'Astra RL',
            'team_b_name' => 'Boost Orbit',
            'home_team' => 'Astra RL',
            'away_team' => 'Boost Orbit',
            'starts_at' => $this->now->addHours(5),
            'locked_at' => $this->now->addHours(5)->subMinutes(5),
            'ends_at' => null,
            'status' => EsportMatch::STATUS_SCHEDULED,
            'result' => null,
            'finished_at' => null,
            'team_a_score' => null,
            'team_b_score' => null,
            'child_matches_unlocked_at' => null,
            'settled_at' => null,
            'meta' => ['seed' => self::PREVIEW_KEY],
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        $this->upsertMatch('preview-overdue-1', [
            'game_key' => 'valorant',
            'event_type' => EsportMatch::EVENT_TYPE_HEAD_TO_HEAD,
            'event_name' => null,
            'competition_name' => 'Preview Overdue Match',
            'competition_stage' => 'Group Stage',
            'competition_split' => 'Week 9',
            'best_of' => 1,
            'parent_match_id' => null,
            'team_a_name' => 'Legacy Core',
            'team_b_name' => 'Silent Hawks',
            'home_team' => 'Legacy Core',
            'away_team' => 'Silent Hawks',
            'starts_at' => $this->now->subHours(4),
            'locked_at' => $this->now->subHours(4)->subMinutes(5),
            'ends_at' => null,
            'status' => EsportMatch::STATUS_SCHEDULED,
            'result' => null,
            'finished_at' => null,
            'team_a_score' => null,
            'team_b_score' => null,
            'child_matches_unlocked_at' => null,
            'settled_at' => null,
            'meta' => ['seed' => self::PREVIEW_KEY],
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        $tournamentRun = $this->upsertMatch('preview-rl-run-open', [
            'game_key' => EsportMatch::GAME_ROCKET_LEAGUE,
            'event_type' => EsportMatch::EVENT_TYPE_TOURNAMENT_RUN,
            'event_name' => 'Preview RLCS Run',
            'competition_name' => 'RLCS Preview',
            'competition_stage' => 'Open Qualifier',
            'competition_split' => 'Spring',
            'best_of' => null,
            'parent_match_id' => null,
            'team_a_name' => null,
            'team_b_name' => null,
            'home_team' => 'ERAH RL',
            'away_team' => 'Tournament',
            'starts_at' => $this->now->addDay(),
            'locked_at' => $this->now->addDay()->subHour(),
            'ends_at' => $this->now->addDays(2),
            'status' => EsportMatch::STATUS_SCHEDULED,
            'result' => null,
            'finished_at' => null,
            'team_a_score' => null,
            'team_b_score' => null,
            'child_matches_unlocked_at' => $this->now->subHour(),
            'settled_at' => null,
            'meta' => ['seed' => self::PREVIEW_KEY],
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        $tournamentChild = $this->upsertMatch('preview-rl-run-child-1', [
            'game_key' => EsportMatch::GAME_ROCKET_LEAGUE,
            'event_type' => EsportMatch::EVENT_TYPE_HEAD_TO_HEAD,
            'event_name' => $tournamentRun->event_name,
            'competition_name' => $tournamentRun->competition_name,
            'competition_stage' => 'Round 1',
            'competition_split' => $tournamentRun->competition_split,
            'best_of' => 5,
            'parent_match_id' => $tournamentRun->id,
            'team_a_name' => 'ERAH RL',
            'team_b_name' => 'Nebula Sparks',
            'home_team' => 'ERAH RL',
            'away_team' => 'Nebula Sparks',
            'starts_at' => $this->now->addDay()->addHours(2),
            'locked_at' => $this->now->addDay()->addHours(2)->subMinutes(5),
            'ends_at' => null,
            'status' => EsportMatch::STATUS_SCHEDULED,
            'result' => null,
            'finished_at' => null,
            'team_a_score' => null,
            'team_b_score' => null,
            'child_matches_unlocked_at' => null,
            'settled_at' => null,
            'meta' => ['seed' => self::PREVIEW_KEY],
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        $this->placeBet($this->mustHaveUser('member_active'), $openMatch, Bet::PREDICTION_HOME, 220, 'open-member-active');
        $this->placeBet($this->mustHaveUser('noah'), $openMatch, Bet::PREDICTION_AWAY, 180, 'open-noah');
        $this->placeBet($this->mustHaveUser('member_medium'), $awaitingSettlement, Bet::PREDICTION_HOME, 160, 'awaiting-member-medium');
        $this->placeBet($this->mustHaveUser('supporter_alpha'), $awaitingSettlement, Bet::PREDICTION_AWAY, 120, 'awaiting-supporter-alpha');
        $this->placeBet($this->mustHaveUser('player_one'), $settledHome, Bet::PREDICTION_HOME, 200, 'settled-home-player-one');
        $this->placeBet($this->mustHaveUser('marco'), $settledHome, Bet::PREDICTION_AWAY, 150, 'settled-home-marco');
        $this->placeBet($this->mustHaveUser('ryan'), $settledHome, Bet::PREDICTION_DRAW, 80, 'settled-home-ryan');
        $this->placeBet($this->mustHaveUser('member_new'), $settledVoid, Bet::PREDICTION_HOME, 95, 'settled-void-member-new');
        $this->placeBet($this->mustHaveUser('maya'), $settledVoid, Bet::PREDICTION_AWAY, 110, 'settled-void-maya');
        $this->placeBet($this->mustHaveUser('supporter_beta'), $tournamentChild, Bet::PREDICTION_HOME, 130, 'tournament-supporter-beta');

        try {
            app(SettleMatchBetsAction::class)->execute(
                actor: $this->admin,
                matchId: $settledHome->id,
                result: EsportMatch::RESULT_HOME,
                idempotencyKey: 'seed.preview.match.settle.'.self::PREVIEW_KEY.'.settled-home',
                teamAScore: 3,
                teamBScore: 1,
            );
        } catch (Throwable) {
            // Ignore replay collisions.
        }

        try {
            app(SettleMatchBetsAction::class)->execute(
                actor: $this->admin,
                matchId: $settledVoid->id,
                result: EsportMatch::RESULT_VOID,
                idempotencyKey: 'seed.preview.match.settle.'.self::PREVIEW_KEY.'.settled-void',
                teamAScore: null,
                teamBScore: null,
            );
        } catch (Throwable) {
            // Ignore replay collisions.
        }

        EsportMatch::query()->whereKey($awaitingSettlement->id)->update([
            'starts_at' => $this->now->subHours(2),
            'locked_at' => $this->now->subHours(2)->subMinutes(5),
            'status' => EsportMatch::STATUS_FINISHED,
            'result' => EsportMatch::RESULT_HOME,
            'finished_at' => $this->now->subHour(),
            'team_a_score' => 2,
            'team_b_score' => 1,
            'settled_at' => null,
            'updated_by' => $this->admin->id,
        ]);

        EsportMatch::query()->whereKey($liveMatch->id)->update([
            'status' => EsportMatch::STATUS_LIVE,
            'updated_by' => $this->admin->id,
        ]);
    }

    private function seedDuelsUniverse(): void
    {
        $create = app(CreateDuelAction::class);
        $accept = app(AcceptDuelAction::class);
        $refuse = app(RefuseDuelAction::class);
        $expire = app(ExpireDuelAction::class);
        $duelService = app(DuelService::class);

        $scenarios = [
            [
                'key' => 'pending-1',
                'challenger' => 'member_active',
                'challenged' => 'member_medium',
                'state' => 'pending',
                'message' => 'Preview duel pending for dashboard.',
                'expires' => 300,
            ],
            [
                'key' => 'accepted-open',
                'challenger' => 'noah',
                'challenged' => 'lina',
                'state' => 'accepted',
                'message' => 'Preview accepted duel still in progress.',
                'expires' => 360,
            ],
            [
                'key' => 'settled-1',
                'challenger' => 'supporter_alpha',
                'challenged' => 'supporter_beta',
                'state' => 'settled',
                'winner' => 'supporter_beta',
                'message' => 'Preview settled duel one.',
                'expires' => 420,
                'challenger_score' => 1,
                'challenged_score' => 3,
            ],
            [
                'key' => 'settled-2',
                'challenger' => 'member_streamer',
                'challenged' => 'member_active',
                'state' => 'settled',
                'winner' => 'member_active',
                'message' => 'Preview settled duel two.',
                'expires' => 420,
                'challenger_score' => 2,
                'challenged_score' => 3,
            ],
            [
                'key' => 'refused-1',
                'challenger' => 'marco',
                'challenged' => 'zoe',
                'state' => 'refused',
                'message' => 'Preview refused duel.',
                'expires' => 360,
            ],
            [
                'key' => 'expired-1',
                'challenger' => 'member_new',
                'challenged' => 'ryan',
                'state' => 'expired',
                'message' => 'Preview expired duel.',
                'expires' => 5,
            ],
        ];

        foreach ($scenarios as $scenario) {
            if (! isset($this->users[$scenario['challenger']], $this->users[$scenario['challenged']])) {
                continue;
            }

            $challenger = $this->users[$scenario['challenger']];
            $challenged = $this->users[$scenario['challenged']];

            try {
                $created = $create->execute(
                    challenger: $challenger,
                    challengedUserId: $challenged->id,
                    idempotencyKey: 'seed.preview.duel.'.self::PREVIEW_KEY.'.'.$scenario['key'],
                    message: $scenario['message'],
                    expiresInMinutes: (int) $scenario['expires'],
                );
            } catch (Throwable) {
                $created = [
                    'duel' => Duel::query()
                        ->where('challenger_id', $challenger->id)
                        ->where('idempotency_key', 'seed.preview.duel.'.self::PREVIEW_KEY.'.'.$scenario['key'])
                        ->first(),
                ];
            }

            $duel = $created['duel'] instanceof Duel ? $created['duel'] : null;
            if (! $duel) {
                continue;
            }

            $duel = Duel::query()->whereKey($duel->id)->firstOrFail();
            $state = (string) $scenario['state'];

            if ($state === 'accepted' || $state === 'settled') {
                if ($duel->status === Duel::STATUS_PENDING) {
                    try {
                        $accept->execute($challenged, $duel->id);
                    } catch (Throwable) {
                        // Ignore replay collisions.
                    }
                }
            }

            if ($state === 'refused') {
                if ($duel->status === Duel::STATUS_PENDING) {
                    try {
                        $refuse->execute($challenged, $duel->id);
                    } catch (Throwable) {
                        // Ignore replay collisions.
                    }
                }
            }

            if ($state === 'expired') {
                $duel = Duel::query()->whereKey($duel->id)->firstOrFail();
                if ($duel->status === Duel::STATUS_PENDING) {
                    $duel->expires_at = $this->now->subMinutes(10);
                    $duel->save();

                    try {
                        $expire->execute($duel->id);
                    } catch (Throwable) {
                        // Ignore replay collisions.
                    }
                }
            }

            if ($state === 'settled') {
                $duel = Duel::query()->whereKey($duel->id)->firstOrFail();
                $winnerAlias = (string) ($scenario['winner'] ?? '');
                if (! isset($this->users[$winnerAlias])) {
                    continue;
                }

                $winner = $this->users[$winnerAlias];

                try {
                    $duelService->recordResult(
                        actor: $this->admin,
                        duel: $duel,
                        winner: $winner,
                        challengerScore: $scenario['challenger_score'] ?? null,
                        challengedScore: $scenario['challenged_score'] ?? null,
                        note: 'Preview settled duel',
                    );
                } catch (Throwable) {
                    // Ignore replay collisions.
                }
            }
        }
    }

    private function seedQuizUniverse(): void
    {
        $quizDefinitions = [
            [
                'slug' => 'preview-strategy-quiz',
                'title' => 'Preview Strategy Quiz',
                'description' => 'Quiz tactique pour remplir la zone quiz avec plusieurs tentatives.',
                'intro' => 'Version preview: micro situations de game sense.',
                'pass_score' => 3,
                'max_attempts_per_user' => 4,
                'reward_points' => 180,
                'xp_reward' => 240,
                'is_active' => true,
                'starts_at' => $this->now->subDays(3),
                'ends_at' => $this->now->addDays(8),
                'questions' => [
                    [
                        'prompt' => 'Quel call est prioritaire en 2v2 retake ?',
                        'type' => QuizQuestion::TYPE_SINGLE_CHOICE,
                        'points' => 1,
                        'answers' => [
                            ['label' => 'Split et trade', 'correct' => true],
                            ['label' => 'Rush sans info', 'correct' => false],
                            ['label' => 'Eco fake', 'correct' => false],
                        ],
                    ],
                    [
                        'prompt' => 'Quel indicateur confirme une bonne discipline utilitaire ?',
                        'type' => QuizQuestion::TYPE_SINGLE_CHOICE,
                        'points' => 1,
                        'answers' => [
                            ['label' => 'Utilitaire conserve pour retake', 'correct' => true],
                            ['label' => 'Utilitaire lancee en spawn', 'correct' => false],
                            ['label' => 'Aucune smoke restante', 'correct' => false],
                        ],
                    ],
                    [
                        'prompt' => 'Quel mot-cle indique un tempo lent ?',
                        'type' => QuizQuestion::TYPE_SHORT_TEXT,
                        'points' => 1,
                        'accepted_answer' => 'default',
                    ],
                ],
            ],
            [
                'slug' => 'preview-community-quiz',
                'title' => 'Preview Community Quiz',
                'description' => 'Quiz orientee communaute, progression et points.',
                'intro' => 'Version preview: comprehension des modules ERAH.',
                'pass_score' => 2,
                'max_attempts_per_user' => 3,
                'reward_points' => 120,
                'xp_reward' => 160,
                'is_active' => true,
                'starts_at' => $this->now->subDays(10),
                'ends_at' => null,
                'questions' => [
                    [
                        'prompt' => 'Quel module affiche le suivi detaille des cadeaux ?',
                        'type' => QuizQuestion::TYPE_SINGLE_CHOICE,
                        'points' => 1,
                        'answers' => [
                            ['label' => 'Mes commandes cadeaux', 'correct' => true],
                            ['label' => 'Mes duels', 'correct' => false],
                            ['label' => 'Mes paris', 'correct' => false],
                        ],
                    ],
                    [
                        'prompt' => 'Les points servent a ?',
                        'type' => QuizQuestion::TYPE_SINGLE_CHOICE,
                        'points' => 1,
                        'answers' => [
                            ['label' => 'Cadeaux, paris et duels', 'correct' => true],
                            ['label' => 'Uniquement les quiz', 'correct' => false],
                            ['label' => 'Uniquement le profil', 'correct' => false],
                        ],
                    ],
                ],
            ],
            [
                'slug' => 'preview-archived-quiz',
                'title' => 'Preview Archived Quiz',
                'description' => 'Quiz cloture pour afficher un historique.',
                'intro' => 'Version preview archivee.',
                'pass_score' => 1,
                'max_attempts_per_user' => 2,
                'reward_points' => 80,
                'xp_reward' => 110,
                'is_active' => false,
                'starts_at' => $this->now->subMonths(2),
                'ends_at' => $this->now->subMonths(1)->subDays(20),
                'questions' => [
                    [
                        'prompt' => 'Quel statut cloture une commande cadeau ?',
                        'type' => QuizQuestion::TYPE_SINGLE_CHOICE,
                        'points' => 1,
                        'answers' => [
                            ['label' => 'delivered', 'correct' => true],
                            ['label' => 'pending', 'correct' => false],
                            ['label' => 'approved', 'correct' => false],
                        ],
                    ],
                ],
            ],
        ];

        $quizzes = [];
        foreach ($quizDefinitions as $definition) {
            $quiz = Quiz::query()->updateOrCreate(
                ['slug' => $definition['slug']],
                [
                    'title' => $definition['title'],
                    'description' => $definition['description'],
                    'intro' => $definition['intro'],
                    'pass_score' => (int) $definition['pass_score'],
                    'max_attempts_per_user' => $definition['max_attempts_per_user'],
                    'reward_points' => (int) $definition['reward_points'],
                    'xp_reward' => (int) $definition['xp_reward'],
                    'is_active' => (bool) $definition['is_active'],
                    'starts_at' => $definition['starts_at'],
                    'ends_at' => $definition['ends_at'],
                    'mission_template_id' => null,
                    'created_by' => $this->admin->id,
                    'updated_by' => $this->admin->id,
                ],
            );

            $quiz->questions()->delete();
            foreach ($definition['questions'] as $questionIndex => $questionDefinition) {
                $question = QuizQuestion::query()->create([
                    'quiz_id' => $quiz->id,
                    'prompt' => $questionDefinition['prompt'],
                    'question_type' => $questionDefinition['type'],
                    'explanation' => null,
                    'accepted_answer' => $questionDefinition['accepted_answer'] ?? null,
                    'sort_order' => $questionIndex + 1,
                    'points' => (int) $questionDefinition['points'],
                    'is_active' => true,
                ]);

                foreach ($questionDefinition['answers'] ?? [] as $answerIndex => $answerDefinition) {
                    QuizAnswer::query()->create([
                        'question_id' => $question->id,
                        'label' => $answerDefinition['label'],
                        'is_correct' => (bool) $answerDefinition['correct'],
                        'sort_order' => $answerIndex + 1,
                    ]);
                }
            }

            $quizzes[$definition['slug']] = $quiz->fresh(['questions.answers']);
        }

        $quizService = app(\App\Services\QuizService::class);

        $attemptPlans = [
            ['alias' => 'member_active', 'quiz' => 'preview-strategy-quiz', 'attempts' => 2, 'passes' => 1],
            ['alias' => 'member_medium', 'quiz' => 'preview-strategy-quiz', 'attempts' => 1, 'passes' => 1],
            ['alias' => 'member_new', 'quiz' => 'preview-community-quiz', 'attempts' => 1, 'passes' => 0],
            ['alias' => 'supporter_alpha', 'quiz' => 'preview-community-quiz', 'attempts' => 1, 'passes' => 1],
            ['alias' => 'supporter_beta', 'quiz' => 'preview-community-quiz', 'attempts' => 2, 'passes' => 2],
        ];

        foreach ($attemptPlans as $plan) {
            if (! isset($this->users[$plan['alias']], $quizzes[$plan['quiz']])) {
                continue;
            }

            $user = $this->users[$plan['alias']];
            $quiz = $quizzes[$plan['quiz']];
            $existingAttempts = QuizAttempt::query()
                ->where('quiz_id', $quiz->id)
                ->where('user_id', $user->id)
                ->count();

            for ($i = $existingAttempts; $i < (int) $plan['attempts']; $i++) {
                $shouldPass = $i < (int) $plan['passes'];
                $answers = $this->buildQuizAnswersPayload($quiz, $shouldPass);

                try {
                    $quizService->attempt($user, $quiz, $answers);
                } catch (Throwable) {
                    break;
                }
            }
        }

        $archivedQuiz = $quizzes['preview-archived-quiz'] ?? null;
        if ($archivedQuiz && isset($this->users['player_one'])) {
            QuizAttempt::query()->updateOrCreate(
                [
                    'quiz_id' => $archivedQuiz->id,
                    'user_id' => $this->users['player_one']->id,
                    'started_at' => CarbonImmutable::parse('2026-02-03 20:05:00'),
                ],
                [
                    'score' => 1,
                    'max_score' => 1,
                    'passed' => true,
                    'answers' => ['archived' => 'preview'],
                    'finished_at' => CarbonImmutable::parse('2026-02-03 20:07:00'),
                    'reward_granted_at' => CarbonImmutable::parse('2026-02-03 20:07:00'),
                ],
            );
        }
    }

    private function seedLiveCodeUniverse(): void
    {
        $codes = [
            [
                'code' => 'ERAHLIVE',
                'label' => 'ERAH Live Welcome',
                'description' => 'Code live principal de demonstration.',
                'status' => 'published',
                'reward_points' => 75,
                'bet_points' => 25,
                'xp_reward' => 50,
                'usage_limit' => 500,
                'per_user_limit' => 1,
                'expires_at' => $this->now->addMonths(2),
            ],
            [
                'code' => 'PREVIEWBOOST',
                'label' => 'Preview Boost',
                'description' => 'Code actif pour enrichir les redemptions live.',
                'status' => 'published',
                'reward_points' => 140,
                'bet_points' => 40,
                'xp_reward' => 100,
                'usage_limit' => 120,
                'per_user_limit' => 1,
                'expires_at' => $this->now->addDays(10),
            ],
            [
                'code' => 'PREVIEWVIP',
                'label' => 'Preview VIP',
                'description' => 'Code actif avec limite stricte.',
                'status' => 'published',
                'reward_points' => 220,
                'bet_points' => 60,
                'xp_reward' => 140,
                'usage_limit' => 8,
                'per_user_limit' => 1,
                'expires_at' => $this->now->addDay(),
            ],
            [
                'code' => 'PREVIEWEXPIRED',
                'label' => 'Preview Expired',
                'description' => 'Code expire visible dans l historique.',
                'status' => 'published',
                'reward_points' => 90,
                'bet_points' => 20,
                'xp_reward' => 70,
                'usage_limit' => 30,
                'per_user_limit' => 1,
                'expires_at' => $this->now->subDays(3),
            ],
            [
                'code' => 'PREVIEWDRAFT',
                'label' => 'Preview Draft',
                'description' => 'Code brouillon pour zone admin.',
                'status' => 'draft',
                'reward_points' => 40,
                'bet_points' => 0,
                'xp_reward' => 30,
                'usage_limit' => null,
                'per_user_limit' => 1,
                'expires_at' => $this->now->addMonths(1),
            ],
        ];

        foreach ($codes as $definition) {
            LiveCode::query()->updateOrCreate(
                ['code' => $definition['code']],
                [
                    'label' => $definition['label'],
                    'description' => $definition['description'],
                    'status' => $definition['status'],
                    'reward_points' => $definition['reward_points'],
                    'bet_points' => $definition['bet_points'],
                    'xp_reward' => $definition['xp_reward'],
                    'usage_limit' => $definition['usage_limit'],
                    'per_user_limit' => $definition['per_user_limit'],
                    'expires_at' => $definition['expires_at'],
                    'mission_template_id' => null,
                    'created_by' => $this->admin->id,
                    'meta' => ['seed' => self::PREVIEW_KEY],
                ],
            );
        }

        $liveCodeService = app(LiveCodeService::class);
        $redeemScenarios = [
            ['alias' => 'member_active', 'code' => 'ERAHLIVE'],
            ['alias' => 'member_medium', 'code' => 'PREVIEWBOOST'],
            ['alias' => 'supporter_alpha', 'code' => 'PREVIEWBOOST'],
            ['alias' => 'supporter_beta', 'code' => 'PREVIEWVIP'],
        ];

        foreach ($redeemScenarios as $scenario) {
            if (! isset($this->users[$scenario['alias']])) {
                continue;
            }

            $user = $this->users[$scenario['alias']];
            $code = LiveCode::query()->where('code', $scenario['code'])->first();
            if (! $code) {
                continue;
            }

            $alreadyRedeemed = LiveCodeRedemption::query()
                ->where('live_code_id', $code->id)
                ->where('user_id', $user->id)
                ->exists();

            if ($alreadyRedeemed) {
                continue;
            }

            try {
                $liveCodeService->redeem($user, $scenario['code']);
            } catch (Throwable) {
                // Ignore replay and limit collisions.
            }
        }

        $expiredCode = LiveCode::query()->where('code', 'PREVIEWEXPIRED')->first();
        $expiredUser = $this->users['player_one'] ?? null;
        if ($expiredCode && $expiredUser) {
            LiveCodeRedemption::query()->updateOrCreate(
                ['live_code_id' => $expiredCode->id, 'user_id' => $expiredUser->id],
                [
                    'reward_points' => (int) $expiredCode->reward_points,
                    'bet_points' => (int) $expiredCode->bet_points,
                    'xp_reward' => (int) $expiredCode->xp_reward,
                    'meta' => ['seed' => self::PREVIEW_KEY, 'archived' => true],
                    'redeemed_at' => $this->now->subDays(7),
                ],
            );
        }
    }

    private function seedAchievements(): void
    {
        $achievementService = app(AchievementService::class);
        $achievementService->seedDefaults();

        foreach ($this->users as $user) {
            if ($user->role !== User::ROLE_USER) {
                continue;
            }

            try {
                $achievementService->sync($user);
            } catch (Throwable) {
                // Keep seeding resilient when replayed on partially customized data.
            }
        }
    }

    private function seedReviewsAndGallery(): void
    {
        app(GalleryPhotoImportService::class)->importIfEmpty();

        $manualGallery = [
            ['key' => 'preview-gallery-1', 'title' => 'Preview LAN Stage', 'media_type' => GalleryPhoto::MEDIA_TYPE_IMAGE, 'image_path' => 'https://picsum.photos/seed/preview-gallery-1/1400/900', 'filter_key' => 'evenements'],
            ['key' => 'preview-gallery-2', 'title' => 'Preview Team Warmup', 'media_type' => GalleryPhoto::MEDIA_TYPE_IMAGE, 'image_path' => 'https://picsum.photos/seed/preview-gallery-2/1400/900', 'filter_key' => 'competitions'],
            ['key' => 'preview-gallery-3', 'title' => 'Preview Studio Clip', 'media_type' => GalleryPhoto::MEDIA_TYPE_IMAGE, 'image_path' => 'https://picsum.photos/seed/preview-gallery-3/1400/900', 'filter_key' => 'valorant'],
            ['key' => 'preview-gallery-4', 'title' => 'Preview Rocket League Day', 'media_type' => GalleryPhoto::MEDIA_TYPE_IMAGE, 'image_path' => 'https://picsum.photos/seed/preview-gallery-4/1400/900', 'filter_key' => 'competitions'],
            ['key' => 'preview-gallery-5', 'title' => 'Preview Community Meet', 'media_type' => GalleryPhoto::MEDIA_TYPE_IMAGE, 'image_path' => 'https://picsum.photos/seed/preview-gallery-5/1400/900', 'filter_key' => 'evenements'],
            ['key' => 'preview-gallery-6', 'title' => 'Preview Story Reel', 'media_type' => GalleryPhoto::MEDIA_TYPE_VIDEO, 'video_path' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerJoyrides.mp4', 'filter_key' => 'evenements'],
        ];

        foreach ($manualGallery as $index => $item) {
            $hash = sha1(self::PREVIEW_KEY.'.gallery.'.$item['key']);

            GalleryPhoto::query()->updateOrCreate(
                ['imported_hash' => $hash],
                [
                    'title' => $item['title'],
                    'description' => 'Entree galerie preview '.($index + 1),
                    'image_path' => $item['image_path'] ?? null,
                    'video_path' => $item['video_path'] ?? null,
                    'media_type' => $item['media_type'],
                    'alt_text' => $item['title'],
                    'filter_key' => $item['filter_key'],
                    'filter_label' => Str::headline((string) $item['filter_key']),
                    'category_label' => Str::headline((string) $item['filter_key']),
                    'cursor_label' => 'Voir',
                    'sort_order' => 300 + $index,
                    'is_active' => true,
                    'published_at' => $this->now->subDays(10 - $index),
                    'storage_disk' => null,
                    'media_mime_type' => $item['media_type'] === GalleryPhoto::MEDIA_TYPE_VIDEO ? 'video/mp4' : 'image/jpeg',
                    'media_size' => null,
                    'legacy_source' => 'preview-seeder',
                    'created_by' => $this->admin->id,
                    'updated_by' => $this->admin->id,
                ],
            );
        }

        $reviewScenarios = [
            ['alias' => 'member_active', 'status' => ClubReview::STATUS_PUBLISHED, 'featured' => true, 'content' => 'Workflow mission + cadeaux tres lisible, bon rythme global.'],
            ['alias' => 'member_medium', 'status' => ClubReview::STATUS_PUBLISHED, 'featured' => false, 'content' => 'Le suivi de commandes cadeaux est clair et rassurant.'],
            ['alias' => 'member_new', 'status' => ClubReview::STATUS_DRAFT, 'featured' => false, 'content' => 'Je decouvre encore les modules, mais la navigation reste simple.'],
            ['alias' => 'supporter_alpha', 'status' => ClubReview::STATUS_PUBLISHED, 'featured' => true, 'content' => 'Le programme supporter est bien integre aux missions et clips.'],
            ['alias' => 'supporter_beta', 'status' => ClubReview::STATUS_HIDDEN, 'featured' => false, 'content' => 'Version brouillon pour moderation admin.'],
            ['alias' => 'member_streamer', 'status' => ClubReview::STATUS_PUBLISHED, 'featured' => false, 'content' => 'Bonne visibilite des clips et interactions communautaires.'],
            ['alias' => 'player_one', 'status' => ClubReview::STATUS_PUBLISHED, 'featured' => false, 'content' => 'Le dashboard est rempli et utile pour tester chaque parcours.'],
            ['alias' => 'lina', 'status' => ClubReview::STATUS_DRAFT, 'featured' => false, 'content' => 'Avis en attente de publication pour test moderation.'],
        ];

        foreach ($reviewScenarios as $index => $scenario) {
            if (! isset($this->users[$scenario['alias']])) {
                continue;
            }

            $user = $this->users[$scenario['alias']];
            $status = $scenario['status'];

            ClubReview::query()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'author_name' => $user->name,
                    'author_profile_url' => null,
                    'content' => $scenario['content'],
                    'status' => $status,
                    'is_featured' => (bool) $scenario['featured'],
                    'source' => ClubReview::SOURCE_MEMBER,
                    'display_order' => 200 + $index,
                    'published_at' => $status === ClubReview::STATUS_PUBLISHED ? $this->now->subDays(2 + $index) : null,
                ],
            );
        }
    }

    private function seedPlatformEventsUniverse(): void
    {
        $events = [
            [
                'key' => 'preview-missions-boost',
                'title' => 'Preview Missions Boost',
                'description' => 'Bonus temporaire sur certaines missions communautaires.',
                'type' => 'bonus_missions',
                'status' => 'published',
                'is_active' => true,
                'starts_at' => $this->now->subDay(),
                'ends_at' => $this->now->addDays(5),
                'config' => ['xp_multiplier' => 1.2, 'reward_points_multiplier' => 1.15],
            ],
            [
                'key' => 'preview-shop-drop',
                'title' => 'Preview Shop Drop',
                'description' => 'Mise en avant des articles shop limit es.',
                'type' => 'shop_drop',
                'status' => 'published',
                'is_active' => true,
                'starts_at' => $this->now->subHours(4),
                'ends_at' => $this->now->addDays(2),
                'config' => ['featured_keys' => ['hardware.streamdeck-mini', 'coaching.vod-review']],
            ],
            [
                'key' => 'preview-community-cup',
                'title' => 'Preview Community Cup',
                'description' => 'Evenement a venir pour remplir les pages publiques.',
                'type' => 'tournament',
                'status' => 'draft',
                'is_active' => true,
                'starts_at' => $this->now->addDays(4),
                'ends_at' => $this->now->addDays(6),
                'config' => ['format' => 'bo3', 'bracket_size' => 16],
            ],
            [
                'key' => 'preview-ended-campaign',
                'title' => 'Preview Ended Campaign',
                'description' => 'Campagne terminee pour historique.',
                'type' => 'campaign',
                'status' => 'published',
                'is_active' => false,
                'starts_at' => $this->now->subDays(20),
                'ends_at' => $this->now->subDays(8),
                'config' => ['result' => 'completed'],
            ],
        ];

        foreach ($events as $event) {
            PlatformEvent::query()->updateOrCreate(
                ['key' => $event['key']],
                [
                    'title' => $event['title'],
                    'description' => $event['description'],
                    'type' => $event['type'],
                    'status' => $event['status'],
                    'is_active' => $event['is_active'],
                    'starts_at' => $event['starts_at'],
                    'ends_at' => $event['ends_at'],
                    'config' => $event['config'] + ['seed' => self::PREVIEW_KEY],
                ],
            );
        }
    }

    private function seedActivityFeed(): void
    {
        $clip = Clip::query()->where('slug', 'preview-ace-night')->first()
            ?: Clip::query()->where('is_published', true)->orderByDesc('id')->first();
        $gift = $this->findGift('Hoodie ERAH') ?: Gift::query()->orderBy('id')->first();
        $openBet = Bet::query()
            ->where('idempotency_key', 'like', 'seed.preview.bet.'.self::PREVIEW_KEY.'.open-%')
            ->orderBy('id')
            ->first();
        $wonBet = Bet::query()
            ->where('idempotency_key', 'like', 'seed.preview.bet.'.self::PREVIEW_KEY.'.%')
            ->where('status', Bet::STATUS_WON)
            ->orderByDesc('id')
            ->first();
        $duelPending = Duel::query()
            ->where('idempotency_key', 'seed.preview.duel.'.self::PREVIEW_KEY.'.pending-1')
            ->first();
        $duelAccepted = Duel::query()
            ->where('idempotency_key', 'seed.preview.duel.'.self::PREVIEW_KEY.'.accepted-open')
            ->first();

        $events = [
            [
                'alias' => 'member_active',
                'key' => 'login',
                'type' => ActivityEvent::TYPE_LOGIN_DAILY,
                'ref_type' => 'user',
                'ref_id' => (string) $this->mustHaveUser('member_active')->id,
                'at' => $this->now->subMinutes(5),
            ],
            [
                'alias' => 'member_active',
                'key' => 'clip-like',
                'type' => ActivityEvent::TYPE_CLIP_LIKE,
                'ref_type' => 'clip',
                'ref_id' => (string) ($clip?->id ?? 0),
                'at' => $this->now->subMinutes(10),
            ],
            [
                'alias' => 'member_medium',
                'key' => 'clip-comment',
                'type' => ActivityEvent::TYPE_CLIP_COMMENT,
                'ref_type' => 'clip',
                'ref_id' => (string) ($clip?->id ?? 0),
                'at' => $this->now->subMinutes(14),
            ],
            [
                'alias' => 'supporter_alpha',
                'key' => 'clip-favorite',
                'type' => ActivityEvent::TYPE_CLIP_FAVORITE,
                'ref_type' => 'clip',
                'ref_id' => (string) ($clip?->id ?? 0),
                'at' => $this->now->subMinutes(20),
            ],
            [
                'alias' => 'supporter_beta',
                'key' => 'clip-share',
                'type' => ActivityEvent::TYPE_CLIP_SHARE,
                'ref_type' => 'clip',
                'ref_id' => (string) ($clip?->id ?? 0),
                'at' => $this->now->subMinutes(22),
            ],
            [
                'alias' => 'member_active',
                'key' => 'bet-placed',
                'type' => ActivityEvent::TYPE_BET_PLACED,
                'ref_type' => 'bet',
                'ref_id' => (string) ($openBet?->id ?? 0),
                'at' => $this->now->subMinutes(30),
            ],
            [
                'alias' => 'player_one',
                'key' => 'bet-won',
                'type' => ActivityEvent::TYPE_BET_WON,
                'ref_type' => 'bet',
                'ref_id' => (string) ($wonBet?->id ?? 0),
                'at' => $this->now->subMinutes(35),
            ],
            [
                'alias' => 'member_active',
                'key' => 'duel-sent',
                'type' => ActivityEvent::TYPE_DUEL_SENT,
                'ref_type' => 'duel',
                'ref_id' => (string) ($duelPending?->id ?? 0),
                'at' => $this->now->subMinutes(45),
            ],
            [
                'alias' => 'lina',
                'key' => 'duel-accepted',
                'type' => ActivityEvent::TYPE_DUEL_ACCEPTED,
                'ref_type' => 'duel',
                'ref_id' => (string) ($duelAccepted?->id ?? 0),
                'at' => $this->now->subMinutes(49),
            ],
            [
                'alias' => 'member_medium',
                'key' => 'gift-cart-add',
                'type' => ActivityEvent::TYPE_GIFT_CART_ADD,
                'ref_type' => 'gift',
                'ref_id' => (string) ($gift?->id ?? 0),
                'at' => $this->now->subMinutes(54),
            ],
            [
                'alias' => 'member_medium',
                'key' => 'gift-cart-update',
                'type' => ActivityEvent::TYPE_GIFT_CART_UPDATE,
                'ref_type' => 'gift',
                'ref_id' => (string) ($gift?->id ?? 0),
                'at' => $this->now->subMinutes(57),
            ],
            [
                'alias' => 'member_medium',
                'key' => 'gift-cart-remove',
                'type' => ActivityEvent::TYPE_GIFT_CART_REMOVE,
                'ref_type' => 'gift',
                'ref_id' => (string) ($gift?->id ?? 0),
                'at' => $this->now->subMinutes(60),
            ],
            [
                'alias' => 'member_active',
                'key' => 'gift-cart-checkout',
                'type' => ActivityEvent::TYPE_GIFT_CART_CHECKOUT,
                'ref_type' => 'gift_cart',
                'ref_id' => (string) ($gift?->id ?? 0),
                'at' => $this->now->subMinutes(65),
            ],
            [
                'alias' => 'supporter_alpha',
                'key' => 'gift-favorite-add',
                'type' => ActivityEvent::TYPE_GIFT_FAVORITE_ADD,
                'ref_type' => 'gift',
                'ref_id' => (string) ($gift?->id ?? 0),
                'at' => $this->now->subMinutes(72),
            ],
            [
                'alias' => 'supporter_beta',
                'key' => 'gift-favorite-remove',
                'type' => ActivityEvent::TYPE_GIFT_FAVORITE_REMOVE,
                'ref_type' => 'gift',
                'ref_id' => (string) ($gift?->id ?? 0),
                'at' => $this->now->subMinutes(79),
            ],
        ];

        foreach ($events as $event) {
            if (! isset($this->users[$event['alias']])) {
                continue;
            }

            $this->upsertActivityEvent(
                user: $this->users[$event['alias']],
                uniqueKey: self::PREVIEW_KEY.'.activity.'.$event['key'],
                eventType: $event['type'],
                refType: $event['ref_type'],
                refId: $event['ref_id'],
                occurredAt: $event['at'],
            );
        }
    }

    private function seedAuditFeed(): void
    {
        $previewRedemptions = GiftRedemption::query()
            ->where('internal_note', 'like', '%[preview:gift-%')
            ->with('user')
            ->get();

        foreach ($previewRedemptions as $redemption) {
            $actor = $redemption->user;
            $requestedAt = $redemption->requested_at instanceof CarbonImmutable ? $redemption->requested_at : CarbonImmutable::instance($redemption->requested_at ?: now());

            $this->upsertAuditLog(
                action: 'gift.redeem',
                actor: $actor,
                target: $redemption,
                createdAt: $requestedAt,
                context: ['redemption_id' => $redemption->id, 'gift_id' => $redemption->gift_id],
            );

            if ($redemption->approved_at) {
                $this->upsertAuditLog(
                    action: 'gift.redeem.approve',
                    actor: $this->admin,
                    target: $redemption,
                    createdAt: CarbonImmutable::instance($redemption->approved_at),
                    context: ['redemption_id' => $redemption->id, 'gift_id' => $redemption->gift_id],
                );
            }

            if ($redemption->shipped_at) {
                $this->upsertAuditLog(
                    action: 'gift.redeem.ship',
                    actor: $this->admin,
                    target: $redemption,
                    createdAt: CarbonImmutable::instance($redemption->shipped_at),
                    context: [
                        'redemption_id' => $redemption->id,
                        'gift_id' => $redemption->gift_id,
                        'tracking_code' => $redemption->tracking_code,
                    ],
                );
            }

            if ($redemption->delivered_at) {
                $this->upsertAuditLog(
                    action: 'gift.redeem.deliver',
                    actor: $this->admin,
                    target: $redemption,
                    createdAt: CarbonImmutable::instance($redemption->delivered_at),
                    context: ['redemption_id' => $redemption->id, 'gift_id' => $redemption->gift_id],
                );
            }

            if (in_array((string) $redemption->status, [
                GiftRedemption::STATUS_REJECTED,
                GiftRedemption::STATUS_CANCELLED,
                GiftRedemption::STATUS_REFUNDED,
            ], true)) {
                $rejectAt = $redemption->rejected_at
                    ? CarbonImmutable::instance($redemption->rejected_at)
                    : ($redemption->updated_at ? CarbonImmutable::instance($redemption->updated_at) : $this->now->subHours(2));

                $this->upsertAuditLog(
                    action: 'gift.redeem.reject',
                    actor: $this->admin,
                    target: $redemption,
                    createdAt: $rejectAt,
                    context: ['redemption_id' => $redemption->id, 'gift_id' => $redemption->gift_id, 'reason' => $redemption->reason],
                );
            }
        }

        $purchases = UserPurchase::query()
            ->where('idempotency_key', 'like', 'seed.preview.shop.'.self::PREVIEW_KEY.'.%')
            ->with('user')
            ->orderBy('id')
            ->get();

        foreach ($purchases as $purchase) {
            $this->upsertAuditLog(
                action: 'shop.purchase.completed',
                actor: $purchase->user,
                target: $purchase,
                createdAt: CarbonImmutable::instance($purchase->purchased_at ?: $purchase->created_at ?: now()),
                context: ['purchase_id' => $purchase->id, 'shop_item_id' => $purchase->shop_item_id],
            );
        }

        $bets = Bet::query()
            ->where('idempotency_key', 'like', 'seed.preview.bet.'.self::PREVIEW_KEY.'.%')
            ->with('user')
            ->orderBy('id')
            ->get();

        foreach ($bets as $bet) {
            $this->upsertAuditLog(
                action: 'bets.placed',
                actor: $bet->user,
                target: $bet,
                createdAt: CarbonImmutable::instance($bet->placed_at ?: $bet->created_at ?: now()),
                context: ['bet_id' => $bet->id, 'match_id' => $bet->match_id],
            );
        }

        $settledMatches = EsportMatch::query()
            ->where('match_key', 'like', 'preview-%')
            ->whereNotNull('settled_at')
            ->orderBy('id')
            ->get();

        foreach ($settledMatches as $match) {
            $this->upsertAuditLog(
                action: 'matches.settled',
                actor: $this->admin,
                target: $match,
                createdAt: CarbonImmutable::instance($match->settled_at ?: now()),
                context: ['match_id' => $match->id, 'match_key' => $match->match_key, 'result' => $match->result],
            );
        }

        $duels = Duel::query()
            ->where('idempotency_key', 'like', 'seed.preview.duel.'.self::PREVIEW_KEY.'.%')
            ->with(['challenger', 'challenged', 'result'])
            ->get();

        foreach ($duels as $duel) {
            $this->upsertAuditLog(
                action: 'duels.created',
                actor: $duel->challenger,
                target: $duel,
                createdAt: CarbonImmutable::instance($duel->requested_at ?: $duel->created_at ?: now()),
                context: ['duel_id' => $duel->id, 'challenger_id' => $duel->challenger_id, 'challenged_id' => $duel->challenged_id],
            );

            if ($duel->accepted_at) {
                $this->upsertAuditLog(
                    action: 'duels.accepted',
                    actor: $duel->challenged,
                    target: $duel,
                    createdAt: CarbonImmutable::instance($duel->accepted_at),
                    context: ['duel_id' => $duel->id],
                );
            }

            if ($duel->result && $duel->result->settled_at) {
                $this->upsertAuditLog(
                    action: 'duels.result.recorded',
                    actor: $this->admin,
                    target: $duel,
                    createdAt: CarbonImmutable::instance($duel->result->settled_at),
                    context: ['duel_id' => $duel->id, 'winner_user_id' => $duel->result->winner_user_id],
                );
            }
        }

        $clipPublished = Clip::query()->where('slug', 'preview-ace-night')->first();
        $clipDraft = Clip::query()->where('slug', 'preview-draft-content-1')->first();
        if ($clipPublished && $clipPublished->published_at) {
            $this->upsertAuditLog(
                action: 'clips.published',
                actor: $this->admin,
                target: $clipPublished,
                createdAt: CarbonImmutable::instance($clipPublished->published_at),
                context: ['clip_id' => $clipPublished->id, 'slug' => $clipPublished->slug],
            );
        }
        if ($clipDraft) {
            $this->upsertAuditLog(
                action: 'clips.unpublished',
                actor: $this->admin,
                target: $clipDraft,
                createdAt: $this->now->subHours(9),
                context: ['clip_id' => $clipDraft->id, 'slug' => $clipDraft->slug],
            );
        }

        $reviews = ClubReview::query()->whereIn('source', [ClubReview::SOURCE_MEMBER])->get();
        foreach ($reviews as $review) {
            $this->upsertAuditLog(
                action: 'reviews.created',
                actor: $review->user,
                target: $review,
                createdAt: CarbonImmutable::instance($review->created_at ?: now()),
                context: ['review_id' => $review->id, 'status' => $review->status],
            );

            if ($review->status !== ClubReview::STATUS_PUBLISHED) {
                $this->upsertAuditLog(
                    action: 'reviews.moderated',
                    actor: $this->admin,
                    target: $review,
                    createdAt: CarbonImmutable::instance($review->updated_at ?: now()),
                    context: ['review_id' => $review->id, 'status' => $review->status],
                );
            }
        }

        $liveRedemptions = LiveCodeRedemption::query()
            ->with(['user', 'liveCode'])
            ->orderBy('id')
            ->get();
        foreach ($liveRedemptions as $redemption) {
            if (! $redemption->liveCode) {
                continue;
            }

            $this->upsertAuditLog(
                action: 'live-codes.redeemed',
                actor: $redemption->user,
                target: $redemption->liveCode,
                createdAt: CarbonImmutable::instance($redemption->redeemed_at ?: $redemption->created_at ?: now()),
                context: ['live_code_id' => $redemption->live_code_id, 'redemption_id' => $redemption->id],
            );
        }

        $dailyTemplate = MissionTemplate::query()->where('key', 'launch.daily-login')->first();
        if ($dailyTemplate) {
            $this->upsertAuditLog(
                action: 'missions.template.updated',
                actor: $this->admin,
                target: $dailyTemplate,
                createdAt: $this->now->subHours(3),
                context: ['mission_template_id' => $dailyTemplate->id],
            );
        }

        $this->upsertAuditLog(
            action: 'missions.generation.daily',
            actor: $this->admin,
            target: null,
            createdAt: $this->now->subHours(2),
            context: ['generated_for' => $this->now->toDateString(), 'count' => UserMission::query()->count()],
        );
        $this->upsertAuditLog(
            action: 'missions.generation.weekly',
            actor: $this->admin,
            target: null,
            createdAt: $this->now->subHours(2)->subMinutes(10),
            context: ['generated_for' => $this->now->startOfWeek()->toDateString()],
        );
        $this->upsertAuditLog(
            action: 'missions.generation.event_window',
            actor: $this->admin,
            target: null,
            createdAt: $this->now->subHours(2)->subMinutes(20),
            context: ['active_event_templates' => MissionTemplate::query()->where('scope', MissionTemplate::SCOPE_EVENT_WINDOW)->count()],
        );
        $this->upsertAuditLog(
            action: 'missions.repair.run',
            actor: $this->admin,
            target: null,
            createdAt: $this->now->subHour(),
            context: ['status' => 'ok'],
        );
    }

    /**
     * @return array<int|string, mixed>
     */
    private function buildQuizAnswersPayload(Quiz $quiz, bool $pass): array
    {
        $answers = [];
        $questions = $quiz->questions;

        foreach ($questions as $index => $question) {
            if ($question->question_type === QuizQuestion::TYPE_SHORT_TEXT) {
                $answers[$question->id] = $pass ? (string) $question->accepted_answer : 'incorrect';
                continue;
            }

            $correct = $question->answers->firstWhere('is_correct', true);
            $wrong = $question->answers->firstWhere('is_correct', false);

            if ($pass) {
                $answers[$question->id] = $correct?->id;
                continue;
            }

            $answers[$question->id] = $index === 0 ? ($wrong?->id ?? $correct?->id) : ($correct?->id);
        }

        return $answers;
    }

    private function mustHaveUser(string $alias): User
    {
        if (! isset($this->users[$alias])) {
            throw new RuntimeException('Utilisateur alias introuvable: '.$alias);
        }

        return $this->users[$alias];
    }

    private function findGift(string $title): ?Gift
    {
        return Gift::query()->where('title', $title)->first();
    }

    private function findShopItem(string $key): ?ShopItem
    {
        return ShopItem::query()->where('key', $key)->first();
    }

    private function upsertMatch(string $matchKey, array $attributes): EsportMatch
    {
        return EsportMatch::query()->updateOrCreate(['match_key' => $matchKey], $attributes)->fresh();
    }

    private function upsertRedemption(string $previewKey, array $attributes): GiftRedemption
    {
        $marker = '[preview:'.$previewKey.']';
        $redemption = GiftRedemption::query()
            ->where('internal_note', 'like', '%'.$marker.'%')
            ->first();

        if (! $redemption) {
            $redemption = new GiftRedemption();
        }

        $redemption->fill($attributes);
        $existingNote = trim((string) ($attributes['internal_note'] ?? ''));
        $redemption->internal_note = trim($marker.' '.$existingNote);
        $redemption->save();

        return $redemption->fresh();
    }

    private function upsertRedemptionEvent(
        GiftRedemption $redemption,
        string $type,
        ?User $actor,
        CarbonImmutable $at,
        array $data = []
    ): void {
        $event = GiftRedemptionEvent::query()->firstOrNew([
            'redemption_id' => $redemption->id,
            'type' => $type,
            'created_at' => $at,
        ]);

        $event->actor_user_id = $actor?->id;
        $event->data = $data + ['seed' => self::PREVIEW_KEY];
        $event->created_at = $at;
        $event->save();
    }

    private function upsertActivityEvent(
        User $user,
        string $uniqueKey,
        string $eventType,
        string $refType,
        string $refId,
        CarbonImmutable $occurredAt,
        array $metadata = []
    ): void {
        ActivityEvent::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'unique_key' => $uniqueKey,
            ],
            [
                'event_type' => $eventType,
                'ref_type' => $refType,
                'ref_id' => $refId,
                'occurred_at' => $occurredAt,
                'metadata' => $metadata + ['seed' => self::PREVIEW_KEY],
                'created_at' => $occurredAt,
            ],
        );
    }

    private function upsertAuditLog(
        string $action,
        ?Model $actor,
        ?Model $target,
        CarbonImmutable $createdAt,
        array $context = []
    ): void {
        $actorClass = $actor ? $actor::class : null;
        $targetClass = $target ? $target::class : null;

        $existing = AuditLog::query()
            ->where('action', $action)
            ->where('actor_type', $actorClass)
            ->where('actor_id', $actor?->getKey())
            ->where('target_type', $targetClass)
            ->where('target_id', $target?->getKey())
            ->where('created_at', $createdAt)
            ->first();

        if ($existing) {
            $existing->context = $context + ['seed' => self::PREVIEW_KEY];
            $existing->save();

            return;
        }

        AuditLog::query()->create([
            'action' => $action,
            'actor_type' => $actorClass,
            'actor_id' => $actor?->getKey(),
            'target_type' => $targetClass,
            'target_id' => $target?->getKey(),
            'context' => $context + ['seed' => self::PREVIEW_KEY],
            'created_at' => $createdAt,
        ]);
    }

    private function placeBet(User $user, EsportMatch $match, string $prediction, int $stake, string $key): void
    {
        try {
            app(PlaceBetAction::class)->execute($user, [
                'match_id' => $match->id,
                'prediction' => $prediction,
                'stake_points' => $stake,
                'idempotency_key' => 'seed.preview.bet.'.self::PREVIEW_KEY.'.'.$key,
                'meta' => ['seed' => self::PREVIEW_KEY],
            ]);
        } catch (Throwable) {
            // Ignore idempotent collisions when replaying the preview seed.
        }
    }
}
