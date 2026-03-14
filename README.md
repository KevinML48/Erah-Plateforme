# ERAH Plateforme

ERAH Plateforme est une application Laravel 11 orientee esport/community. Le socle actif est Blade-first, avec quelques surfaces Inertia conservees la ou elles sont deja branchees proprement dans l authentification, l aide et certains ecrans admin.

La plateforme couvre aujourd hui :

- landing publique et pages de decouverte
- espace membre avec dashboard, matchs, clips, paris, missions, cadeaux, duels et profil
- portefeuille points unifie
- progression XP, ligues et classements
- centre d aide, assistant ERAH et visite guidee
- modules communautaires additionnels: quiz, live codes, succes, boutique et evenements
- pilotage admin pour clips, matchs, missions, cadeaux, galerie, avis et moderation

## Stack active

- Laravel 11 / PHP 8.3
- Blade + Tailwind + layouts marketing/app
- JS leger pour interactions web, PWA et surfaces hybrides
- Inertia / React conserves sur quelques ecrans deja relies au produit
- `templates-neuf` utilise comme base de reference pour certains patterns HTML et surfaces premium

## Entrees principales

### Public

- `/` : landing et decouverte ERAH
- `/app/*` : lecture publique des modules ouverts
- `/aide` et `/aide/assistant` : FAQ et assistant public
- `/supporter` : presentation du programme supporter
- `/contact` : formulaire de contact (stockage + notification email)

### Membre connecte

- `/console/dashboard` : espace principal
- `/console/*` : modules membres et hub d aide
- `/console/assistant` : assistant conversationnel membre

### Admin

- `/console/admin/*` : pilotage, moderation et configuration

## Modules principaux

- Clips et interactions communautaires
- Matchs et paris
- Missions, focus missions et recompenses
- Cadeaux et demandes de redemption
- Duels et classement duel
- Notifications, wallet points et historique
- Profil public, avis membres et supporter
- Quiz, live codes, succes, boutique et evenements

## Structure utile

- `app/Http/Controllers/Web` : controleurs web Blade et surfaces hybrides
- `app/Services` : logique metier principale
- `resources/views/pages` : pages Blade produit
- `resources/views/components` : composants Blade reutilisables
- `resources/js/Pages` : surfaces Inertia encore actives
- `docs/` : docs projet, mapping routes et spec UI

## Mise en route

```bash
composer install
npm install
php artisan migrate
php artisan db:seed
npm run build
php artisan test
```

## Commandes utiles

```bash
php artisan migrate
php artisan db:seed --class=Database\\Seeders\\LaunchMissionCatalogSeeder
php artisan db:seed --class=CommunityPlatformSeeder
php artisan db:seed --class=MissionsAndGiftsSeeder
php artisan test
php artisan optimize:clear
```

## Regles produit a connaitre

- Les points plateforme servent de monnaie unique pour cadeaux, paris et duels.
- L XP fait progresser le membre et alimente sa ligue.
- Les ligues suivent la liste canonique: Bronze, Argent, Gold, Platine, Diamant, Champion, ERAH Prime.
- Les missions distribuent des rewards simples et lisibles.
- Le catalogue canonique des 50 missions de lancement est versionne dans `database/seeders/data/launch_missions.php`.
- Les zones privees et admin restent non indexees.

## Production

Recommandations minimales :

- `php artisan config:cache`
- `php artisan route:cache`
- `php artisan view:cache`
- `php artisan queue:work` (worker actif)
- `php artisan schedule:run` (cron chaque minute)
- OPcache actif
- MySQL ou PostgreSQL
- Redis pour cache, queue et sessions si disponible

Variables contact a renseigner :

- `MAIL_CONTACT_ADDRESS` : adresse de reception des demandes contact
- `MAIL_CONTACT_NAME` : nom associe a la boite de reception contact

## Documentation

- `docs/ROUTES_MAPPING.md` : cartographie actuelle des parcours
- `docs/SITEMAP.md` : vue sitemap / navigation
- `docs/GAME_UI_SPEC.md` : repere UI Blade-first pour l application
- `docs/PRODUCTION_RUNBOOK.md` : prerequis d'exploitation prod (queue/scheduler/jobs)
