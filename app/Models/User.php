<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Support\MediaStorage;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use Billable, HasApiTokens, HasFactory, Notifiable;

    public const ROLE_USER = 'user';
    public const ROLE_ADMIN = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'bio',
        'avatar_path',
        'provider_avatar_url',
        'provider_avatar_provider',
        'twitter_url',
        'instagram_url',
        'tiktok_url',
        'discord_url',
        'equipped_profile_badge',
        'equipped_avatar_frame',
        'equipped_profile_banner',
        'equipped_profile_title',
        'equipped_username_color',
        'equipped_profile_theme',
        'profile_featured_until',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'profile_featured_until' => 'datetime',
        ];
    }

    public function getCustomAvatarUrlAttribute(): ?string
    {
        $avatarPath = (string) ($this->avatar_path ?? '');

        if ($avatarPath === '') {
            return null;
        }

        if (! MediaStorage::isManagedPath($avatarPath)) {
            return MediaStorage::url($avatarPath);
        }

        if (! MediaStorage::pathExists($avatarPath, MediaStorage::resolveDiskForPath($avatarPath))) {
            return null;
        }

        return MediaStorage::url($avatarPath);
    }

    public function getProviderAvatarUrlAttribute(?string $value): ?string
    {
        $resolved = $this->normalizeProviderAvatarUrl($value);

        if ($resolved !== null) {
            return $resolved;
        }

        $account = $this->resolveProviderAvatarAccount();

        return $this->normalizeProviderAvatarUrl($account?->avatar_url);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->custom_avatar_url ?: $this->provider_avatar_url;
    }

    public function getDisplayAvatarUrlAttribute(): string
    {
        return $this->avatar_url ?: MediaStorage::fallbackAvatarUrl();
    }

    public function hasCustomAvatar(): bool
    {
        return $this->custom_avatar_url !== null;
    }

    public function hasAnyAvatar(): bool
    {
        return $this->avatar_url !== null;
    }

    public function syncProviderAvatar(?string $preferredProvider = null): void
    {
        $account = $this->resolveProviderAvatarAccount($preferredProvider);
        $providerAvatarUrl = $this->normalizeProviderAvatarUrl($account?->avatar_url);

        $this->forceFill([
            'provider_avatar_url' => $providerAvatarUrl,
            'provider_avatar_provider' => $providerAvatarUrl !== null ? $account?->provider : null,
        ])->saveQuietly();
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function progress(): HasOne
    {
        return $this->hasOne(UserProgress::class);
    }

    public function pointsTransactions(): HasMany
    {
        return $this->hasMany(PointsTransaction::class);
    }

    public function leaguePromotions(): HasMany
    {
        return $this->hasMany(LeaguePromotion::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function sentAdminOutboundEmails(): HasMany
    {
        return $this->hasMany(AdminOutboundEmail::class, 'sender_admin_user_id');
    }

    public function receivedAdminOutboundEmails(): HasMany
    {
        return $this->hasMany(AdminOutboundEmail::class, 'recipient_user_id');
    }

    public function notificationPreferences(): HasMany
    {
        return $this->hasMany(NotificationPreference::class);
    }

    public function notificationChannels(): HasOne
    {
        return $this->hasOne(UserNotificationChannel::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(UserDevice::class);
    }

    public function createdClips(): HasMany
    {
        return $this->hasMany(Clip::class, 'created_by');
    }

    public function updatedClips(): HasMany
    {
        return $this->hasMany(Clip::class, 'updated_by');
    }

    public function clipLikes(): HasMany
    {
        return $this->hasMany(ClipLike::class);
    }

    public function clipFavorites(): HasMany
    {
        return $this->hasMany(ClipFavorite::class);
    }

    public function clipComments(): HasMany
    {
        return $this->hasMany(ClipComment::class);
    }

    public function clipViews(): HasMany
    {
        return $this->hasMany(ClipView::class);
    }

    public function clipShares(): HasMany
    {
        return $this->hasMany(ClipShare::class);
    }

    public function duelsCreated(): HasMany
    {
        return $this->hasMany(Duel::class, 'challenger_id');
    }

    public function duelsReceived(): HasMany
    {
        return $this->hasMany(Duel::class, 'challenged_id');
    }

    public function duelEvents(): HasMany
    {
        return $this->hasMany(DuelEvent::class, 'actor_id');
    }

    public function duelWins(): HasMany
    {
        return $this->hasMany(DuelResult::class, 'winner_user_id');
    }

    public function duelLosses(): HasMany
    {
        return $this->hasMany(DuelResult::class, 'loser_user_id');
    }

    public function createdMatches(): HasMany
    {
        return $this->hasMany(EsportMatch::class, 'created_by');
    }

    public function updatedMatches(): HasMany
    {
        return $this->hasMany(EsportMatch::class, 'updated_by');
    }

    public function bets(): HasMany
    {
        return $this->hasMany(Bet::class);
    }

    public function processedMatchSettlements(): HasMany
    {
        return $this->hasMany(MatchSettlement::class, 'processed_by');
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(UserWallet::class);
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function activityEvents(): HasMany
    {
        return $this->hasMany(ActivityEvent::class);
    }

    public function missionProgress(): HasMany
    {
        return $this->hasMany(UserMission::class);
    }

    private function resolveProviderAvatarAccount(?string $preferredProvider = null): ?SocialAccount
    {
        $accounts = $this->providerAvatarAccounts();

        if ($preferredProvider !== null) {
            $preferred = $accounts->first(fn (SocialAccount $account) => $account->provider === $preferredProvider
                && $this->normalizeProviderAvatarUrl($account->avatar_url) !== null);

            if ($preferred) {
                return $preferred;
            }
        }

        $storedProvider = trim((string) $this->getRawOriginal('provider_avatar_provider', ''));
        if ($storedProvider !== '') {
            $matchingStoredProvider = $accounts->first(fn (SocialAccount $account) => $account->provider === $storedProvider
                && $this->normalizeProviderAvatarUrl($account->avatar_url) !== null);

            if ($matchingStoredProvider) {
                return $matchingStoredProvider;
            }
        }

        return $accounts->first(fn (SocialAccount $account) => $this->normalizeProviderAvatarUrl($account->avatar_url) !== null);
    }

    /**
     * @return Collection<int, SocialAccount>
     */
    private function providerAvatarAccounts(): Collection
    {
        if ($this->relationLoaded('socialAccounts')) {
            return $this->socialAccounts
                ->whereIn('provider', ['discord', 'google'])
                ->sortByDesc(fn (SocialAccount $account) => $account->updated_at?->getTimestamp() ?? 0)
                ->values();
        }

        if (! $this->exists) {
            return collect();
        }

        return $this->socialAccounts()
            ->whereIn('provider', ['discord', 'google'])
            ->orderByDesc('updated_at')
            ->get();
    }

    private function normalizeProviderAvatarUrl(mixed $value): ?string
    {
        $url = trim((string) $value);

        if ($url === '' || filter_var($url, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        if (! in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        return $url;
    }

    public function missionFocuses(): HasMany
    {
        return $this->hasMany(UserMissionFocus::class);
    }

    public function missionEventRecords(): HasMany
    {
        return $this->hasMany(MissionEventRecord::class);
    }

    public function missionCompletions(): HasMany
    {
        return $this->hasMany(MissionCompletion::class);
    }

    public function rewardWallet(): HasOne
    {
        return $this->hasOne(UserRewardWallet::class);
    }

    public function rewardWalletTransactions(): HasMany
    {
        return $this->hasMany(RewardWalletTransaction::class);
    }

    public function communityRewardGrants(): HasMany
    {
        return $this->hasMany(CommunityRewardGrant::class);
    }

    public function giftRedemptions(): HasMany
    {
        return $this->hasMany(GiftRedemption::class);
    }

    public function giftCartItems(): HasMany
    {
        return $this->hasMany(GiftCartItem::class);
    }

    public function giftFavorites(): HasMany
    {
        return $this->hasMany(GiftFavorite::class);
    }

    public function giftRedemptionEvents(): HasMany
    {
        return $this->hasMany(GiftRedemptionEvent::class, 'actor_user_id');
    }

    public function profileCosmetics(): HasMany
    {
        return $this->hasMany(UserProfileCosmetic::class);
    }

    public function supportSubscriptions(): HasMany
    {
        return $this->hasMany(UserSupportSubscription::class);
    }

    public function clubReview(): HasOne
    {
        return $this->hasOne(ClubReview::class);
    }

    public function supportPublicProfile(): HasOne
    {
        return $this->hasOne(SupporterPublicProfile::class);
    }

    public function supporterMonthlyRewards(): HasMany
    {
        return $this->hasMany(SupporterMonthlyReward::class);
    }

    public function clipVotes(): HasMany
    {
        return $this->hasMany(ClipVote::class);
    }

    public function clipSupporterReactions(): HasMany
    {
        return $this->hasMany(ClipSupporterReaction::class);
    }

    public function rankHistories(): HasMany
    {
        return $this->hasMany(UserRankHistory::class);
    }

    public function loginStreak(): HasOne
    {
        return $this->hasOne(UserLoginStreak::class);
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function liveCodeRedemptions(): HasMany
    {
        return $this->hasMany(LiveCodeRedemption::class);
    }

    public function userAchievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(UserPurchase::class);
    }

    public function pushSubscriptions(): HasMany
    {
        return $this->hasMany(PushSubscription::class);
    }

    public function assistantConversations(): HasMany
    {
        return $this->hasMany(AssistantConversation::class);
    }

    public function assistantFavorites(): HasMany
    {
        return $this->hasMany(AssistantFavorite::class);
    }

    public function guidedTours(): HasMany
    {
        return $this->hasMany(UserGuidedTour::class);
    }

    public function activeSupportSubscription(): ?UserSupportSubscription
    {
        $relation = $this->relationLoaded('supportSubscriptions')
            ? collect($this->supportSubscriptions)->first(fn (UserSupportSubscription $subscription) => $subscription->status === UserSupportSubscription::STATUS_ACTIVE)
            : null;

        if ($relation instanceof UserSupportSubscription) {
            return $relation;
        }

        return $this->supportSubscriptions()
            ->active()
            ->current()
            ->first();
    }

    public function isSupporterActive(): bool
    {
        return $this->activeSupportSubscription() !== null;
    }

    public function supporterStatus(): string
    {
        $latest = $this->relationLoaded('supportSubscriptions')
            ? collect($this->supportSubscriptions)->sortByDesc('id')->first()
            : $this->supportSubscriptions()->current()->first();

        return $latest?->status ?? UserSupportSubscription::STATUS_INACTIVE;
    }

    public function supporterEndsAt(): mixed
    {
        $subscription = $this->activeSupportSubscription()
            ?: ($this->relationLoaded('supportSubscriptions')
                ? collect($this->supportSubscriptions)->sortByDesc('id')->first()
                : $this->supportSubscriptions()->current()->first());

        return $subscription?->current_period_end ?? $subscription?->ended_at ?? null;
    }
}
