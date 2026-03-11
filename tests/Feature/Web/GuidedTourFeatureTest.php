<?php

namespace Tests\Feature\Web;

use App\Models\User;
use App\Services\GuidedTour\PlatformGuidedTourService;
use Database\Seeders\HelpCenterSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class GuidedTourFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_must_authenticate_before_using_guided_tour_endpoints(): void
    {
        $this->postJson(route('guided-tour.start'))
            ->assertUnauthorized();
    }

    public function test_help_center_shows_login_cta_for_guided_tour_when_user_is_guest(): void
    {
        $this->seed(HelpCenterSeeder::class);

        $this->get(route('help.index'))
            ->assertOk()
            ->assertSee('Se connecter pour lancer la visite');
    }

    public function test_help_center_does_not_crash_if_guided_tour_table_is_missing(): void
    {
        $this->seed(HelpCenterSeeder::class);

        $user = User::factory()->create([
            'role' => User::ROLE_USER,
        ]);

        Schema::drop('user_guided_tours');

        $this->actingAs($user)
            ->get(route('help.index'))
            ->assertOk()
            ->assertSee('Configuration requise')
            ->assertSee('La visite sera disponible des que la base est a jour.');
    }

    public function test_authenticated_user_can_start_pause_resume_and_complete_the_guided_tour(): void
    {
        $this->seed(HelpCenterSeeder::class);

        $user = User::factory()->create([
            'role' => User::ROLE_USER,
        ]);

        $stepsCount = count(app(PlatformGuidedTourService::class)->steps());

        $this->actingAs($user)
            ->postJson(route('guided-tour.start'))
            ->assertOk()
            ->assertJsonPath('data.state.status', 'in_progress')
            ->assertJsonPath('data.state.current_step_index', 0)
            ->assertJsonPath('data.state.is_paused', false);

        $this->actingAs($user)
            ->patchJson(route('guided-tour.update'), [
                'action' => 'next',
            ])
            ->assertOk()
            ->assertJsonPath('data.state.current_step_index', 1)
            ->assertJsonPath('data.state.progress_text', sprintf('Etape %d sur %d', 2, $stepsCount));

        $this->actingAs($user)
            ->patchJson(route('guided-tour.update'), [
                'action' => 'pause',
            ])
            ->assertOk()
            ->assertJsonPath('data.state.is_paused', true)
            ->assertJsonPath('data.state.primary_label', 'Reprendre la visite');

        $this->actingAs($user)
            ->get(route('help.index'))
            ->assertOk()
            ->assertSee('Reprendre la visite')
            ->assertSee(sprintf('Etape %d sur %d', 2, $stepsCount));

        $this->actingAs($user)
            ->patchJson(route('guided-tour.update'), [
                'action' => 'resume',
            ])
            ->assertOk()
            ->assertJsonPath('data.state.is_paused', false);

        for ($index = 2; $index <= $stepsCount; $index++) {
            $this->actingAs($user)
                ->patchJson(route('guided-tour.update'), [
                    'action' => 'next',
                ])
                ->assertOk();
        }

        $this->actingAs($user)
            ->get(route('help.index'))
            ->assertOk()
            ->assertSee('Visite terminee')
            ->assertSee('Revoir la visite');
    }

    public function test_authenticated_user_can_restart_the_guided_tour_from_help_center_state(): void
    {
        $this->seed(HelpCenterSeeder::class);

        $user = User::factory()->create([
            'role' => User::ROLE_USER,
        ]);

        $this->actingAs($user)->postJson(route('guided-tour.start'))->assertOk();
        $this->actingAs($user)->patchJson(route('guided-tour.update'), ['action' => 'next'])->assertOk();

        $this->actingAs($user)
            ->postJson(route('guided-tour.restart'))
            ->assertOk()
            ->assertJsonPath('data.state.current_step_index', 0)
            ->assertJsonPath('data.state.status', 'in_progress')
            ->assertJsonPath('data.state.is_paused', false);
    }

    public function test_dashboard_embeds_guided_tour_and_mission_live_toast_bootstraps(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_USER,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('mission-live-toast-data', false)
            ->assertSee('erah-guided-tour-data', false);
    }
}
