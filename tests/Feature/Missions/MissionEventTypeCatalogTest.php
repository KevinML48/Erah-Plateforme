<?php

namespace Tests\Feature\Missions;

use App\Models\MissionTemplate;
use App\Services\GrantMonthlySupporterRewards;
use App\Support\MissionEventTypeRegistry;
use Database\Seeders\MissionsAndGiftsSeeder;
use Database\Seeders\SupporterProgramSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MissionEventTypeCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_mission_templates_only_use_supported_event_types(): void
    {
        $this->seed([
            MissionsAndGiftsSeeder::class,
            SupporterProgramSeeder::class,
        ]);

        app(GrantMonthlySupporterRewards::class)->execute(now());

        $eventTypes = MissionTemplate::query()
            ->pluck('event_type')
            ->map(fn (string $eventType): string => MissionTemplate::normalizeEventType($eventType))
            ->unique()
            ->values();

        $unsupported = $eventTypes
            ->reject(fn (string $eventType): bool => MissionEventTypeRegistry::isSupported($eventType))
            ->values();

        $this->assertSame(
            [],
            $unsupported->all(),
            'Unsupported mission event type(s): '.implode(', ', $unsupported->all())
        );
    }
}

