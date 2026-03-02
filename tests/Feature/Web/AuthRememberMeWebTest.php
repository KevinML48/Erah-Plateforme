<?php

namespace Tests\Feature\Web;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthRememberMeWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_forms_display_remember_me_toggle(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Auth\\/Login', false);

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Auth\\/Register', false);
    }

    public function test_login_with_remember_me_sets_recaller_cookie(): void
    {
        $user = User::factory()->create([
            'email' => 'remember@example.com',
            'password' => 'Password123!',
        ]);

        $recallerCookie = Auth::guard('web')->getRecallerName();

        $response = $this->post(route('auth.login'), [
            'email' => 'remember@example.com',
            'password' => 'Password123!',
            'remember' => '1',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertCookie($recallerCookie);

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->remember_token);
    }

    public function test_register_without_remember_me_does_not_set_recaller_cookie(): void
    {
        $recallerCookie = Auth::guard('web')->getRecallerName();

        $response = $this->post(route('auth.register'), [
            'name' => 'No Remember',
            'email' => 'no-remember@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'remember' => '0',
        ]);

        $response->assertRedirect(route('onboarding'));
        $response->assertCookieMissing($recallerCookie);

        $user = User::query()->where('email', 'no-remember@example.com')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertNull($user->remember_token);
    }
}
