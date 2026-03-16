<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_email_and_password(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'player@example.com',
            'password' => 'secret-pass-123',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'player@example.com',
            'password' => 'secret-pass-123',
            'device_name' => 'phpunit',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'token_type',
                'access_token',
                'me_endpoint',
                'user' => ['id', 'name', 'email', 'role'],
            ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'auth.token.issued',
            'actor_id' => $user->id,
            'actor_type' => User::class,
        ]);
    }

    public function test_social_google_callback_creates_user_and_audit_log(): void
    {
        $this->mockSocialiteProvider('google', [
            'id' => 'google-123',
            'email' => 'google-user@example.com',
            'name' => 'Google User',
            'avatar' => 'https://cdn.example.com/avatar-google.png',
            'token' => 'plain-google-token',
            'refresh_token' => 'plain-google-refresh',
            'expires_in' => 3600,
        ]);

        $response = $this->getJson('/auth/google/callback');

        $response->assertOk()
            ->assertJsonPath('user.email', 'google-user@example.com')
            ->assertJsonPath('me_endpoint', '/api/me');

        $user = User::query()->where('email', 'google-user@example.com')->firstOrFail();

        $this->assertSame('https://cdn.example.com/avatar-google.png', $user->provider_avatar_url);
        $this->assertSame('google', $user->provider_avatar_provider);

        $this->assertDatabaseHas('social_accounts', [
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_user_id' => 'google-123',
            'email' => 'google-user@example.com',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'auth.social.registered',
            'actor_id' => $user->id,
            'actor_type' => User::class,
        ]);

        $encryptedAccessToken = DB::table('social_accounts')
            ->where('provider', 'google')
            ->value('access_token');

        $this->assertNotSame('plain-google-token', $encryptedAccessToken);
    }

    public function test_social_discord_callback_links_existing_user_by_email(): void
    {
        $existingUser = User::factory()->create([
            'email' => 'existing@example.com',
            'name' => 'Existing User',
        ]);

        $this->mockSocialiteProvider('discord', [
            'id' => 'discord-456',
            'email' => 'existing@example.com',
            'name' => 'Discord Existing',
            'avatar' => 'https://cdn.example.com/avatar-discord.png',
            'token' => 'plain-discord-token',
            'refresh_token' => 'plain-discord-refresh',
            'expires_in' => 3600,
        ]);

        $response = $this->getJson('/auth/discord/callback');

        $response->assertOk()
            ->assertJsonPath('user.id', $existingUser->id)
            ->assertJsonPath('user.email', 'existing@example.com');

        $existingUser->refresh();
        $this->assertSame('https://cdn.example.com/avatar-discord.png', $existingUser->provider_avatar_url);
        $this->assertSame('discord', $existingUser->provider_avatar_provider);

        $this->assertSame(1, User::query()->count());

        $this->assertDatabaseHas('social_accounts', [
            'user_id' => $existingUser->id,
            'provider' => 'discord',
            'provider_user_id' => 'discord-456',
            'email' => 'existing@example.com',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'auth.social.linked',
            'actor_id' => $existingUser->id,
            'actor_type' => User::class,
        ]);
    }

    public function test_social_callback_on_web_flow_logs_in_user_and_redirects_to_home(): void
    {
        $this->mockSocialiteProvider('google', [
            'id' => 'google-web-999',
            'email' => 'google-web-user@example.com',
            'name' => 'Google Web User',
            'avatar' => 'https://cdn.example.com/avatar-google-web.png',
            'token' => 'plain-google-web-token',
            'refresh_token' => 'plain-google-web-refresh',
            'expires_in' => 3600,
        ]);

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(url('/'));
        $response->assertSessionHas('success');
        $this->assertAuthenticated();

        $user = User::query()->where('email', 'google-web-user@example.com')->firstOrFail();
        $this->assertSame('https://cdn.example.com/avatar-google-web.png', $user->provider_avatar_url);
        $this->assertSame('google', $user->provider_avatar_provider);

        $this->assertDatabaseHas('social_accounts', [
            'provider' => 'google',
            'provider_user_id' => 'google-web-999',
            'email' => 'google-web-user@example.com',
        ]);
    }

    public function test_authenticated_user_can_link_discord_account_from_profile(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'player-profile@example.com',
            'name' => 'Profile Player',
        ]);

        $this->mockSocialiteProvider('discord', [
            'id' => 'discord-link-321',
            'email' => 'other-discord@example.com',
            'name' => 'Discord Link',
            'avatar' => 'https://cdn.example.com/avatar-discord-link.png',
            'token' => 'plain-discord-link-token',
            'refresh_token' => 'plain-discord-link-refresh',
            'expires_in' => 3600,
        ]);

        $response = $this
            ->actingAs($user)
            ->withSession([
                'social_auth.link' => [
                    'provider' => 'discord',
                    'user_id' => $user->id,
                    'return_route' => 'profile.show',
                ],
            ])
            ->get('/auth/discord/callback');

        $response->assertRedirect(route('profile.show'));
        $response->assertSessionHas('success', 'Compte discord lie a votre profil.');
        $this->assertAuthenticatedAs($user);
        $this->assertSame(1, User::query()->count());

        $user->refresh();
        $this->assertSame('https://cdn.example.com/avatar-discord-link.png', $user->provider_avatar_url);
        $this->assertSame('discord', $user->provider_avatar_provider);

        $this->assertDatabaseHas('social_accounts', [
            'user_id' => $user->id,
            'provider' => 'discord',
            'provider_user_id' => 'discord-link-321',
            'email' => 'other-discord@example.com',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'auth.social.linked',
            'actor_id' => $user->id,
            'actor_type' => User::class,
        ]);
    }

    public function test_social_login_updates_provider_avatar_without_overwriting_uploaded_avatar(): void
    {
        config(['filesystems.media_disk' => 'public']);
        Storage::fake('public');
        Storage::disk('public')->put('avatars/custom-player.png', 'avatar');

        $user = User::factory()->create([
            'email' => 'kept-avatar@example.com',
            'avatar_path' => 'avatars/custom-player.png',
        ]);

        $this->mockSocialiteProvider('google', [
            'id' => 'google-keep-777',
            'email' => 'kept-avatar@example.com',
            'name' => 'Avatar Keeper',
            'avatar' => 'https://cdn.example.com/avatar-provider-fresh.png',
            'token' => 'plain-provider-token',
            'refresh_token' => 'plain-provider-refresh',
            'expires_in' => 3600,
        ]);

        $this->getJson('/auth/google/callback')
            ->assertOk()
            ->assertJsonPath('user.id', $user->id);

        $user->refresh();

        $this->assertSame('avatars/custom-player.png', $user->avatar_path);
        $this->assertSame('https://cdn.example.com/avatar-provider-fresh.png', $user->provider_avatar_url);
        $this->assertSame(route('media.public.file', ['path' => 'avatars/custom-player.png']), $user->display_avatar_url);
    }

    public function test_admin_user_seeder_is_idempotent_and_audited(): void
    {
        Artisan::call('db:seed', ['--class' => AdminUserSeeder::class]);
        Artisan::call('db:seed', ['--class' => AdminUserSeeder::class]);

        $this->assertSame(1, User::query()->where('email', 'admin@erah.local')->count());

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'seed.admin_user.upserted',
        ]);
    }

    public function test_admin_user_seeder_provisions_configured_platform_admin_account(): void
    {
        $previousEmail = env('PLATFORM_ADMIN_EMAIL');
        $previousName = env('PLATFORM_ADMIN_NAME');
        $previousPassword = env('PLATFORM_ADMIN_PASSWORD');

        putenv('PLATFORM_ADMIN_EMAIL=erah.association@gmail.com');
        putenv('PLATFORM_ADMIN_NAME=ERAH Association');
        putenv('PLATFORM_ADMIN_PASSWORD=ErahAdmin!2026#Cloud');

        try {
            Artisan::call('db:seed', ['--class' => AdminUserSeeder::class]);

            $admin = User::query()->where('email', 'erah.association@gmail.com')->firstOrFail();

            $this->assertSame(User::ROLE_ADMIN, $admin->role);
            $this->assertSame('ERAH Association', $admin->name);
            $this->assertTrue(Hash::check('ErahAdmin!2026#Cloud', $admin->password));
        } finally {
            $this->restoreEnv('PLATFORM_ADMIN_EMAIL', $previousEmail);
            $this->restoreEnv('PLATFORM_ADMIN_NAME', $previousName);
            $this->restoreEnv('PLATFORM_ADMIN_PASSWORD', $previousPassword);
        }
    }

    private function restoreEnv(string $key, mixed $value): void
    {
        if ($value === false || $value === null) {
            putenv($key);

            return;
        }

        putenv($key.'='.$value);
    }

    private function mockSocialiteProvider(string $provider, array $payload): void
    {
        $socialiteUser = new SocialiteUser();
        $socialiteUser->id = $payload['id'];
        $socialiteUser->email = $payload['email'];
        $socialiteUser->name = $payload['name'];
        $socialiteUser->avatar = $payload['avatar'];
        $socialiteUser->token = $payload['token'];
        $socialiteUser->refreshToken = $payload['refresh_token'];
        $socialiteUser->expiresIn = $payload['expires_in'];

        $driver = Mockery::mock();
        $driver->shouldReceive('stateless')->andReturnSelf();
        $driver->shouldReceive('scopes')->andReturnSelf();
        $driver->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with($provider)
            ->andReturn($driver);
    }
}
