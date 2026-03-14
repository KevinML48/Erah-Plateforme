<?php

namespace Tests\Feature\Web;

use App\Models\SocialAccount;
use App\Models\User;
use Database\Seeders\LeagueSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_update_profile_public_fields_and_avatar(): void
    {
        Storage::fake('public');
        $this->seed(LeagueSeeder::class);

        $user = User::factory()->create([
            'name' => 'Old Name',
            'bio' => null,
            'avatar_path' => null,
        ]);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'New Public Name',
            'bio' => 'Competitive FPS player.',
            'twitter_url' => 'https://x.com/newpublicname',
            'instagram_url' => 'https://instagram.com/newpublicname',
            'tiktok_url' => 'https://tiktok.com/@newpublicname',
            'discord_url' => 'https://discord.gg/newpublicname',
            'avatar' => UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg'),
        ]);

        $response->assertRedirect(route('profile.show'));
        $response->assertSessionHas('success');

        $user->refresh();

        $this->assertSame('New Public Name', $user->name);
        $this->assertSame('Competitive FPS player.', $user->bio);
        $this->assertSame('https://x.com/newpublicname', $user->twitter_url);
        $this->assertSame('https://instagram.com/newpublicname', $user->instagram_url);
        $this->assertSame('https://tiktok.com/@newpublicname', $user->tiktok_url);
        $this->assertSame('https://discord.gg/newpublicname', $user->discord_url);
        $this->assertNotNull($user->avatar_path);
        Storage::disk('public')->assertExists($user->avatar_path);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'profile.updated',
            'actor_id' => $user->id,
            'actor_type' => User::class,
        ]);
    }

    public function test_public_profile_page_displays_profile_links_and_bio(): void
    {
        $this->seed(LeagueSeeder::class);

        $viewer = User::factory()->create();
        $user = User::factory()->create([
            'name' => 'Public Player',
            'bio' => 'Bio visible publiquement.',
            'twitter_url' => 'https://x.com/publicplayer',
            'instagram_url' => 'https://instagram.com/publicplayer',
            'tiktok_url' => 'https://tiktok.com/@publicplayer',
            'discord_url' => 'https://discord.gg/publicplayer',
        ]);

        $response = $this->get(route('users.public', $user));

        $response->assertOk();
        $response->assertSee('Public Player');
        $response->assertSee('Bio visible publiquement.');
        $response->assertSee('https://x.com/publicplayer', false);
        $response->assertSee('https://instagram.com/publicplayer', false);
        $response->assertSee('https://tiktok.com/@publicplayer', false);
        $response->assertSee('https://discord.gg/publicplayer', false);

        $this->actingAs($viewer)->get(route('users.public', $user))
            ->assertOk()
            ->assertSee('Public Player');
    }

    public function test_profile_page_offers_a_real_discord_link_button(): void
    {
        $this->seed(LeagueSeeder::class);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('profile.show'));

        $response->assertOk();
        $response->assertSee('Discord public');
        $response->assertSee('Lier mon compte Discord');
        $response->assertSee('/auth/discord/redirect?intent=link&amp;return_route=profile.show', false);
    }

    public function test_profile_page_shows_when_discord_is_already_linked(): void
    {
        $this->seed(LeagueSeeder::class);

        $user = User::factory()->create();

        SocialAccount::query()->create([
            'user_id' => $user->id,
            'provider' => 'discord',
            'provider_user_id' => 'discord-profile-999',
            'email' => 'linked-discord@example.com',
            'avatar_url' => 'https://cdn.example.com/discord-profile.png',
        ]);

        $response = $this->actingAs($user)->get(route('profile.show'));

        $response->assertOk();
        $response->assertSee('Compte lie');
        $response->assertSee('Reconnecter Discord');
        $response->assertSee('linked-discord@example.com');
    }
}
