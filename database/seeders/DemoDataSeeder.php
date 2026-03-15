<?php

namespace Database\Seeders;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Application\Actions\Duels\AcceptDuelAction;
use App\Application\Actions\Duels\CreateDuelAction;
use App\Application\Actions\Duels\ExpireDuelAction;
use App\Application\Actions\Duels\RefuseDuelAction;
use App\Application\Actions\Notifications\EnsureNotificationSettingsAction;
use App\Application\Actions\Notifications\RegisterUserDeviceAction;
use App\Application\Actions\Ranking\AddPointsAction;
use App\Domain\Notifications\Enums\NotificationCategory;
use App\Models\Bet;
use App\Models\Clip;
use App\Models\ClipComment;
use App\Models\ClipFavorite;
use App\Models\ClipLike;
use App\Models\ClipShare;
use App\Models\Duel;
use App\Models\EsportMatch;
use App\Models\MatchSettlement;
use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\PointsTransaction;
use App\Models\User;
use App\Models\UserNotificationChannel;
use Illuminate\Database\Seeder;
use RuntimeException;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@erah.local')->first();
        if (! $admin) {
            throw new RuntimeException('Admin user is missing. Run AdminUserSeeder first.');
        }

        $users = $this->seedUsers();
        $this->seedNotificationSettings($users);
        $this->seedRanking($admin, $users);

        $clips = $this->seedClips($admin);
        $this->seedClipInteractions($admin, $users, $clips);

        $this->seedNotifications($users);
        $this->seedDuels($users);
        $this->seedMatchesAndBets($admin, $users);

        app(StoreAuditLogAction::class)->execute(
            action: 'seed.demo_data.upserted',
            actor: $admin,
            target: null,
            context: [
                'seed_class' => self::class,
                'users' => count($users),
                'clips' => count($clips),
            ],
        );
    }

    /**
     * @return array<string, User>
     */
    private function seedUsers(): array
    {
        $definitions = [
            'one' => [
                'name' => 'Player One',
                'email' => 'player.one@erah.local',
                'bio' => 'Main initiateur, passionne par les clutchs propres et le jeu d equipe.',
                'avatar_path' => 'https://picsum.photos/seed/erah-user-one/240/240',
                'twitter_url' => 'https://x.com/playerone_erah',
                'instagram_url' => 'https://instagram.com/playerone_erah',
                'tiktok_url' => 'https://tiktok.com/@playerone_erah',
                'discord_url' => 'https://discord.gg/erah',
            ],
            'noah' => [
                'name' => 'Noah Blitz',
                'email' => 'noah.blitz@erah.local',
                'bio' => 'Entry fragger, tempo agressif et prises d initiative rapides.',
                'avatar_path' => 'https://picsum.photos/seed/erah-user-noah/240/240',
                'twitter_url' => 'https://x.com/noahblitz',
            ],
            'lina' => [
                'name' => 'Lina Rush',
                'email' => 'lina.rush@erah.local',
                'bio' => 'Coach VOD et shotcaller, fan de strategie mid-round.',
                'avatar_path' => 'https://picsum.photos/seed/erah-user-lina/240/240',
                'instagram_url' => 'https://instagram.com/lina.rush',
            ],
            'marco' => [
                'name' => 'Marco Ace',
                'email' => 'marco.ace@erah.local',
                'bio' => 'Sniper principal, precision et discipline avant tout.',
                'avatar_path' => 'https://picsum.photos/seed/erah-user-marco/240/240',
                'discord_url' => 'https://discord.gg/erahmarco',
            ],
            'zoe' => [
                'name' => 'Zoe Void',
                'email' => 'zoe.void@erah.local',
                'bio' => 'Support utilitaire, specialisee dans les executions propres.',
                'avatar_path' => 'https://picsum.photos/seed/erah-user-zoe/240/240',
                'tiktok_url' => 'https://tiktok.com/@zoevoid',
            ],
            'yuna' => [
                'name' => 'Yuna Strike',
                'email' => 'yuna.strike@erah.local',
                'bio' => 'Rifler polyvalente et anti-eco specialist.',
                'avatar_path' => 'https://picsum.photos/seed/erah-user-yuna/240/240',
            ],
            'ryan' => [
                'name' => 'Ryan Pulse',
                'email' => 'ryan.pulse@erah.local',
                'bio' => 'Analyse macro et adaptation en BO3.',
                'avatar_path' => 'https://picsum.photos/seed/erah-user-ryan/240/240',
            ],
            'maya' => [
                'name' => 'Maya Nova',
                'email' => 'maya.nova@erah.local',
                'bio' => 'Jeune talent, progression rapide en ladder.',
                'avatar_path' => 'https://picsum.photos/seed/erah-user-maya/240/240',
            ],
            'aiden' => [
                'name' => 'Aiden Wolf',
                'email' => 'aiden.wolf@erah.local',
                'bio' => 'Leader vocal, aime controler le tempo du match.',
                'avatar_path' => 'https://picsum.photos/seed/erah-user-aiden/240/240',
            ],
            'sara' => [
                'name' => 'Sara Drift',
                'email' => 'sara.drift@erah.local',
                'bio' => 'Aim laser et adaptation rapide en clutch.',
                'avatar_path' => 'https://picsum.photos/seed/erah-user-sara/240/240',
            ],
            'leo' => [
                'name' => 'Leo Zenith',
                'email' => 'leo.zenith@erah.local',
                'bio' => 'Joueur polyvalent avec excellent sens du timing.',
                'avatar_path' => 'https://picsum.photos/seed/erah-user-leo/240/240',
            ],
            'nina' => [
                'name' => 'Nina Flux',
                'email' => 'nina.flux@erah.local',
                'bio' => 'Specialiste des retakes et des executions propres.',
                'avatar_path' => 'https://picsum.photos/seed/erah-user-nina/240/240',
            ],
            'tom' => [
                'name' => 'Tom Viper',
                'email' => 'tom.viper@erah.local',
                'bio' => 'Entry agresif, toujours en première ligne.',
                'avatar_path' => 'https://picsum.photos/seed/erah-user-tom/240/240',
            ],
            'clara' => [
                'name' => 'Clara Echo',
                'email' => 'clara.echo@erah.local',
                'bio' => 'Support smart utilitaire et bons calls macro.',
                'avatar_path' => 'https://picsum.photos/seed/erah-user-clara/240/240',
            ],
            'hugo' => [
                'name' => 'Hugo Prime',
                'email' => 'hugo.prime@erah.local',
                'bio' => 'Main rifle, stable et regulier sur le long terme.',
                'avatar_path' => 'https://picsum.photos/seed/erah-user-hugo/240/240',
            ],
            'ines' => [
                'name' => 'Ines Spark',
                'email' => 'ines.spark@erah.local',
                'bio' => 'Aime les duels techniques et les rounds lents.',
                'avatar_path' => 'https://picsum.photos/seed/erah-user-ines/240/240',
            ],
            'alex' => [
                'name' => 'Alex Comet',
                'email' => 'alex.comet@erah.local',
                'bio' => 'Progression constante avec un style discipline.',
                'avatar_path' => 'https://picsum.photos/seed/erah-user-alex/240/240',
            ],
            'jade' => [
                'name' => 'Jade Orbit',
                'email' => 'jade.orbit@erah.local',
                'bio' => 'Bonne lecture de jeu et sens du positionnement.',
                'avatar_path' => 'https://picsum.photos/seed/erah-user-jade/240/240',
            ],
            'nora' => [
                'name' => 'Nora Blaze',
                'email' => 'nora.blaze@erah.local',
                'bio' => 'Main lurk, prend les timings faibles a la perfection.',
                'avatar_path' => 'https://picsum.photos/seed/erah-user-nora/240/240',
            ],
            'isaac' => [
                'name' => 'Isaac Volt',
                'email' => 'isaac.volt@erah.local',
                'bio' => 'Joueur equipe, fort sur les retakes coordonnes.',
                'avatar_path' => 'https://picsum.photos/seed/erah-user-isaac/240/240',
            ],
            'paul' => [
                'name' => 'Paul Rift',
                'email' => 'paul.rift@erah.local',
                'bio' => 'Rookie motive, grind quotidien en ladder.',
                'avatar_path' => 'https://picsum.photos/seed/erah-user-paul/240/240',
            ],
            'emma' => [
                'name' => 'Emma Pulse',
                'email' => 'emma.pulse@erah.local',
                'bio' => 'Aime travailler la mecanique pure en deathmatch.',
                'avatar_path' => 'https://picsum.photos/seed/erah-user-emma/240/240',
            ],
            'yanis' => [
                'name' => 'Yanis Crow',
                'email' => 'yanis.crow@erah.local',
                'bio' => 'Profil tactique, progression lente mais solide.',
                'avatar_path' => 'https://picsum.photos/seed/erah-user-yanis/240/240',
            ],
            'chloe' => [
                'name' => 'Chloe Mist',
                'email' => 'chloe.mist@erah.local',
                'bio' => 'Nouvelle recrue avec objectif top ladder.',
                'avatar_path' => 'https://picsum.photos/seed/erah-user-chloe/240/240',
            ],
        ];

        $password = (string) env('DEMO_USER_PASSWORD', 'Password123!');
        $users = [];

        foreach ($definitions as $alias => $definition) {
            $user = User::query()->firstOrCreate(
                ['email' => $definition['email']],
                [
                    'name' => $definition['name'],
                    'password' => $password,
                    'role' => User::ROLE_USER,
                    'email_verified_at' => now(),
                    'bio' => $definition['bio'] ?? null,
                    'avatar_path' => $definition['avatar_path'] ?? null,
                    'twitter_url' => $definition['twitter_url'] ?? null,
                    'instagram_url' => $definition['instagram_url'] ?? null,
                    'tiktok_url' => $definition['tiktok_url'] ?? null,
                    'discord_url' => $definition['discord_url'] ?? null,
                ]
            );

            $dirty = false;

            if ($user->name !== $definition['name']) {
                $user->name = $definition['name'];
                $dirty = true;
            }

            if ($user->role !== User::ROLE_USER) {
                $user->role = User::ROLE_USER;
                $dirty = true;
            }

            if (! $user->email_verified_at) {
                $user->email_verified_at = now();
                $dirty = true;
            }

            foreach (['bio', 'avatar_path', 'twitter_url', 'instagram_url', 'tiktok_url', 'discord_url'] as $field) {
                $target = $definition[$field] ?? null;
                if ($user->{$field} !== $target) {
                    $user->{$field} = $target;
                    $dirty = true;
                }
            }

            if ($dirty) {
                $user->save();
            }

            $users[$alias] = $user->fresh();
        }

        return $users;
    }

    /**
     * @param array<string, User> $users
     */
    private function seedNotificationSettings(array $users): void
    {
        $ensureSettings = app(EnsureNotificationSettingsAction::class);
        $registerDevice = app(RegisterUserDeviceAction::class);

        $globalChannels = [
            'one' => ['email' => true, 'push' => true],
            'noah' => ['email' => true, 'push' => false],
            'lina' => ['email' => true, 'push' => true],
            'marco' => ['email' => false, 'push' => true],
            'zoe' => ['email' => true, 'push' => true],
            'yuna' => ['email' => true, 'push' => true],
            'ryan' => ['email' => true, 'push' => false],
            'maya' => ['email' => true, 'push' => true],
        ];

        $overrides = [
            'noah' => [
                'clips' => ['email' => false, 'push' => false],
            ],
            'marco' => [
                'bet' => ['email' => false, 'push' => true],
            ],
            'ryan' => [
                'duel' => ['email' => true, 'push' => false],
            ],
        ];

        foreach ($users as $alias => $user) {
            $ensureSettings->execute($user);

            UserNotificationChannel::query()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'email_opt_in' => $globalChannels[$alias]['email'] ?? true,
                    'push_opt_in' => $globalChannels[$alias]['push'] ?? true,
                ]
            );

            foreach (NotificationCategory::values() as $category) {
                $categoryPreference = $overrides[$alias][$category] ?? ['email' => true, 'push' => true];

                NotificationPreference::query()->updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'category' => $category,
                    ],
                    [
                        'email_enabled' => $categoryPreference['email'],
                        'push_enabled' => $categoryPreference['push'],
                    ]
                );
            }

            $registerDevice->execute($user, [
                'platform' => 'android',
                'device_token' => 'seed-device-'.$user->id.'-android',
                'device_name' => 'Demo Device '.strtoupper($alias),
                'is_active' => true,
                'meta' => ['source' => 'demo-seeder'],
            ]);
        }
    }

    /**
     * @param array<string, User> $users
     */
    private function seedRanking(User $admin, array $users): void
    {
        $rankPoints = [
            'one' => 1650,
            'noah' => 1180,
            'lina' => 920,
            'marco' => 760,
            'zoe' => 540,
            'yuna' => 330,
            'ryan' => 180,
            'maya' => 80,
            'aiden' => 1540,
            'sara' => 1470,
            'leo' => 1290,
            'nina' => 1035,
            'tom' => 840,
            'clara' => 690,
            'hugo' => 470,
            'ines' => 390,
            'alex' => 280,
            'jade' => 230,
            'nora' => 145,
            'isaac' => 110,
            'paul' => 95,
            'emma' => 55,
            'yanis' => 35,
            'chloe' => 10,
        ];

        $xpPoints = [
            'one' => 4200,
            'noah' => 3100,
            'lina' => 2500,
            'marco' => 2100,
            'zoe' => 1700,
            'yuna' => 1300,
            'ryan' => 900,
            'maya' => 500,
            'aiden' => 3900,
            'sara' => 3650,
            'leo' => 3400,
            'nina' => 2950,
            'tom' => 2750,
            'clara' => 2450,
            'hugo' => 2100,
            'ines' => 1880,
            'alex' => 1620,
            'jade' => 1320,
            'nora' => 1120,
            'isaac' => 980,
            'paul' => 760,
            'emma' => 560,
            'yanis' => 420,
            'chloe' => 320,
        ];

        $addPoints = app(AddPointsAction::class);

        foreach ($rankPoints as $alias => $points) {
            $user = $users[$alias];

            $addPoints->execute(
                user: $user,
                kind: PointsTransaction::KIND_RANK,
                points: $points,
                sourceType: 'seed.demo.rank',
                sourceId: $alias.'.v1',
                actor: $admin,
                meta: ['seed' => 'demo'],
            );

            $addPoints->execute(
                user: $user,
                kind: PointsTransaction::KIND_XP,
                points: $xpPoints[$alias],
                sourceType: 'seed.demo.xp',
                sourceId: $alias.'.v1',
                actor: $admin,
                meta: ['seed' => 'demo'],
            );
        }
    }

    /**
     * @return array<string, Clip>
     */
    private function seedClips(User $admin): array
    {
        $definitions = [
            [
                'slug' => 'ace-clutch-finale',
                'title' => 'Ace clutch en finale',
                'description' => 'Round decisif remporte en 1v4 avec un timing parfait.',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
                'thumbnail_url' => 'https://picsum.photos/seed/erah-clip-1/1280/720',
                'is_published' => true,
                'published_days_ago' => 1,
            ],
            [
                'slug' => 'retake-millimetre',
                'title' => 'Retake au millimetre',
                'description' => 'Coordination parfaite sur un retake a 3 secondes de la fin.',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4',
                'thumbnail_url' => 'https://picsum.photos/seed/erah-clip-2/1280/720',
                'is_published' => true,
                'published_days_ago' => 2,
            ],
            [
                'slug' => 'rush-mid-lightning',
                'title' => 'Rush mid lightning',
                'description' => 'Execution agressive en debut de match avec enchainement parfait.',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4',
                'thumbnail_url' => 'https://picsum.photos/seed/erah-clip-3/1280/720',
                'is_published' => true,
                'published_days_ago' => 3,
            ],
            [
                'slug' => 'sniper-flick-masterclass',
                'title' => 'Sniper flick masterclass',
                'description' => 'Deux flicks consecutifs pour retourner le round.',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4',
                'thumbnail_url' => 'https://picsum.photos/seed/erah-clip-4/1280/720',
                'is_published' => true,
                'published_days_ago' => 5,
            ],
            [
                'slug' => 'defuse-sous-pression',
                'title' => 'Defuse sous pression',
                'description' => 'Defuse a la derniere seconde sous fumigene.',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/Sintel.mp4',
                'thumbnail_url' => 'https://picsum.photos/seed/erah-clip-5/1280/720',
                'is_published' => true,
                'published_days_ago' => 6,
            ],
            [
                'slug' => 'eco-round-surprise',
                'title' => 'Eco round surprise',
                'description' => 'Victoire improbable en eco round contre full buy.',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4',
                'thumbnail_url' => 'https://picsum.photos/seed/erah-clip-6/1280/720',
                'is_published' => true,
                'published_days_ago' => 7,
            ],
            [
                'slug' => 'strat-smoke-execute',
                'title' => 'Strat smoke execute',
                'description' => 'Set smoke parfait pour ouvrir le site en equipe.',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/VolkswagenGTIReview.mp4',
                'thumbnail_url' => 'https://picsum.photos/seed/erah-clip-7/1280/720',
                'is_published' => false,
                'published_days_ago' => null,
            ],
            [
                'slug' => 'draft-analysis-vod',
                'title' => 'Draft analyse VOD',
                'description' => 'Brouillon technique pour une publication admin.',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/WeAreGoingOnBullrun.mp4',
                'thumbnail_url' => 'https://picsum.photos/seed/erah-clip-8/1280/720',
                'is_published' => false,
                'published_days_ago' => null,
            ],
        ];

        $clips = [];

        foreach ($definitions as $definition) {
            $publishedAt = $definition['is_published'] && is_int($definition['published_days_ago'])
                ? now()->subDays($definition['published_days_ago'])
                : null;

            $clip = Clip::query()->updateOrCreate(
                ['slug' => $definition['slug']],
                [
                    'title' => $definition['title'],
                    'description' => $definition['description'],
                    'video_url' => $definition['video_url'],
                    'thumbnail_url' => $definition['thumbnail_url'],
                    'is_published' => $definition['is_published'],
                    'published_at' => $publishedAt,
                    'created_by' => $admin->id,
                    'updated_by' => $admin->id,
                ]
            );

            $clips[$definition['slug']] = $clip->fresh();
        }

        return $clips;
    }

    /**
     * @param array<string, User> $users
     * @param array<string, Clip> $clips
     */
    private function seedClipInteractions(User $admin, array $users, array $clips): void
    {
        $likes = [
            'ace-clutch-finale' => ['one', 'noah', 'lina', 'marco', 'zoe'],
            'retake-millimetre' => ['one', 'lina', 'zoe', 'yuna'],
            'rush-mid-lightning' => ['noah', 'marco', 'zoe', 'ryan'],
            'sniper-flick-masterclass' => ['one', 'noah', 'lina'],
            'defuse-sous-pression' => ['yuna', 'maya'],
            'eco-round-surprise' => ['one', 'noah', 'marco', 'ryan', 'maya'],
        ];

        $favorites = [
            'ace-clutch-finale' => ['one', 'lina', 'zoe'],
            'retake-millimetre' => ['noah', 'marco'],
            'rush-mid-lightning' => ['one', 'yuna', 'maya'],
            'sniper-flick-masterclass' => ['lina', 'ryan'],
            'eco-round-surprise' => ['one', 'noah', 'zoe'],
        ];

        $comments = [
            'ace-clutch-finale' => [
                ['user' => 'one', 'body' => 'Timing parfait, ce clutch etait monstrueux.'],
                ['user' => 'noah', 'body' => 'Le crosshair placement est ultra propre.'],
                ['user' => 'zoe', 'body' => 'Clip de la semaine, clairement.'],
            ],
            'retake-millimetre' => [
                ['user' => 'lina', 'body' => 'J adore la coordination dans ce retake.'],
                ['user' => 'marco', 'body' => 'Les utilitaires ont fait toute la diff.'],
            ],
            'rush-mid-lightning' => [
                ['user' => 'ryan', 'body' => 'Execution rapide et tres propre.'],
                ['user' => 'maya', 'body' => 'La prise d info avant le push est nickel.'],
            ],
            'sniper-flick-masterclass' => [
                ['user' => 'one', 'body' => 'Double flick, C\'est sale.'],
            ],
        ];

        foreach ($likes as $slug => $aliases) {
            foreach ($aliases as $alias) {
                ClipLike::query()->firstOrCreate([
                    'clip_id' => $clips[$slug]->id,
                    'user_id' => $users[$alias]->id,
                ]);
            }
        }

        foreach ($favorites as $slug => $aliases) {
            foreach ($aliases as $alias) {
                ClipFavorite::query()->firstOrCreate([
                    'clip_id' => $clips[$slug]->id,
                    'user_id' => $users[$alias]->id,
                ]);
            }
        }

        foreach ($comments as $slug => $items) {
            foreach ($items as $item) {
                ClipComment::query()->firstOrCreate([
                    'clip_id' => $clips[$slug]->id,
                    'user_id' => $users[$item['user']]->id,
                    'body' => $item['body'],
                ]);
            }
        }

        $shareChannels = ['link', 'discord', 'copy'];
        foreach (['ace-clutch-finale', 'retake-millimetre', 'rush-mid-lightning'] as $index => $slug) {
            $clip = $clips[$slug];
            $alias = array_keys($users)[$index];

            ClipShare::query()->firstOrCreate([
                'clip_id' => $clip->id,
                'user_id' => $users[$alias]->id,
                'channel' => $shareChannels[$index],
                'shared_url' => url('/clips/'.$clip->slug),
            ]);
        }

        foreach ($clips as $clip) {
            Clip::query()
                ->whereKey($clip->id)
                ->update([
                    'likes_count' => ClipLike::query()->where('clip_id', $clip->id)->count(),
                    'favorites_count' => ClipFavorite::query()->where('clip_id', $clip->id)->count(),
                    'comments_count' => ClipComment::query()->where('clip_id', $clip->id)->count(),
                    'updated_by' => $admin->id,
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * @param array<string, User> $users
     */
    private function seedNotifications(array $users): void
    {
        $notifications = [
            'one' => [
                ['category' => 'system', 'title' => 'Bienvenue sur ERAH', 'message' => 'Ton profil est pret.', 'read' => true],
                ['category' => 'duel', 'title' => 'Duel en attente', 'message' => 'Noah Blitz attend ta réponse.', 'read' => false],
                ['category' => 'clips', 'title' => 'Nouveau clip tendance', 'message' => 'Ace clutch en finale gagne en popularite.', 'read' => false],
            ],
            'noah' => [
                ['category' => 'match', 'title' => 'Match programme', 'message' => 'ERAH Weekly #25 commence demain.', 'read' => false],
                ['category' => 'bet', 'title' => 'Pari regle', 'message' => 'Ton dernier pari a ete confirme.', 'read' => true],
            ],
            'lina' => [
                ['category' => 'clips', 'title' => 'Commentaire recu', 'message' => 'Marco Ace a reagi a ton commentaire.', 'read' => false],
            ],
            'marco' => [
                ['category' => 'duel', 'title' => 'Duel accepte', 'message' => 'Zoe Void a accepte ton duel.', 'read' => true],
            ],
            'zoe' => [
                ['category' => 'system', 'title' => 'Promotion de ligue', 'message' => 'Tu as atteint la ligue Platine.', 'read' => false],
            ],
        ];

        foreach ($notifications as $alias => $entries) {
            $user = $users[$alias];

            foreach ($entries as $entry) {
                Notification::query()->updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'category' => $entry['category'],
                        'title' => $entry['title'],
                    ],
                    [
                        'message' => $entry['message'],
                        'data' => ['source' => 'demo-seeder'],
                        'read_at' => $entry['read'] ? now()->subMinutes(30) : null,
                    ]
                );
            }
        }
    }

    /**
     * @param array<string, User> $users
     */
    private function seedDuels(array $users): void
    {
        $createDuel = app(CreateDuelAction::class);
        $acceptDuel = app(AcceptDuelAction::class);
        $refuseDuel = app(RefuseDuelAction::class);
        $expireDuel = app(ExpireDuelAction::class);

        $duelDefinitions = [
            [
                'key' => 'seed-duel-pending-v1',
                'challenger' => 'one',
                'challenged' => 'noah',
                'message' => 'Prend ta revanche ce soir ?',
                'expires' => 240,
                'result' => 'pending',
            ],
            [
                'key' => 'seed-duel-pending-v2',
                'challenger' => 'noah',
                'challenged' => 'one',
                'message' => 'Match retour immediat ?',
                'expires' => 360,
                'result' => 'pending',
            ],
            [
                'key' => 'seed-duel-pending-v3',
                'challenger' => 'zoe',
                'challenged' => 'marco',
                'message' => 'Duel sniper only.',
                'expires' => 420,
                'result' => 'pending',
            ],
            [
                'key' => 'seed-duel-pending-v4',
                'challenger' => 'maya',
                'challenged' => 'lina',
                'message' => 'BO1 test de setup.',
                'expires' => 520,
                'result' => 'pending',
            ],
            [
                'key' => 'seed-duel-accepted-v1',
                'challenger' => 'marco',
                'challenged' => 'zoe',
                'message' => 'Best of 3 en custom.',
                'expires' => 180,
                'result' => 'accepted',
            ],
            [
                'key' => 'seed-duel-accepted-v2',
                'challenger' => 'lina',
                'challenged' => 'ryan',
                'message' => 'Analyse + duel macro.',
                'expires' => 200,
                'result' => 'accepted',
            ],
            [
                'key' => 'seed-duel-accepted-v3',
                'challenger' => 'one',
                'challenged' => 'yuna',
                'message' => 'Rifles only, map courte.',
                'expires' => 300,
                'result' => 'accepted',
            ],
            [
                'key' => 'seed-duel-refused-v1',
                'challenger' => 'yuna',
                'challenged' => 'ryan',
                'message' => 'On lance le duel maintenant ?',
                'expires' => 180,
                'result' => 'refused',
            ],
            [
                'key' => 'seed-duel-refused-v2',
                'challenger' => 'noah',
                'challenged' => 'maya',
                'message' => 'Session late night ?',
                'expires' => 240,
                'result' => 'refused',
            ],
            [
                'key' => 'seed-duel-expired-v1',
                'challenger' => 'lina',
                'challenged' => 'maya',
                'message' => 'Tu as 5 minutes pour accepter.',
                'expires' => 5,
                'result' => 'expired',
            ],
            [
                'key' => 'seed-duel-expired-v2',
                'challenger' => 'zoe',
                'challenged' => 'one',
                'message' => 'Mini duel warmup avant scrim.',
                'expires' => 5,
                'result' => 'expired',
            ],
            [
                'key' => 'seed-duel-expired-v3',
                'challenger' => 'ryan',
                'challenged' => 'marco',
                'message' => 'Duel tactique sans vocal.',
                'expires' => 5,
                'result' => 'expired',
            ],
        ];

        foreach ($duelDefinitions as $definition) {
            $created = $createDuel->execute(
                challenger: $users[$definition['challenger']],
                challengedUserId: $users[$definition['challenged']]->id,
                idempotencyKey: $definition['key'],
                message: $definition['message'],
                expiresInMinutes: $definition['expires'],
            );

            $duelId = (int) $created['duel']->id;
            $result = (string) $definition['result'];

            if ($result === 'accepted') {
                $acceptDuel->execute($users[$definition['challenged']], $duelId);
                continue;
            }

            if ($result === 'refused') {
                $refuseDuel->execute($users[$definition['challenged']], $duelId);
                continue;
            }

            if ($result === 'expired') {
                $expiredDuel = Duel::query()->find($duelId);
                if ($expiredDuel && $expiredDuel->status === Duel::STATUS_PENDING) {
                    $expiredDuel->expires_at = now()->subMinutes(10);
                    $expiredDuel->save();
                }

                $expireDuel->execute($duelId);
            }
        }
    }

    /**
     * @param array<string, User> $users
     */
    private function seedMatchesAndBets(User $admin, array $users): void
    {
        $matchDefinitions = [
            'erah-weekly-25' => [
                'home_team' => 'Team Nova',
                'away_team' => 'Team Pulse',
                'starts_at' => now()->addDay(),
                'status' => EsportMatch::STATUS_SCHEDULED,
                'result' => null,
                'settled_at' => null,
            ],
            'erah-weekly-24' => [
                'home_team' => 'Orion Five',
                'away_team' => 'Crimson Fox',
                'starts_at' => now()->subDays(1),
                'status' => EsportMatch::STATUS_FINISHED,
                'result' => EsportMatch::RESULT_HOME,
                'settled_at' => now()->subHours(12),
            ],
            'erah-weekly-23' => [
                'home_team' => 'Silver Owls',
                'away_team' => 'Vector Unit',
                'starts_at' => now()->subDays(2),
                'status' => EsportMatch::STATUS_FINISHED,
                'result' => EsportMatch::RESULT_VOID,
                'settled_at' => now()->subHours(18),
            ],
        ];

        $matches = [];

        foreach ($matchDefinitions as $matchKey => $definition) {
            $match = EsportMatch::query()->updateOrCreate(
                ['match_key' => $matchKey],
                [
                    'home_team' => $definition['home_team'],
                    'away_team' => $definition['away_team'],
                    'starts_at' => $definition['starts_at'],
                    'status' => $definition['status'],
                    'result' => $definition['result'],
                    'settled_at' => $definition['settled_at'],
                    'meta' => ['seed' => 'demo'],
                    'created_by' => $admin->id,
                    'updated_by' => $admin->id,
                ]
            );

            $matches[$matchKey] = $match->fresh();
        }

        $betDefinitions = [
            ['match_key' => 'erah-weekly-25', 'user' => 'one', 'prediction' => Bet::PREDICTION_HOME, 'stake' => 140],
            ['match_key' => 'erah-weekly-25', 'user' => 'noah', 'prediction' => Bet::PREDICTION_AWAY, 'stake' => 80],
            ['match_key' => 'erah-weekly-25', 'user' => 'marco', 'prediction' => Bet::PREDICTION_DRAW, 'stake' => 40],
            ['match_key' => 'erah-weekly-24', 'user' => 'lina', 'prediction' => Bet::PREDICTION_HOME, 'stake' => 100],
            ['match_key' => 'erah-weekly-24', 'user' => 'zoe', 'prediction' => Bet::PREDICTION_AWAY, 'stake' => 120],
            ['match_key' => 'erah-weekly-24', 'user' => 'ryan', 'prediction' => Bet::PREDICTION_HOME, 'stake' => 60],
            ['match_key' => 'erah-weekly-23', 'user' => 'yuna', 'prediction' => Bet::PREDICTION_HOME, 'stake' => 75],
            ['match_key' => 'erah-weekly-23', 'user' => 'maya', 'prediction' => Bet::PREDICTION_AWAY, 'stake' => 50],
        ];

        foreach ($betDefinitions as $definition) {
            $match = $matches[$definition['match_key']];
            $user = $users[$definition['user']];
            $stake = (int) $definition['stake'];
            $prediction = $definition['prediction'];
            $potentialPayout = $this->calculatePotentialPayout($prediction, $stake);

            $status = Bet::STATUS_PENDING;
            $settlementPoints = 0;
            $settledAt = null;

            if ($match->status === EsportMatch::STATUS_FINISHED && $match->result === EsportMatch::RESULT_VOID) {
                $status = Bet::STATUS_VOID;
                $settlementPoints = $stake;
                $settledAt = $match->settled_at;
            } elseif ($match->status === EsportMatch::STATUS_FINISHED) {
                $status = $prediction === $match->result ? Bet::STATUS_WON : Bet::STATUS_LOST;
                $settlementPoints = $status === Bet::STATUS_WON ? $potentialPayout : 0;
                $settledAt = $match->settled_at;
            }

            Bet::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'match_id' => $match->id,
                ],
                [
                    'prediction' => $prediction,
                    'stake_points' => $stake,
                    'potential_payout' => $potentialPayout,
                    'settlement_points' => $settlementPoints,
                    'status' => $status,
                    'idempotency_key' => 'seed-bet-'.$definition['match_key'].'-'.$definition['user'],
                    'placed_at' => $match->starts_at?->copy()->subHours(4) ?? now()->subHours(4),
                    'settled_at' => $settledAt,
                    'meta' => ['seed' => 'demo'],
                ]
            );
        }

        foreach ($matches as $match) {
            if ($match->status !== EsportMatch::STATUS_FINISHED || ! $match->result || ! $match->settled_at) {
                continue;
            }

            $bets = Bet::query()->where('match_id', $match->id)->get();

            MatchSettlement::query()->updateOrCreate(
                ['match_id' => $match->id],
                [
                    'idempotency_key' => 'seed-settle-'.$match->match_key,
                    'result' => $match->result,
                    'bets_total' => $bets->count(),
                    'won_count' => $bets->where('status', Bet::STATUS_WON)->count(),
                    'lost_count' => $bets->where('status', Bet::STATUS_LOST)->count(),
                    'void_count' => $bets->where('status', Bet::STATUS_VOID)->count(),
                    'payout_total' => (int) $bets->sum('settlement_points'),
                    'processused_by' => $admin->id,
                    'processused_at' => $match->settled_at,
                    'meta' => ['seed' => 'demo'],
                ]
            );
        }
    }

    private function calculatePotentialPayout(string $prediction, int $stake): int
    {
        return $prediction === Bet::PREDICTION_DRAW
            ? $stake * 3
            : $stake * 2;
    }
}
