<?php

namespace App\Http\Controllers\Auth;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Application\Actions\Auth\HandleSocialCallbackAction;
use App\Application\Actions\Auth\IssueApiTokenAction;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MissionEngine;
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

    public function redirect(Request $request, string $provider): RedirectResponse
    {
        $driver = $this->resolveDriver($provider);

        if ($request->user() && $request->query('intent') === 'link') {
            $request->session()->put('social_auth.link', [
                'provider' => $provider,
                'user_id' => $request->user()->id,
                'return_route' => (string) $request->query('return_route', 'profile.show'),
            ]);
        } else {
            $request->session()->forget('social_auth.link');
        }

        return $driver->redirect();
    }

    public function callback(
        Request $request,
        string $provider,
        HandleSocialCallbackAction $handleSocialCallbackAction,
        IssueApiTokenAction $issueApiTokenAction,
        StoreAuditLogAction $storeAuditLogAction,
        MissionEngine $missionEngine
    ): JsonResponse|RedirectResponse {
        $driver = $this->resolveDriver($provider);
        $linkContext = $this->resolveLinkContext($request, $provider);

        try {
            $socialiteUser = $driver->user();
            $user = $handleSocialCallbackAction->execute(
                provider: $provider,
                providerUser: $socialiteUser,
                ipAddress: $request->ip(),
                userAgent: $request->userAgent(),
                linkToUserId: $linkContext['user_id'],
            );
        } catch (Throwable $exception) {
            return $this->buildSocialFailureResponse(
                request: $request,
                provider: $provider,
                exception: $exception,
                storeAuditLogAction: $storeAuditLogAction,
                returnRoute: $linkContext['return_route'],
            );
        }

        $this->emitProfileCompletionIfEligible($user->fresh(['socialAccounts']), $missionEngine);

        if (! $request->expectsJson()) {
            if ($linkContext['user_id'] !== null) {
                if (! Auth::check() || (int) Auth::id() !== (int) $linkContext['user_id']) {
                    Auth::loginUsingId((int) $linkContext['user_id'], true);
                }

                $request->session()->regenerate();

                return redirect()
                    ->route($linkContext['return_route'])
                    ->with('success', 'Compte '.$provider.' lie a votre profil.');
            }

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
        StoreAuditLogAction $storeAuditLogAction,
        ?string $returnRoute = null
    ): JsonResponse|RedirectResponse {
        $rawMessage = $exception->getMessage();
        $reason = str_contains(strtolower($rawMessage), 'invalid_grant')
            ? 'invalid_grant'
            : 'oauth_error';

        $hint = $reason === 'invalid_grant'
            ? 'OAuth code invalide/expire ou redirect URI non conforme.'
            : ($rawMessage !== '' ? $rawMessage : 'Erreur OAuth durant le callback.');

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
            ->route($returnRoute ?: 'login')
            ->with('error', 'Echec '.$provider.': '.$hint);
    }

    /**
     * @return array{user_id:int|null,return_route:string}
     */
    private function resolveLinkContext(Request $request, string $provider): array
    {
        $payload = $request->session()->pull('social_auth.link');

        if (! is_array($payload) || ($payload['provider'] ?? null) !== $provider) {
            return ['user_id' => null, 'return_route' => 'profile.show'];
        }

        $userId = isset($payload['user_id']) ? (int) $payload['user_id'] : null;
        $returnRoute = (string) ($payload['return_route'] ?? 'profile.show');

        return [
            'user_id' => $userId > 0 ? $userId : null,
            'return_route' => $returnRoute !== '' ? $returnRoute : 'profile.show',
        ];
    }

    private function emitProfileCompletionIfEligible(User $user, MissionEngine $missionEngine): void
    {
        $completion = $this->calculateProfileCompletion($user);

        if ($completion < 75) {
            return;
        }

        $missionEngine->recordEvent($user, 'profile.completed', 1, [
            'event_key' => 'profile.completed.'.$user->id,
            'profile_completion' => $completion,
            'subject_type' => User::class,
            'subject_id' => (string) $user->id,
        ]);
    }

    private function calculateProfileCompletion(User $user): int
    {
        $hasSocialPresence = ! blank($user->twitter_url)
            || ! blank($user->instagram_url)
            || ! blank($user->tiktok_url)
            || ! blank($user->discord_url)
            || $user->socialAccounts->contains(fn ($account) => $account->provider === 'discord');

        $checkpoints = [
            ! blank($user->name),
            ! blank($user->bio),
            ! blank($user->avatar_path),
            $hasSocialPresence,
        ];

        $complèted = count(array_filter($checkpoints));

        return (int) round(($complèted / max(1, count($checkpoints))) * 100);
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
