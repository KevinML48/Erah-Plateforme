<?php

namespace Tests\Feature\Web;

use App\Models\Clip;
use App\Models\ClubReview;
use App\Models\User;
use Database\Seeders\LeagueSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicProfileModerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_moderate_profile_directly_from_public_profile(): void
    {
        Storage::fake('public');
        $this->seed(LeagueSeeder::class);

        Storage::disk('public')->put('avatars/member.png', 'avatar');

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user = User::factory()->create([
            'name' => 'Pseudo Douteux',
            'bio' => 'Bio initiale.',
            'avatar_path' => 'avatars/member.png',
            'twitter_url' => 'https://x.com/douteux',
            'instagram_url' => 'https://instagram.com/douteux',
        ]);

        ClubReview::factory()->member($user)->create([
            'content' => 'Avis vraiment douteux a masquer.',
            'status' => ClubReview::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('users.public', $user))
            ->assertOk()
            ->assertSee('Moderation admin')
            ->assertSee('Controle du profil')
            ->assertSee('Suppression du compte');

        $response = $this->actingAs($admin)->put(route('admin.users.public-profile.update', $user), [
            'name' => 'Pseudo Corrige',
            'bio' => 'Bio nettoyee par la moderation.',
            'twitter_url' => 'https://x.com/corrige',
            'instagram_url' => 'https://instagram.com/corrige',
            'tiktok_url' => '',
            'discord_url' => '',
            'remove_avatar' => '1',
            'clear_social_links' => '1',
            'review_status' => ClubReview::STATUS_HIDDEN,
            'delete_review' => '0',
        ]);

        $response
            ->assertRedirect(route('users.public', $user))
            ->assertSessionHas('success');

        $user->refresh();
        $review = $user->clubReview()->first();

        $this->assertSame('Pseudo Corrige', $user->name);
        $this->assertSame('Bio nettoyee par la moderation.', $user->bio);
        $this->assertNull($user->avatar_path);
        $this->assertNull($user->twitter_url);
        $this->assertNull($user->instagram_url);
        $this->assertNull($user->tiktok_url);
        $this->assertNull($user->discord_url);
        $this->assertSame(ClubReview::STATUS_HIDDEN, $review?->status);
        Storage::disk('public')->assertMissing('avatars/member.png');

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'users.profile_moderated',
            'actor_id' => $admin->id,
            'target_id' => $user->id,
        ]);
    }

    public function test_admin_can_delete_account_from_public_profile_and_reassign_authored_content(): void
    {
        $this->seed(LeagueSeeder::class);

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user = User::factory()->create(['name' => 'Compte A Supprimer']);

        $clip = Clip::factory()->create([
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        ClubReview::factory()->member($user)->create([
            'content' => 'Avis a retirer avec le compte.',
            'status' => ClubReview::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.users.public-profile.destroy', $user), [
            'confirmation_name' => 'Compte A Supprimer',
        ]);

        $response
            ->assertRedirect(route('users.index', ['q' => 'Compte A Supprimer']))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('club_reviews', ['user_id' => $user->id]);
        $this->assertDatabaseHas('clips', [
            'id' => $clip->id,
            'created_by' => $admin->id,
            'updated_by' => null,
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'users.deleted_by_admin',
            'actor_id' => $admin->id,
            'target_id' => $user->id,
        ]);
    }
}
