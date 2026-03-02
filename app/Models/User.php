<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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

    public function giftRedemptions(): HasMany
    {
        return $this->hasMany(GiftRedemption::class);
    }

    public function giftRedemptionEvents(): HasMany
    {
        return $this->hasMany(GiftRedemptionEvent::class, 'actor_user_id');
    }
}
