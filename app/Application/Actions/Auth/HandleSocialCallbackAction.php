<?php

namespace App\Application\Actions\Auth;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class HandleSocialCallbackAction
{
    public function __construct(
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    public function execute(
        string $provider,
        SocialiteUser $providerUser,
        ?string $ipAddress = null,
        ?string $userAgent = null,
        ?int $linkToUserId = null
    ): User {
        $provider = Str::lower($provider);
        $providerUserId = trim((string) $providerUser->getId());
        $email = $this->normalizeEmail($providerUser->getEmail());
        $name = trim((string) ($providerUser->getName() ?: $providerUser->getNickname() ?: 'ERAH User'));

        if ($providerUserId === '') {
            throw new \RuntimeException('Provider user ID is missing.');
        }

        return DB::transaction(function () use (
            $provider,
            $providerUserId,
            $email,
            $name,
            $providerUser,
            $ipAddress,
            $userAgent,
            $linkToUserId
        ) {
            $socialAccount = SocialAccount::query()
                ->where('provider', $provider)
                ->where('provider_user_id', $providerUserId)
                ->lockForUpdate()
                ->first();

            $user = $linkToUserId
                ? User::query()->whereKey($linkToUserId)->lockForUpdate()->firstOrFail()
                : null;
            $isNewUser = false;
            $isLinkingExistingUser = false;
            $isExplicitLink = $user !== null;

            if ($socialAccount && $user && $socialAccount->user_id !== $user->id) {
                throw new \RuntimeException('Ce compte '.$provider.' est deja lie a un autre membre.');
            }

            if ($socialAccount) {
                $user ??= User::query()->lockForUpdate()->findOrFail($socialAccount->user_id);
            }

            if (! $user && $email) {
                $user = User::query()->where('email', $email)->lockForUpdate()->first();
                $isLinkingExistingUser = (bool) $user;
            }

            if (! $user) {
                $user = User::query()->create([
                    'name' => $name,
                    'email' => $email ?? $this->buildFallbackEmail($provider, $providerUserId),
                    'email_verified_at' => now(),
                    'password' => Hash::make(Str::password(40)),
                    'role' => User::ROLE_USER,
                ]);
                $isNewUser = true;
            }

            if (! $isExplicitLink && $email && $user->email !== $email) {
                $existingEmailOwner = User::query()
                    ->where('email', $email)
                    ->where('id', '!=', $user->id)
                    ->exists();

                if (! $existingEmailOwner) {
                    $user->email = $email;
                }
            }

            if (! $isExplicitLink && ! $user->email_verified_at) {
                $user->email_verified_at = now();
            }

            if (! $isExplicitLink && (blank($user->name) || Str::startsWith($user->name, 'discord_') || Str::startsWith($user->name, 'google_'))) {
                $user->name = $name;
            }

            $user->save();

            $socialAccount ??= SocialAccount::query()->firstOrNew([
                'user_id' => $user->id,
                'provider' => $provider,
            ]);

            $socialAccount->fill([
                'provider_user_id' => $providerUserId,
                'email' => $email,
                'avatar_url' => $providerUser->getAvatar(),
                'access_token' => $this->extractProperty($providerUser, 'token'),
                'refresh_token' => $this->extractProperty($providerUser, 'refreshToken'),
                'token_expires_at' => $this->resolveTokenExpiry($this->extractProperty($providerUser, 'expiresIn')),
            ]);
            $socialAccount->save();

            $this->storeAuditLogAction->execute(
                action: $isNewUser ? 'auth.social.registered' : 'auth.social.linked',
                actor: $user,
                target: $socialAccount,
                context: [
                    'provider' => $provider,
                    'provider_user_id' => $providerUserId,
                    'linked_existing_user' => $isLinkingExistingUser,
                    'explicit_link' => $isExplicitLink,
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                ],
            );

            return $user->fresh();
        });
    }

    private function resolveTokenExpiry(mixed $expiresIn): ?Carbon
    {
        if (! is_numeric($expiresIn) || (int) $expiresIn <= 0) {
            return null;
        }

        return now()->addSeconds((int) $expiresIn);
    }

    private function normalizeEmail(?string $email): ?string
    {
        $email = trim((string) $email);

        return $email !== '' ? Str::lower($email) : null;
    }

    private function buildFallbackEmail(string $provider, string $providerUserId): string
    {
        $base = Str::lower($provider).'_'.Str::of($providerUserId)->slug('_');
        $suffix = 0;

        do {
            $candidate = $suffix === 0
                ? "{$base}@oauth.erah.local"
                : "{$base}_{$suffix}@oauth.erah.local";

            $suffix++;
        } while (User::query()->where('email', $candidate)->exists());

        return $candidate;
    }

    private function extractProperty(SocialiteUser $providerUser, string $property): mixed
    {
        if (property_exists($providerUser, $property)) {
            $value = $providerUser->{$property};

            return $value !== '' ? $value : null;
        }

        return null;
    }
}
