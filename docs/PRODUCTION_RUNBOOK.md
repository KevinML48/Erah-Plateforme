# Production Runbook

Ce document liste le minimum d'exploitation a activer pour un deploiement stable.

## 1) Variables et mode prod

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL` renseignee
- base de donnees prod configuree

## 2) Build et caches

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 3) Queue workers obligatoires

Ces jobs sont critiques pour le comportement produit:

- `App\Jobs\ExpireDuelJob`: expiration automatique des duels
- `App\Jobs\SendNotificationChannelJob`: livraison asynchrone des canaux de notification
- `App\Jobs\SendAdminOutboundEmailJob`: emails admin individuels
- `Illuminate\Mail\SendQueuedMailable`: emails marketing / contact mis en queue

Sans worker actif, ces traitements sont retardes ou absents.

Commande minimum:

```bash
php artisan queue:work --queue=default --sleep=1 --tries=3 --max-time=3600
```

## 4) Scheduler obligatoire

Le scheduler doit tourner chaque minute:

```bash
php artisan schedule:run
```

Tache actuellement planifiee:

- `supporter:grant-monthly-rewards` (mensuel)

## 5) Supervision recommandee

- Superviser `queue:work` (systemd, supervisor, pm2, etc.)
- Rotation des logs Laravel (`storage/logs`)
- Monitoring des echecs jobs (`failed_jobs`)

## 6) Smoke checks post-deploiement

- ouverture `/console/dashboard`
- creation d'un duel puis verification expiration
- envoi d'une notification puis verification job traite
- achat shop / cadeau / pari (debit points unique)
- verification leaderboard et ligues affiches

