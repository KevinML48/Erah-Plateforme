<?php

namespace Tests\Feature\Web;

use App\Models\Clip;
use App\Models\ClipComment;
use App\Models\CommunityRewardGrant;
use App\Models\EsportMatch;
use App\Models\User;
use App\Models\UserProgress;
use Database\Seeders\LeagueSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestPlatformAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_consult_public_platform_pages(): void
    {
        $this->seed(LeagueSeeder::class);

        $member = User::factory()->create([
            'name' => 'Public Member',
            'bio' => 'Profil public consultable.',
        ]);

        UserProgress::query()->create([
            'user_id' => $member->id,
            'current_league_id' => null,
            'total_xp' => 0,
            'total_rank_points' => 0,
            'duel_score' => 120,
            'duel_wins' => 2,
            'duel_losses' => 1,
            'duel_current_streak' => 1,
            'duel_best_streak' => 2,
        ]);

        $clip = Clip::factory()->create([
            'title' => 'Clip public',
            'created_by' => $member->id,
        ]);

        ClipComment::query()->create([
            'clip_id' => $clip->id,
            'user_id' => $member->id,
            'parent_id' => null,
            'body' => 'Commentaire public visible.',
            'status' => ClipComment::STATUS_PUBLISHED,
        ]);

        $match = EsportMatch::factory()->create();

        $this->get(route('clips.index'))
            ->assertOk()
            ->assertSee('Clip public');

        $this->get(route('clips.show', $clip->slug))
            ->assertOk()
            ->assertSee('Clip public')
            ->assertSee('Commentaire public visible.');

        $this->get(route('matches.index'))
            ->assertOk()
            ->assertSee('ERAH Match Center');

        $this->get(route('matches.show', $match->id))
            ->assertOk()
            ->assertSee($match->displayTitle());

        $this->get(route('leaderboards.index'))
            ->assertOk();

        $this->get(route('statistics.index'))
            ->assertOk()
            ->assertSee('Statistiques');

        $this->get(route('duels.leaderboard'))
            ->assertOk()
            ->assertSee('Classement duel')
            ->assertSee(route('users.public', $member), false);

        $this->get(route('users.public', $member))
            ->assertOk()
            ->assertSee('Public Member');
    }

    public function test_guest_is_redirected_to_login_for_clip_and_bet_actions(): void
    {
        $clip = Clip::factory()->create();
        $match = EsportMatch::factory()->create();
        $loginUrl = route('login', ['required' => 'participation']);

        $this->post(route('clips.like', $clip->id))
            ->assertRedirect($loginUrl);

        $this->post(route('clips.comment', $clip->id), [
            'body' => 'Je veux participer',
        ])->assertRedirect($loginUrl);

        $this->post(route('matches.bets.store', $match->id), [
            'market_key' => 'winner',
            'selection_key' => 'team_a',
            'stake_points' => 100,
            'idempotency_key' => 'guest-test',
        ])->assertRedirect($loginUrl);
    }

    public function test_guest_cannot_access_account_only_pages(): void
    {
        $loginUrl = route('login', ['required' => 'participation']);

        $this->get(route('missions.index'))->assertRedirect($loginUrl);
        $this->get(route('duels.index'))->assertRedirect($loginUrl);
        $this->get(route('clips.favorites'))->assertRedirect($loginUrl);
        $this->get(route('shop.index'))->assertRedirect($loginUrl);
    }

    public function test_guest_viewing_a_clip_never_receives_rewards(): void
    {
        $clip = Clip::factory()->create();

        $this->get(route('clips.show', $clip->slug))
            ->assertOk();

        $this->assertDatabaseCount('community_reward_grants', 0);
        $this->assertSame(0, CommunityRewardGrant::query()->count());
    }

    public function test_login_page_displays_participation_message_when_required(): void
    {
        $this->get(route('login', ['required' => 'participation']))
            ->assertOk()
            ->assertSee('Creez un compte pour participer, gagner des points et progresser sur la plateforme.');
    }
}
