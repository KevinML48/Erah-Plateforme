<?php

namespace Tests\Feature\Web;

use App\Models\ClubReview;
use App\Models\User;
use Database\Seeders\ClubReviewSeeder;
use Database\Seeders\LeagueSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClubReviewFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_review_from_profile(): void
    {
        $this->seed(LeagueSeeder::class);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('profile.show'))
            ->post(route('profile.reviews.store'), [
                'content' => 'ERAH propose une plateforme claire, motivante et agreable a utiliser chaque semaine.',
            ]);

        $response->assertRedirect(route('profile.show'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('club_reviews', [
            'user_id' => $user->id,
            'status' => ClubReview::STATUS_PUBLISHED,
            'source' => ClubReview::SOURCE_MEMBER,
        ]);
    }

    public function test_user_cannot_create_multiple_active_reviews(): void
    {
        $user = User::factory()->create();

        ClubReview::factory()->member($user)->create([
            'content' => 'Premier avis membre tres positif sur le club.',
        ]);

        $this->actingAs($user)->post(route('profile.reviews.store'), [
            'content' => 'Nouvelle version plus complete de mon avis membre sur le club et la plateforme.',
        ])->assertRedirect();

        $this->assertSame(1, ClubReview::query()->where('user_id', $user->id)->count());
        $this->assertDatabaseHas('club_reviews', [
            'user_id' => $user->id,
            'content' => 'Nouvelle version plus complete de mon avis membre sur le club et la plateforme.',
        ]);
    }

    public function test_user_can_update_review(): void
    {
        $user = User::factory()->create();
        $review = ClubReview::factory()->member($user)->create([
            'content' => 'Avis initial a retravailler pour le club.',
        ]);

        $this->actingAs($user)->put(route('profile.reviews.update'), [
            'content' => 'Avis finalise apres quelques semaines de plus sur la plateforme ERAH.',
        ])->assertRedirect();

        $review->refresh();

        $this->assertSame('Avis finalise apres quelques semaines de plus sur la plateforme ERAH.', $review->content);
        $this->assertSame(ClubReview::STATUS_PUBLISHED, $review->status);
        $this->assertNotNull($review->published_at);
    }

    public function test_only_published_reviews_are_visible_publicly(): void
    {
        ClubReview::factory()->create([
            'author_name' => 'Visible',
            'content' => 'Avis visible publiquement sur le site.',
            'status' => ClubReview::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        ClubReview::factory()->hidden()->create([
            'author_name' => 'Cache',
            'content' => 'Avis masque qui ne doit pas sortir.',
        ]);

        ClubReview::factory()->draft()->create([
            'author_name' => 'Brouillon',
            'content' => 'Avis brouillon non public.',
        ]);

        $response = $this->get(route('reviews.index'));

        $response->assertOk();
        $response->assertSee('Avis visible publiquement sur le site.');
        $response->assertDontSee('Avis masque qui ne doit pas sortir.');
        $response->assertDontSee('Avis brouillon non public.');
    }

    public function test_home_displays_only_five_latest_published_reviews(): void
    {
        foreach (range(1, 6) as $index) {
            ClubReview::factory()->create([
                'author_name' => 'Auteur '.$index,
                'content' => 'Avis public numero '.$index,
                'status' => ClubReview::STATUS_PUBLISHED,
                'published_at' => now()->subMinutes(6 - $index),
            ]);
        }

        $response = $this->get(route('marketing.index'));

        $response->assertOk();
        $response->assertSee('Avis public numero 6');
        $response->assertSee('Avis public numero 5');
        $response->assertSee('Avis public numero 4');
        $response->assertSee('Avis public numero 3');
        $response->assertSee('Avis public numero 2');
        $response->assertDontSee('Avis public numero 1');
        $response->assertSee(route('reviews.index'));
    }

    public function test_home_falls_back_to_legacy_reviews_when_database_is_empty(): void
    {
        $response = $this->get(route('marketing.index'));

        $response->assertOk();
        $response->assertSee('Hermes_vlr');
        $response->assertSee('Pikali');
        $response->assertSee('Voir tous les avis');
    }

    public function test_reviews_page_is_paginated(): void
    {
        foreach (range(1, 14) as $index) {
            ClubReview::factory()->create([
                'author_name' => 'Auteur '.$index,
                'content' => 'Avis pagine '.$index,
                'status' => ClubReview::STATUS_PUBLISHED,
                'published_at' => now()->subMinutes(15 - $index),
            ]);
        }

        $this->get(route('reviews.index'))
            ->assertOk()
            ->assertSee('Avis pagine 14')
            ->assertDontSee('Avis pagine 2');

        $this->get(route('reviews.index', ['page' => 2]))
            ->assertOk()
            ->assertSee('Avis pagine 2');
    }

    public function test_admin_can_hide_review(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $review = ClubReview::factory()->create([
            'status' => ClubReview::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $response = $this->actingAs($admin)->put(route('admin.reviews.update', $review), [
            'status' => ClubReview::STATUS_HIDDEN,
            'is_featured' => 0,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('club_reviews', [
            'id' => $review->id,
            'status' => ClubReview::STATUS_HIDDEN,
        ]);
    }

    public function test_seeded_reviews_are_inserted(): void
    {
        $this->seed(ClubReviewSeeder::class);

        $this->assertSame(8, ClubReview::query()->count());
        $this->assertDatabaseHas('club_reviews', [
            'author_name' => 'Hermes_vlr',
            'source' => ClubReview::SOURCE_SEED,
            'status' => ClubReview::STATUS_PUBLISHED,
        ]);
    }

    public function test_member_review_contains_link_to_public_profile(): void
    {
        $this->seed(LeagueSeeder::class);

        $user = User::factory()->create([
            'name' => 'Public Reviewer',
        ]);

        ClubReview::factory()->member($user)->create([
            'content' => 'Avis membre lie a un vrai profil public de la plateforme.',
            'status' => ClubReview::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $response = $this->get(route('reviews.index'));

        $response->assertOk();
        $response->assertSee(route('users.public', $user), false);

        $this->get(route('users.public', $user))
            ->assertOk()
            ->assertSee('Public Reviewer');
    }
}
