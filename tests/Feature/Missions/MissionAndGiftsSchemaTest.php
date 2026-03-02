<?php

namespace Tests\Feature\Missions;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MissionAndGiftsSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_mission_and_gift_tables_exist(): void
    {
        $this->assertTrue(Schema::hasTable('activity_events'));
        $this->assertTrue(Schema::hasTable('mission_templates'));
        $this->assertTrue(Schema::hasTable('mission_instances'));
        $this->assertTrue(Schema::hasTable('user_missions'));
        $this->assertTrue(Schema::hasTable('mission_completions'));
        $this->assertTrue(Schema::hasTable('user_reward_wallets'));
        $this->assertTrue(Schema::hasTable('reward_wallet_transactions'));
        $this->assertTrue(Schema::hasTable('gifts'));
        $this->assertTrue(Schema::hasTable('gift_redemptions'));
        $this->assertTrue(Schema::hasTable('gift_redemption_events'));
    }

    public function test_activity_events_unique_constraint_is_enforced(): void
    {
        $user = User::factory()->create();

        DB::table('activity_events')->insert([
            'user_id' => $user->id,
            'event_type' => 'clip_like',
            'ref_type' => 'clip',
            'ref_id' => '10',
            'occurred_at' => now(),
            'unique_key' => 'clip_like:'.$user->id.':10:2026-02-28',
            'metadata' => json_encode(['source' => 'test']),
            'created_at' => now(),
        ]);

        $this->expectException(QueryException::class);

        DB::table('activity_events')->insert([
            'user_id' => $user->id,
            'event_type' => 'clip_like',
            'ref_type' => 'clip',
            'ref_id' => '10',
            'occurred_at' => now(),
            'unique_key' => 'clip_like:'.$user->id.':10:2026-02-28',
            'metadata' => null,
            'created_at' => now(),
        ]);
    }
}
