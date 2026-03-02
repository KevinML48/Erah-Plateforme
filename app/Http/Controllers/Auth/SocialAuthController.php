<?php

namespace App\Http\Controllers\Auth;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Application\Actions\Auth\HandleSocialCallbackAction;
use App\Application\Actions\Auth\IssueApiTokenAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class SocialAuthController extends Controller
{
    /**
     * @var string[]
     */
    private const SUPPORTED_PROVIDERS = ['google', 'discord'];

    public function redirect(string $provider): RedirectResponse
    {
        $driver = $this->resolveDriver($provider);

        return $driver->redirect();
    }

    public function callback(
        Request $request,
        string $provider,
        HandleSocialCallbackAction $handleSocialCallbackAction,
        IssueApiTokenAction $issueApiTokenAction,
        StoreAuditLogAction $storeAuditLogAction
    ): JsonResponse|RedirectResponse {
        $driver = $this->resolveDriver($provider);
        try {
            $socialiteUser = $driver->user();
        } catch (Throwable $exception) {
            return $this->buildSocialFailureResponse(
                request: $request,
                provider: $provider,
                exception: $exception,
                storeAuditLogAction: $storeAuditLogAction,
            );
        }

        $user = $handleSocialCallbackAction->execute(
            provider: $provider,
            providerUser: $socialiteUser,
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
        );

        if (! $request->expectsJson()) {
            Auth::login($user, true);
            $request->session()->regenerate();

            return redirect()
                ->route('dashboard')
                ->with('success', 'Connexion '.$provider.' reussie.');
        }

        $token = $issueApiTokenAction->execute(
            user: $user,
            deviceName: 'social-'.$provider,
            reason: 'social-'.$provider,
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return response()->json([
            'token_type' => 'Bearer',
            'access_token' => $token,
            'me_endpoint' => '/api/me',
            'user' => $user,
        ]);
    }

    private function buildSocialFailureResponse(
        Request $request,
        string $provider,
        Throwable $exception,
        StoreAuditLogAction $storeAuditLogAction
    ): JsonResponse|RedirectResponse {
        $rawMessage = $exception->getMessage();
        $reason = str_contains(strtolower($rawMessage), 'invalid_grant')
            ? 'invalid_grant'
            : 'oauth_error';

        $hint = $reason === 'invalid_grant'
            ? 'OAuth code invalide/expire ou redirect URI Google non conforme.'
            : 'Erreur OAuth durant le callback.';

        $storeAuditLogAction->execute(
            action: 'auth.social.failed',
            actor: null,
            target: null,
            context: [
                'provider' => $provider,
                'reason' => $reason,
                'hint' => $hint,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'exception_class' => $exception::class,
                'message' => $rawMessage,
            ],
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Echec du login social.',
                'provider' => $provider,
                'reason' => $reason,
                'hint' => $hint,
            ], 422);
        }

        return redirect()
            ->route('login')
            ->with('error', 'Echec login '.$provider.': '.$hint);
    }

    private function resolveDriver(string $provider): mixed
    {
        if (! in_array($provider, self::SUPPORTED_PROVIDERS, true)) {
            throw new NotFoundHttpException();
        }

        $driver = Socialite::driver($provider)->stateless();

        if ($provider === 'google') {
            return $driver->scopes(['openid', 'profile', 'email']);
        }

        if ($provider === 'discord') {
            return $driver->scopes(['identify', 'email']);
        }

        return $driver;
    }
}
