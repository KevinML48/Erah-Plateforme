<?php
declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserStatus;
use App\Enums\UserRole;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'google_id',
        'discord_id',
        'avatar_url',
        'phone',
        'bio',
        'country',
        'city_state',
        'postal_code',
        'tax_id',
        'facebook',
        'x_url',
        'linkedin',
        'instagram',
        'status',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
            'points_balance' => 'integer',
            'rank_id' => 'integer',
            'is_admin' => 'boolean',
            'role' => UserRole::class,
            'status' => UserStatus::class,
        ];
    }

    public function pointLogs(): HasMany
    {
        return $this->hasMany(PointLog::class);
    }

    public function rank(): BelongsTo
    {
        return $this->belongsTo(Rank::class);
    }

    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function rewardRedemptions(): HasMany
    {
        return $this->hasMany(RewardRedemption::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin || (bool) $this->is_admin;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() !== 'admin') {
            return false;
        }

        return $this->hasPermissionTo('admin.access')
            || $this->hasAnyRole(['super_admin', 'admin', 'moderator', 'logistics', 'analyst']);
    }

    public function getNextRank(): ?Rank
    {
        return Rank::query()
            ->where('min_points', '>', (int) $this->points_balance)
            ->orderBy('min_points')
            ->first();
    }

    public function getProgressToNextRank(): int
    {
        $currentPoints = (int) $this->points_balance;
        $nextRank = $this->getNextRank();

        if (!$nextRank) {
            return 100;
        }

        $currentRankMin = Rank::query()
            ->where('min_points', '<=', $currentPoints)
            ->max('min_points');

        $currentRankMin = (int) ($currentRankMin ?? 0);
        $segmentSize = max(1, $nextRank->min_points - $currentRankMin);
        $progressInSegment = max(0, $currentPoints - $currentRankMin);

        return (int) min(100, round(($progressInSegment / $segmentSize) * 100));
    }
}
