# Launch Missions Catalog

## Source of truth

- Canonical source: `database/seeders/data/launch_missions.php`
- Seeder: `database/seeders/LaunchMissionCatalogSeeder.php`

The data file contains the 50 launch missions only.
Do not add hardcoded mission lists elsewhere.

## Commands

Seed or refresh the launch catalog only:

```bash
php artisan db:seed --class=Database\\Seeders\\LaunchMissionCatalogSeeder
```

Seed the launch catalog plus gifts and wallet preview data:

```bash
php artisan db:seed --class=Database\\Seeders\\MissionsAndGiftsSeeder
```

## Notes

- The seeder is idempotent: it upserts by stable `key`.
- Non-launch templates are deactivated to avoid a mixed demo/final catalog.
- Long progression missions are backfilled for existing users through real mission events:
  - `progress.level.reached`
  - `progress.rank.reached`
- Event-window missions use rolling launch dates resolved from the catalog source.

## How to adjust a mission later

1. Edit the mission entry in `database/seeders/data/launch_missions.php`.
2. Re-run `LaunchMissionCatalogSeeder`.
3. Verify in admin missions and on `/missions`.
