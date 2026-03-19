<?php

namespace Tests\Feature\Web;

use App\Models\Duel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DuelsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_duel_create_page_shows_opponent_name_without_exposing_email(): void
    {
        $authUser = User::factory()->create([
            'name' => 'Alpha Owner',
            'email' => 'alpha.owner@example.test',
        ]);

        $visibleOpponent = User::factory()->create([
            'name' => 'Beta Rival',
            'email' => 'beta.rival@example.test',
        ]);

        $hiddenOpponent = User::factory()->create([
            'name' => 'Gamma Other',
            'email' => 'gamma.other@example.test',
        ]);

        $response = $this->actingAs($authUser)->get(route('duels.create', ['q' => 'Beta']));

        $response->assertOk()
            ->assertSeeText('Beta Rival')
            ->assertSeeText('Membre ERAH')
            ->assertDontSee('beta.rival@example.test', false)
            ->assertDontSeeText('Gamma Other');

        $this->assertSame($visibleOpponent->id, $response->viewData('users')->sole()->id);
        $this->assertFalse(isset($response->viewData('users')->first()->email));
        $this->assertNotSame($hiddenOpponent->id, $response->viewData('users')->sole()->id);
    }

    public function test_duels_page_expires_overdue_pending_duels_before_rendering_counts(): void
    {
        $challenger = User::factory()->create();
        $challenged = User::factory()->create();

        $duel = Duel::factory()->create([
            'challenger_id' => $challenger->id,
            'challenged_id' => $challenged->id,
            'status' => Duel::STATUS_PENDING,
            'requested_at' => now()->subHour(),
            'expires_at' => now()->subMinute(),
        ]);

        $response = $this->actingAs($challenged)->get(route('duels.index', ['status' => 'pending']));

        $response->assertOk()
            ->assertViewHas('statusCounts', fn (array $counts): bool => $counts['pending'] === 0 && $counts['finished'] === 1)
            ->assertViewHas('summary', fn (array $summary): bool => $summary['needs_response'] === 0);

        $duel->refresh();
        $this->assertSame(Duel::STATUS_EXPIRED, $duel->status);
    }
}
