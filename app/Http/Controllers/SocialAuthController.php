<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\EventTrackingService;
use App\Services\LoginTrackingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends Controller
{
    public function __construct(
        private readonly EventTrackingService $eventTrackingService,
        private readonly LoginTrackingService $loginTrackingService
    ) {
    }

    public function redirect(string $provider): RedirectResponse
    {
        abort_unless($this->isSupportedProvider($provider), 404);

        $driver = Socialite::driver($provider);

        if ($provider === 'discord') {
            $driver->scopes(['identify', 'email']);
        }

        return $driver->redirect();
    }

    public function callback(Request $request, string $provider): RedirectResponse
    {
        abort_unless($this->isSupportedProvider($provider), 404);

        try {
            $oauthUser = Socialite::driver($provider)->user();
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('signin')->with('auth_error', 'Connexion '.$provider.' impossible.');
        }

        $email = $oauthUser->getEmail();
        $providerId = (string) $oauthUser->getId();
        $name = $oauthUser->getName() ?: $oauthUser->getNickname() ?: 'User';
        $avatarUrl = $oauthUser->getAvatar();
        $providerColumn = $provider === 'google' ? 'google_id' : 'discord_id';

        if (!$email) {
            $email = $provider.'_'.$providerId.'@local.user';
        }

        $user = User::query()
            ->where($providerColumn, $providerId)
            ->orWhere('email', $email)
            ->first();

        if (!$user) {
            $user = User::query()->create([
                'name' => $name,
                'email' => $email,
                'password' => Str::random(40),
                $providerColumn => $providerId,
                'avatar_url' => $avatarUrl,
            ]);
        } else {
            $updates = [$providerColumn => $providerId];

            if (!$user->email && $email) {
                $updates['email'] = $email;
            }

            if (!$user->name && $name) {
                $updates['name'] = $name;
            }

            if ($avatarUrl) {
                $updates['avatar_url'] = $avatarUrl;
            }

            $user->update($updates);
        }

        Auth::login($user, true);
        $request->session()->regenerate();
        $this->loginTrackingService->onSuccessfulLogin($user);

        if ($provider === 'discord') {
            $this->eventTrackingService->trackAction($user, 'discord_linked', [
                'discord_id' => $providerId,
            ]);
        }

        return redirect()->route('dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('signin');
    }

    private function isSupportedProvider(string $provider): bool
    {
        return in_array($provider, ['google', 'discord'], true);
    }
}
