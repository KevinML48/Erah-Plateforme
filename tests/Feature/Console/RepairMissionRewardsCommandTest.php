<?php

namespace Tests\Feature\Console;

use App\Models\CommunityRewardGrant;
use App\Models\MissionCompletion;
use App\Models\MissionInstance;
use App\Models\MissionTemplate;
use App\Models\User;
use App\Models\UserMission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepairMissionRewardsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_repairs_completed_mission_reward_without_duplicate_grant(): void
    {
        $user = User::factory()->create();
        $template = MissionTemplate::query()->create([
            'key' => 'mission.repair.reward',
            'title' => 'Repair me',
            'event_type' => 'clip.comment',
            'target_count' => 1,
            'scope' => MissionTemplate::SCOPE_ONCE,
            'rewards' => ['xp' => 90, 'points' => 55],
            'is_active' => true,
        ]);

        $instance = MissionInstance::query()->create([
            'mission_template_id' => $template->id,
            'period_start' => now()->subDay(),
            'period_end' => now()->addDay(),
        ]);

        $mission = UserMission::query()->create([
            'user_id' => $user->id,
            'mission_instance_id' => $instance->id,
            'progress_count' => 1,
            'completed_at' => now()->subMinute(),
            'rewarded_at' => null,
            'claimed_at' => null,
        ]);

        $this->artisan('erah:repair-mission-rewards', [
            '--user-id' => $user->id,
        ])->assertExitCode(0);

        $mission->refresh();

        $this->assertNotNull($mission->rewarded_at);
        $this->assertNotNull($mission->claimed_at);
        $this->assertDatabaseHas('mission_completions', [
            'user_id' => $user->id,
            'user_mission_id' => $mission->id,
        ]);
        $this->assertSame(1, CommunityRewardGrant::query()->where('dedupe_key', 'mission.completion.'.$mission->id)->count());

        $this->artisan('erah:repair-mission-rewards', [
            '--user-id' => $user->id,
        ])->assertExitCode(0);

        $this->assertSame(1, CommunityRewardGrant::query()->where('dedupe_key', 'mission.completion.'.$mission->id)->count());
        $this->assertSame(1, MissionCompletion::query()->where('user_mission_id', $mission->id)->count());
    }
}