<?php

namespace Database\Seeders;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\League;
use App\Models\UserProgress;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeagueSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $definitions = collect((array) config('community.xp_leagues', []))
                ->values()
                ->map(fn (array $definition, int $index): array => [
                    'key' => (string) ($definition['key'] ?? 'bronze'),
                    'name' => (string) ($definition['name'] ?? 'Bronze'),
                    'min_rank_points' => max(0, (int) ($definition['xp_threshold'] ?? 0)),
                    'sort_order' => $index + 1,
                ])
                ->values();

            // Preserve existing league IDs when possible, while freeing canonical sort_order slots.
            League::query()->update(['sort_order' => DB::raw('sort_order + 1000')]);

            $activeLeagueIds = [];

            foreach ($definitions as $definition) {
                $league = League::query()
                    ->where('key', $definition['key'])
                    ->first();

                if (! $league) {
                    $league = League::query()
                        ->where('sort_order', $definition['sort_order'] + 1000)
                        ->first();
                }

                if (! $league) {
                    $league = new League();
                }

                $league->fill([
                    'key' => $definition['key'],
                    'name' => $definition['name'],
                    'min_rank_points' => $definition['min_rank_points'],
                    'sort_order' => $definition['sort_order'],
                    'is_active' => true,
                ]);
                $league->save();

                $activeLeagueIds[] = (int) $league->id;
            }

            League::query()
                ->whereNotIn('id', $activeLeagueIds)
                ->update(['is_active' => false]);

            $activeLeagues = League::query()
                ->whereIn('id', $activeLeagueIds)
                ->orderBy('min_rank_points')
                ->get(['id', 'min_rank_points']);

            if ($activeLeagues->isNotEmpty()) {
                UserProgress::query()
                    ->orderBy('user_id')
                    ->chunkById(500, function ($progressRows) use ($activeLeagues): void {
                        foreach ($progressRows as $progress) {
                            $xp = (int) $progress->total_xp;
                            $targetLeagueId = (int) $activeLeagues->first()->id;

                            foreach ($activeLeagues as $league) {
                                if ($xp < (int) $league->min_rank_points) {
                                    break;
                                }

                                $targetLeagueId = (int) $league->id;
                            }

                            if ((int) $progress->current_league_id !== $targetLeagueId) {
                                $progress->current_league_id = $targetLeagueId;
                                $progress->save();
                            }
                        }
                    }, 'user_id');
            }

            app(StoreAuditLogAction::class)->execute(
                action: 'seed.leagues.upserted',
                actor: null,
                target: null,
                context: [
                    'count' => $definitions->count(),
                    'seed_class' => self::class,
                ],
            );
        });
    }
}
