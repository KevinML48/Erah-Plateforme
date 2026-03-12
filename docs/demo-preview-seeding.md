# Demo Preview Seeding

## Purpose

`demo:seed` populates a rich and coherent demo dataset for local/staging only.
It is blocked in production, and `PlatformPreviewSeeder` also self-blocks in production.

## Commands

Rebuild from scratch and load full preview dataset:

```bash
php artisan demo:seed --fresh
```

Recommended for deterministic replay and clean snapshots.

Load/update preview dataset on existing local database:

```bash
php artisan demo:seed
```

This mode preserves existing data and enriches it; use `--fresh` when you need a fully reset demo snapshot.

Direct seeder call (without command wrapper):

```bash
php artisan db:seed --class=Database\\Seeders\\PlatformPreviewSeeder
```

## Demo Accounts

All accounts below use password `12345678`.

- Admin principal: `admin@gmail.com`
- Admin secondaire: `admin.demo@erah.local`
- Member active: `kylian.frost@erah.local`
- Member medium: `lea.circuit@erah.local`
- Member new: `nolan.spark@erah.local`
- Supporter: `sonia.vector@erah.local`
- Supporter: `mathis.nova@erah.local`

## What Gets Filled

- users/profiles with varied activity and progression
- ranks/xp/points wallets and streaks
- profile shortcuts + guided tour states + assistant conversations/favorites
- missions states (available, in progress, completed, focused)
- gifts catalog, favorites, cart, redemptions with multiple statuses
- shop items and purchases
- matches, bets (pending/won/lost/void), settlements
- duels (pending/accepted/settled/refused/expired)
- clips, interactions, views, comments/replies, votes/campaigns
- quizzes and attempts
- live codes (active/expired/draft) and redemptions
- achievements and user achievements
- gallery and club reviews
- platform events, activity feed, audit feed

## Quick Visual Checks

After seeding, verify quickly:

- Public pages: `/`, `/clips`, `/matches`, `/community/reviews`, `/gallery`
- Member pages (login with a member account): `/console/dashboard`, `/missions`, `/gifts`, `/shop`, `/duels`, `/quiz`, `/live-codes`, `/profile`
- Assistant/help pages: `/console/help`, `/console/assistant`
- Admin pages (login as admin): `/console/admin/dashboard`, `/console/admin/gifts`, `/console/admin/operations`
