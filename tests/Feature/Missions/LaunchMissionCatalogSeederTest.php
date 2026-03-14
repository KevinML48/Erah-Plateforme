<?php

namespace Tests\Feature\Missions;

use App\Models\MissionTemplate;
use App\Models\User;
use App\Services\MissionCatalogService;
use App\Support\MissionEventTypeRegistry;
use Database\Seeders\LaunchMissionCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaunchMissionCatalogSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_launch_catalog_seeder_injects_exactly_fifty_active_missions_with_unique_keys(): void
    {
        $this->seed(LaunchMissionCatalogSeeder::class);

        $activeTemplates = MissionTemplate::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $this->assertCount(50, $activeTemplates);
        $this->assertCount(50, $activeTemplates->pluck('key')->unique());
    }

    public function test_launch_catalog_rewards_only_use_xp_and_points_payload(): void
    {
        $this->seed(LaunchMissionCatalogSeeder::class);

        MissionTemplate::query()
            ->where('is_active', true)
            ->get()
            ->each(function (MissionTemplate $template): void {
                $this->assertSame(['xp', 'points'], array_keys($template->rewards));
                $this->assertGreaterThanOrEqual(0, (int) ($template->rewards['xp'] ?? -1));
                $this->assertGreaterThanOrEqual(0, (int) ($template->rewards['points'] ?? -1));
            });
    }

    public function test_launch_catalog_only_uses_supported_event_types_and_valid_event_windows(): void
    {
        $this->seed(LaunchMissionCatalogSeeder::class);

        MissionTemplate::query()
            ->where('is_active', true)
            ->get()
            ->each(function (MissionTemplate $template): void {
                $this->assertTrue(MissionEventTypeRegistry::isSupported($template->event_type), 'Unsupported event type for '.$template->key);

                if ($template->scope === MissionTemplate::SCOPE_EVENT_WINDOW) {
                    $this->assertNotNull($template->start_at, 'Missing start_at for '.$template->key);
                    $this->assertNotNull($template->end_at, 'Missing end_at for '.$template->key);
                    $this->assertTrue($template->start_at->lte($template->end_at), 'Invalid event window for '.$template->key);
                }
            });

        $this->assertSame(
            5,
            MissionTemplate::query()->where('is_active', true)->where('scope', MissionTemplate::SCOPE_EVENT_WINDOW)->count()
        );
    }

    public function test_launch_catalog_is_idempotent_and_discovery_missions_stay_first(): void
    {
        $this->seed(LaunchMissionCatalogSeeder::class);
        $this->seed(LaunchMissionCatalogSeeder::class);

        $orderedKeys = MissionTemplate::query()
            ->where('is_active', true)
            ->orderByDesc('is_discovery')
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->pluck('key')
            ->take(8)
            ->all();

        $this->assertSame([
            'launch.first-step',
            'launch.profile-operational',
            'launch.first-mission-followed',
            'launch.first-focus',
            'launch.first-clip-support',
            'launch.first-bet',
            'launch.first-duel',
            'launch.first-reward',
        ], $orderedKeys);
    }

    public function test_progression_missions_are_bound_to_level_and_rank_events(): void
    {
        $this->seed(LaunchMissionCatalogSeeder::class);

        $templates = MissionTemplate::query()
            ->whereIn('key', ['launch.level-5', 'launch.level-10', 'launch.level-15', 'launch.rank-argent', 'launch.rank-platine'])
            ->get()
            ->keyBy('key');

        $this->assertSame('progress.level.reached', $templates['launch.level-5']->event_type);
        $this->assertSame('progress.level.reached', $templates['launch.level-10']->event_type);
        $this->assertSame('progress.level.reached', $templates['launch.level-15']->event_type);
        $this->assertSame('progress.rank.reached', $templates['launch.rank-argent']->event_type);
        $this->assertSame('progress.rank.reached', $templates['launch.rank-platine']->event_type);
    }

    public function test_catalog_payload_surfaces_discovery_cards_first_for_user(): void
    {
        $this->seed(LaunchMissionCatalogSeeder::class);

        $user = User::factory()->create();
        $payload = app(MissionCatalogService::class)->dashboardPayload($user);

        $this->assertNotEmpty($payload['discovery']);
        $this->assertTrue($payload['discovery'][0]['is_discovery']);
        $this->assertContains($payload['discovery'][0]['key'], [
            'launch.first-step',
            'launch.profile-operational',
            'launch.first-mission-followed',
            'launch.first-focus',
            'launch.first-clip-support',
            'launch.first-bet',
            'launch.first-duel',
            'launch.first-reward',
        ]);
    }
}
