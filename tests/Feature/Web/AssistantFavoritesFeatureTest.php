<?php

namespace Tests\Feature\Web;

use App\Models\AssistantFavorite;
use App\Models\User;
use Database\Seeders\LeagueSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssistantFavoritesFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function favoritePayload(): array
    {
        return [
            'question' => 'Comment gagner des points ?',
            'answer' => 'Les missions quotidiennes restent le levier le plus direct pour progresser.',
            'details' => [
                'Commencez par vos missions actives.',
            ],
            'sources' => [
                [
                    'type' => 'article',
                    'title' => 'Gagner des points avec les missions quotidiennes',
                    'url' => route('help.assistant.page'),
                    'category' => 'Missions',
                ],
            ],
            'next_steps' => [
                'Voir les missions',
            ],
        ];
    }

    public function test_authenticated_user_can_save_assistant_response_to_favorites(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('assistant.favorites.store'), $this->favoritePayload())
            ->assertOk()
            ->assertJsonPath('data.favorite.question', 'Comment gagner des points ?')
            ->assertJsonPath('data.created', true);

        $this->assertDatabaseHas('assistant_favorites', [
            'user_id' => $user->id,
            'question' => 'Comment gagner des points ?',
        ]);
    }

    public function test_same_assistant_response_is_not_duplicated_in_favorites(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson(route('assistant.favorites.store'), $this->favoritePayload())->assertOk();
        $this->actingAs($user)->postJson(route('assistant.favorites.store'), $this->favoritePayload())
            ->assertOk()
            ->assertJsonPath('data.created', false);

        $this->assertDatabaseCount('assistant_favorites', 1);
    }

    public function test_profile_page_displays_saved_assistant_favorites(): void
    {
        $this->seed(LeagueSeeder::class);

        $user = User::factory()->create();
        AssistantFavorite::query()->create([
            'user_id' => $user->id,
            'fingerprint' => hash('sha256', 'favorite-1'),
            'question' => 'Comment suivre les matchs ?',
            'answer' => 'Ouvrez le module Matchs pour voir les rencontres a venir.',
            'details' => ['Les statuts et horaires y sont centralises.'],
            'sources' => [],
            'next_steps' => ['Voir les matchs'],
        ]);

        $this->actingAs($user)
            ->get(route('profile.show'))
            ->assertOk()
            ->assertSee('Mes reponses favorites')
            ->assertSee('Comment suivre les matchs ?')
            ->assertSee('Ouvrez le module Matchs pour voir les rencontres a venir.');
    }

    public function test_user_can_delete_own_assistant_favorite(): void
    {
        $user = User::factory()->create();
        $favorite = AssistantFavorite::query()->create([
            'user_id' => $user->id,
            'fingerprint' => hash('sha256', 'favorite-delete'),
            'question' => 'Question favorite',
            'answer' => 'Reponse favorite',
        ]);

        $this->actingAs($user)
            ->from(route('profile.show'))
            ->delete(route('assistant.favorites.destroy', $favorite))
            ->assertRedirect(route('profile.show'));

        $this->assertDatabaseMissing('assistant_favorites', [
            'id' => $favorite->id,
        ]);
    }

    public function test_user_cannot_delete_another_users_assistant_favorite(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $favorite = AssistantFavorite::query()->create([
            'user_id' => $owner->id,
            'fingerprint' => hash('sha256', 'favorite-private'),
            'question' => 'Question privee',
            'answer' => 'Reponse privee',
        ]);

        $this->actingAs($intruder)
            ->deleteJson(route('assistant.favorites.destroy', $favorite))
            ->assertNotFound();

        $this->assertDatabaseHas('assistant_favorites', [
            'id' => $favorite->id,
            'user_id' => $owner->id,
        ]);
    }
}
