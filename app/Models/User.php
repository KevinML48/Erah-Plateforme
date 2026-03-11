<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
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
        'twitter_url',
        'instagram_url',
        'tiktok_url',
        'discord_url',
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
        ];
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (blank($this->avatar_path)) {
            return null;
        }

        if (Str::startsWith((string) $this->avatar_path, ['http://', 'https://'])) {
            return $this->avatar_path;
        }

        return Storage::disk('public')->url((string) $this->avatar_path);
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

    public function giftRedemptionEvents(): HasMany
    {
        return $this->hasMany(GiftRedemptionEvent::class, 'actor_user_id');
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
