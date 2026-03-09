# ERAH Plateforme

Plateforme communautaire esport Laravel 11 pour ERAH, avec modules clips, paris, duels, missions, cadeaux, profils publics, avis membres, galerie, et maintenant un socle communautaire complet:

- ligues communautaires basees sur l XP
- leaderboards XP / rang / duel
- rewards clips avec caps journaliers
- commentaires clips avec reponses niveau 1
- quiz et tentatives
- codes live et redemptions
- resultats de duels admin
- succes permanents
- streak de connexion
- boutique communautaire
- evenements dynamiques
- push subscriptions et PWA minimale

## Architecture

Le projet conserve les modules existants et ajoute une couche metier dediee dans `app/Services`:

- `WalletService`
- `RewardGrantService`
- `RankService`
- `LeaderboardService`
- `ClipRewardService`
- `MissionEngine`
- `QuizService`
- `LiveCodeService`
- `DuelService`
- `BetService`
- `AchievementService`
- `StreakService`
- `ShopService`
- `PushNotificationService`
- `EventService`

Les controllers restent minces et s appuient sur ces services. Les pages web reutilisent les layouts Blade existants et les nouveaux ecrans marketing/admin s alignent sur `templates-neuf`.

## Donnees ajoutees

Migrations communautaires:

- `2026_03_09_100000_create_community_platform_foundations.php`
- `2026_03_09_100100_extend_clip_comments_and_create_views.php`
- `2026_03_09_100200_create_quiz_tables.php`
- `2026_03_09_100300_create_live_code_tables.php`
- `2026_03_09_100400_create_achievement_tables.php`
- `2026_03_09_100500_create_shop_tables.php`
- `2026_03_09_100600_create_duel_results_table_and_extend_progress.php`

Seeder communautaire:

- `database/seeders/CommunityPlatformSeeder.php`

Ce seeder initialise:

- les succes par defaut
- les objets boutique par defaut
- un quiz communautaire de demonstration
- un code live publie
- un evenement dynamique bonus clips

## Routes principales ajoutees

Web app / console:

- `/app/quizzes`, `/console/quizzes`
- `/app/live-codes`, `/console/live-codes`
- `/app/statistics`, `/console/statistics`
- `/app/achievements`, `/console/achievements`
- `/app/shop`, `/console/shop`

Admin:

- `/console/admin/missions`
- `/console/admin/quizzes`
- `/console/admin/live-codes`
- `/console/admin/events`
- `/console/admin/duels/{duelId}/result`

API:

- `GET /api/community/leaderboards`
- `POST /api/me/push-subscriptions`
- `DELETE /api/me/push-subscriptions`

## PWA

Fichiers publics:

- `public/manifest.json`
- `public/sw.js`

Le manifest est branche sur les layouts app, guest et marketing. Le service worker est enregistre cote app via `resources/js/app.js` et cote marketing dans le layout template.

## Mise en route

```bash
composer install
npm install
php artisan migrate
php artisan db:seed
npm run build
php artisan view:cache
php artisan test
```

Pour seed uniquement le module communautaire:

```bash
php artisan db:seed --class=CommunityPlatformSeeder
```

## Verification

Validation effectuee sur cette implementation:

- `npm run build`
- `php artisan view:cache`
- `php artisan test`

Resultat:

- `103 passed`

## Notes

- Le systeme de ligues competitives existant base sur les `rank_points` est conserve.
- Les nouvelles ligues communautaires basees sur l XP vivent a cote via `RankService` et `user_rank_histories`.
- Les nouvelles recompenses communautaires sont idempotentes via `community_reward_grants`.
- Le front desktop existant n est pas refondu; les nouvelles pages et sections reutilisent les patterns Blade / templates deja presents.
