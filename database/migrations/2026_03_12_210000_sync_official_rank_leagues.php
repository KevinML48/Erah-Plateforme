<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('leagues')) {
            return;
        }

        $definitions = [
            ['key' => 'bronze', 'name' => 'Bronze', 'min_rank_points' => 0, 'sort_order' => 1],
            ['key' => 'argent', 'name' => 'Argent', 'min_rank_points' => 1000, 'sort_order' => 2],
            ['key' => 'gold', 'name' => 'Gold', 'min_rank_points' => 3000, 'sort_order' => 3],
            ['key' => 'platine', 'name' => 'Platine', 'min_rank_points' => 7000, 'sort_order' => 4],
            ['key' => 'diamant', 'name' => 'Diamant', 'min_rank_points' => 15000, 'sort_order' => 5],
            ['key' => 'champion', 'name' => 'Champion', 'min_rank_points' => 30000, 'sort_order' => 6],
            ['key' => 'erah-prime', 'name' => 'ERAH Prime', 'min_rank_points' => 60000, 'sort_order' => 7],
        ];

        DB::transaction(function () use ($definitions): void {
            DB::table('leagues')->update([
                'sort_order' => DB::raw('sort_order + 1000'),
                'updated_at' => now(),
            ]);

            $canonicalLeagueIds = [];

            foreach ($definitions as $definition) {
                $existing = DB::table('leagues')
                    ->where('key', $definition['key'])
                    ->first();

                if (! $existing) {
                    $existing = DB::table('leagues')
                        ->where('sort_order', $definition['sort_order'] + 1000)
                        ->first();
                }

                if ($existing) {
                    DB::table('leagues')
                        ->where('id', $existing->id)
                        ->update([
                            'key' => $definition['key'],
                            'name' => $definition['name'],
                            'min_rank_points' => $definition['min_rank_points'],
                            'sort_order' => $definition['sort_order'],
                            'is_active' => true,
                            'updated_at' => now(),
                        ]);

                    $canonicalLeagueIds[] = (int) $existing->id;
                    continue;
                }

                $canonicalLeagueIds[] = (int) DB::table('leagues')->insertGetId([
                    'key' => $definition['key'],
                    'name' => $definition['name'],
                    'min_rank_points' => $definition['min_rank_points'],
                    'sort_order' => $definition['sort_order'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('leagues')
                ->whereNotIn('id', $canonicalLeagueIds)
                ->update([
                    'is_active' => false,
                    'updated_at' => now(),
                ]);

            if (! Schema::hasTable('user_progress')) {
                return;
            }

            $leagues = DB::table('leagues')
                ->whereIn('id', $canonicalLeagueIds)
                ->orderBy('min_rank_points')
                ->get(['id', 'min_rank_points']);

            if ($leagues->isEmpty()) {
                return;
            }

            DB::table('user_progress')
                ->orderBy('user_id')
                ->chunkById(500, function ($progressRows) use ($leagues): void {
                    foreach ($progressRows as $progress) {
                        $xp = (int) $progress->total_xp;
                        $targetLeagueId = (int) $leagues->first()->id;

                        foreach ($leagues as $league) {
                            if ($xp < (int) $league->min_rank_points) {
                                break;
                            }

                            $targetLeagueId = (int) $league->id;
                        }

                        if ((int) $progress->current_league_id === $targetLeagueId) {
                            continue;
                        }

                        DB::table('user_progress')
                            ->where('user_id', $progress->user_id)
                            ->update(['current_league_id' => $targetLeagueId]);
                    }
                }, 'user_id');
        });
    }

    public function down(): void
    {
        // Data migration intentionally not reversed.
    }
};

