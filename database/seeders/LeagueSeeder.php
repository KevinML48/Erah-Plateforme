<?php

namespace Database\Seeders;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\League;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeagueSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $definitions = [
                ['key' => 'bronze', 'name' => 'Bronze', 'min_rank_points' => 0, 'sort_order' => 1],
                ['key' => 'argent', 'name' => 'Argent', 'min_rank_points' => 100, 'sort_order' => 2],
                ['key' => 'or', 'name' => 'Or', 'min_rank_points' => 250, 'sort_order' => 3],
                ['key' => 'platine', 'name' => 'Platine', 'min_rank_points' => 500, 'sort_order' => 4],
                ['key' => 'diamant', 'name' => 'Diamant', 'min_rank_points' => 900, 'sort_order' => 5],
                ['key' => 'master', 'name' => 'Master', 'min_rank_points' => 1400, 'sort_order' => 6],
            ];

            foreach ($definitions as $definition) {
                League::query()->updateOrCreate(
                    ['key' => $definition['key']],
                    [
                        'name' => $definition['name'],
                        'min_rank_points' => $definition['min_rank_points'],
                        'sort_order' => $definition['sort_order'],
                        'is_active' => true,
                    ]
                );
            }

            app(StoreAuditLogAction::class)->execute(
                action: 'seed.leagues.upserted',
                actor: null,
                target: null,
                context: [
                    'count' => count($definitions),
                    'seed_class' => self::class,
                ],
            );
        });
    }
}
