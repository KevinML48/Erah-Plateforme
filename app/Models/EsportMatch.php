<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EsportMatch extends Model
{
    use HasFactory;

    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_LOCKED = 'locked';
    public const STATUS_LIVE = 'live';
    public const STATUS_FINISHED = 'finished';
    public const STATUS_SETTLED = 'settled';
    public const STATUS_CANCELLED = 'cancelled';

    public const RESULT_HOME = 'home';
    public const RESULT_AWAY = 'away';
    public const RESULT_TEAM_A = 'team_a';
    public const RESULT_TEAM_B = 'team_b';
    public const RESULT_DRAW = 'draw';
    public const RESULT_VOID = 'void';

    protected $table = 'matches';

    protected $fillable = [
        'match_key',
        'game_key',
        'team_a_name',
        'team_b_name',
        'home_team',
        'away_team',
        'starts_at',
        'locked_at',
        'status',
        'result',
        'finished_at',
        'settled_at',
        'meta',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'locked_at' => 'datetime',
            'finished_at' => 'datetime',
            'settled_at' => 'datetime',
            'meta' => 'array',
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_SCHEDULED,
            self::STATUS_LOCKED,
            self::STATUS_LIVE,
            self::STATUS_FINISHED,
            self::STATUS_SETTLED,
            self::STATUS_CANCELLED,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function settlementResults(): array
    {
        return [
            self::RESULT_HOME,
            self::RESULT_AWAY,
            self::RESULT_TEAM_A,
            self::RESULT_TEAM_B,
            self::RESULT_DRAW,
            self::RESULT_VOID,
        ];
    }

    public static function normalizeResultKey(?string $result): ?string
    {
        if ($result === null) {
            return null;
        }

        return match (strtolower(trim($result))) {
            self::RESULT_HOME,
            self::RESULT_TEAM_A => self::RESULT_HOME,
            self::RESULT_AWAY,
            self::RESULT_TEAM_B => self::RESULT_AWAY,
            self::RESULT_DRAW => self::RESULT_DRAW,
            self::RESULT_VOID => self::RESULT_VOID,
            default => null,
        };
    }

    public function scopePublicFeed(Builder $query): Builder
    {
        return $query->orderBy('starts_at');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function bets(): HasMany
    {
        return $this->hasMany(Bet::class, 'match_id');
    }

    public function settlement(): HasOne
    {
        return $this->hasOne(MatchSettlement::class, 'match_id');
    }

    public function markets(): HasMany
    {
        return $this->hasMany(MatchMarket::class, 'match_id');
    }
}
