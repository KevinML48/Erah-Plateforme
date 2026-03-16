<?php

namespace Tests\Feature\Web;

use App\Models\SocialAccount;
use App\Models\User;
use App\Models\UserProfileCosmetic;
use App\Support\MediaStorage;
use Database\Seeders\LeagueSeeder;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_update_profile_public_fields_and_avatar(): void
    {
        config(['filesystems.media_disk' => 'public']);
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
        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk((string) config('filesystems.media_disk'));
        $storage->assertExists($user->avatar_path);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'profile.updated',
            'actor_id' => $user->id,
            'actor_type' => User::class,
        ]);
    }

    public function test_authenticated_user_can_store_avatar_on_s3_media_disk(): void
    {
        config(['filesystems.media_disk' => 's3']);
        Storage::fake('s3');
        $this->seed(LeagueSeeder::class);

        $user = User::factory()->create([
            'avatar_path' => null,
        ]);

        $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'S3 Avatar User',
            'bio' => 'Profil avec media disk S3.',
            'twitter_url' => '',
            'instagram_url' => '',
            'tiktok_url' => '',
            'discord_url' => '',
            'avatar' => UploadedFile::fake()->create('avatar-s3.png', 128, 'image/png'),
        ])->assertRedirect(route('profile.show'));

        $user->refresh();

        $this->assertNotNull($user->avatar_path);
        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk('s3');
        $storage->assertExists((string) $user->avatar_path);
    }

    public function test_avatar_url_falls_back_to_legacy_public_disk_when_media_disk_is_s3(): void
    {
        config(['filesystems.media_disk' => 's3']);
        Storage::fake('public');
        Storage::fake('s3');
        $this->seed(LeagueSeeder::class);

        Storage::disk('public')->put('avatars/legacy-member.png', 'avatar');

        $user = User::factory()->create([
            'avatar_path' => 'avatars/legacy-member.png',
        ]);

        $this->assertSame(route('media.public.file', ['path' => 'avatars/legacy-member.png']), $user->avatar_url);

        $this->actingAs($user)
            ->get(route('profile.show'))
            ->assertOk()
            ->assertSee(route('media.public.file', ['path' => 'avatars/legacy-member.png']), false);
    }

    public function test_profile_page_uses_placeholder_when_avatar_is_missing(): void
    {
        $this->seed(LeagueSeeder::class);

        $user = User::factory()->create([
            'avatar_path' => null,
        ]);

        $this->actingAs($user)
            ->get(route('profile.show'))
            ->assertOk()
            ->assertSee(MediaStorage::fallbackAvatarUrl(), false);
    }

    public function test_profile_page_uses_provider_avatar_when_no_uploaded_avatar_exists(): void
    {
        $this->seed(LeagueSeeder::class);

        $user = User::factory()->create([
            'avatar_path' => null,
            'provider_avatar_url' => 'https://cdn.example.com/provider-profile.png',
            'provider_avatar_provider' => 'google',
        ]);

        $this->assertSame('https://cdn.example.com/provider-profile.png', $user->avatar_url);

        $this->actingAs($user)
            ->get(route('profile.show'))
            ->assertOk()
            ->assertSee('https://cdn.example.com/provider-profile.png', false);
    }

    public function test_uploaded_avatar_has_priority_over_provider_avatar(): void
    {
        config(['filesystems.media_disk' => 'public']);
        Storage::fake('public');
        $this->seed(LeagueSeeder::class);

        Storage::disk('public')->put('avatars/custom-priority.png', 'avatar');

        $user = User::factory()->create([
            'avatar_path' => 'avatars/custom-priority.png',
            'provider_avatar_url' => 'https://cdn.example.com/provider-should-not-win.png',
            'provider_avatar_provider' => 'discord',
        ]);

        $expectedAvatarUrl = route('media.public.file', ['path' => 'avatars/custom-priority.png']);

        $this->assertSame($expectedAvatarUrl, $user->avatar_url);
        $this->assertSame($expectedAvatarUrl, $user->display_avatar_url);

        $this->actingAs($user)
            ->get(route('profile.show'))
            ->assertOk()
            ->assertSee($expectedAvatarUrl, false)
            ->assertDontSee('https://cdn.example.com/provider-should-not-win.png', false);
    }

    public function test_invalid_provider_avatar_falls_back_to_placeholder(): void
    {
        $user = User::factory()->create([
            'avatar_path' => null,
            'provider_avatar_url' => 'not-a-valid-avatar-url',
            'provider_avatar_provider' => 'google',
        ]);

        $this->assertNull($user->avatar_url);
        $this->assertSame(MediaStorage::fallbackAvatarUrl(), $user->display_avatar_url);
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
        $response->assertSee('https://cdn.example.com/discord-profile.png', false);
    }

    public function test_profile_page_lists_owned_cosmetics_and_allows_equipping_them(): void
    {
        $this->seed(LeagueSeeder::class);

        $user = User::factory()->create([
            'equipped_profile_badge' => 'launch_badge_exclusive',
        ]);

        UserProfileCosmetic::query()->create([
            'user_id' => $user->id,
            'slot' => 'badge',
            'cosmetic_key' => 'launch_badge_exclusive',
            'metadata' => [
                'label' => 'Badge exclusif ERAH',
                'description' => 'Badge exclusif de lancement.',
                'preview' => [
                    'pill_background' => 'linear-gradient(135deg, #7f1d1d, #ef4444)',
                    'pill_color' => '#fff1f2',
                ],
            ],
        ]);

        $alternativeBadge = UserProfileCosmetic::query()->create([
            'user_id' => $user->id,
            'slot' => 'badge',
            'cosmetic_key' => 'launch_badge_champion',
            'metadata' => [
                'label' => 'Badge champion',
                'description' => 'Badge alternatif equipeable.',
            ],
        ]);

        $response = $this->actingAs($user)->get(route('profile.show'));

        $response->assertOk();
        $response->assertSee('Objets de profil');
        $response->assertSee('Badge exclusif ERAH');
        $response->assertSee('Badge champion');
        $response->assertSee('Equiper');

        $this->actingAs($user)
            ->post(route('profile.cosmetics.equip', $alternativeBadge))
            ->assertRedirect();

        $user->refresh();
        $this->assertSame('launch_badge_champion', $user->equipped_profile_badge);
    }

    public function test_public_profile_displays_active_profile_cosmetics(): void
    {
        $this->seed(LeagueSeeder::class);

        $user = User::factory()->create([
            'name' => 'Cosmetic Player',
            'equipped_profile_badge' => 'launch_badge_exclusive',
            'equipped_profile_title' => 'launch_title_exclusive',
            'equipped_profile_theme' => 'launch_profile_theme_premium',
            'profile_featured_until' => now()->addDays(4),
        ]);

        UserProfileCosmetic::query()->create([
            'user_id' => $user->id,
            'slot' => 'badge',
            'cosmetic_key' => 'launch_badge_exclusive',
            'metadata' => [
                'label' => 'Badge exclusif ERAH',
                'description' => 'Badge exclusif de lancement.',
                'preview' => [
                    'pill_background' => 'linear-gradient(135deg, #7f1d1d, #ef4444)',
                    'pill_color' => '#fff1f2',
                ],
            ],
        ]);

        UserProfileCosmetic::query()->create([
            'user_id' => $user->id,
            'slot' => 'profile_title',
            'cosmetic_key' => 'launch_title_exclusive',
            'metadata' => [
                'label' => 'Membre prestige',
                'description' => 'Titre exclusif de lancement.',
            ],
        ]);

        UserProfileCosmetic::query()->create([
            'user_id' => $user->id,
            'slot' => 'profile_theme',
            'cosmetic_key' => 'launch_profile_theme_premium',
            'expires_at' => now()->addDays(30),
            'metadata' => [
                'label' => 'Theme premium ERAH',
                'description' => 'Theme premium actif 30 jours.',
            ],
        ]);

        $response = $this->get(route('users.public', $user));

        $response->assertOk();
        $response->assertSee('Cosmetic Player');
        $response->assertSee('Badge exclusif ERAH');
        $response->assertSee('Membre prestige');
        $response->assertSee('Theme premium actif');
        $response->assertSee('Profil en avant jusqu au');
    }

    public function test_expired_profile_cosmetic_is_not_rendered_as_active(): void
    {
        $this->seed(LeagueSeeder::class);

        $user = User::factory()->create([
            'name' => 'Expired Cosmetic Player',
            'equipped_profile_theme' => 'launch_profile_theme_premium',
        ]);

        UserProfileCosmetic::query()->create([
            'user_id' => $user->id,
            'slot' => 'profile_theme',
            'cosmetic_key' => 'launch_profile_theme_premium',
            'expires_at' => now()->subDay(),
            'metadata' => [
                'label' => 'Theme premium ERAH',
                'description' => 'Theme premium actif 30 jours.',
            ],
        ]);

        $this->actingAs($user)
            ->get(route('profile.show'))
            ->assertOk()
            ->assertDontSee('Theme profil actif');

        $this->get(route('users.public', $user))
            ->assertOk()
            ->assertDontSee('Theme premium actif');
    }
}
