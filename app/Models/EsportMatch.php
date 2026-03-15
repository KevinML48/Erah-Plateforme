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

    public const EVENT_TYPE_HEAD_TO_HEAD = 'head_to_head';
    public const EVENT_TYPE_TOURNAMENT_RUN = 'tournament_run';

    public const GAME_VALORANT = 'valorant';
    public const GAME_ROCKET_LEAGUE = 'rocket_league';

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
        'event_type',
        'event_name',
        'competition_name',
        'competition_stage',
        'competition_split',
        'best_of',
        'parent_match_id',
        'team_a_name',
        'team_b_name',
        'home_team',
        'away_team',
        'starts_at',
        'locked_at',
        'ends_at',
        'status',
        'result',
        'finished_at',
        'team_a_score',
        'team_b_score',
        'child_matches_unlocked_at',
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
            'ends_at' => 'datetime',
            'finished_at' => 'datetime',
            'child_matches_unlocked_at' => 'datetime',
            'settled_at' => 'datetime',
            'meta' => 'array',
            'best_of' => 'integer',
            'parent_match_id' => 'integer',
            'team_a_score' => 'integer',
            'team_b_score' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function eventTypes(): array
    {
        return [
            self::EVENT_TYPE_HEAD_TO_HEAD,
            self::EVENT_TYPE_TOURNAMENT_RUN,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function supportedGames(): array
    {
        return array_keys((array) config('betting.events.games', []));
    }

    /**
     * @return array<int, int>
     */
    public static function bestOfOptions(): array
    {
        return array_map('intval', array_keys((array) config('betting.events.best_of', [
            1 => 'BO1',
            3 => 'BO3',
            5 => 'BO5',
            7 => 'BO7',
        ])));
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
        return $query
            ->orderByRaw("case when event_type = ? then 0 else 1 end", [self::EVENT_TYPE_TOURNAMENT_RUN])
            ->orderBy('starts_at');
    }

    public function isTournamentRun(): bool
    {
        return (string) $this->event_type === self::EVENT_TYPE_TOURNAMENT_RUN;
    }

    public function isHeadToHead(): bool
    {
        return (string) $this->event_type !== self::EVENT_TYPE_TOURNAMENT_RUN;
    }

    public function hasUnlockedChildMatches(): bool
    {
        return $this->child_matches_unlocked_at !== null;
    }

    public function displayTitle(): string
    {
        if ($this->isTournamentRun()) {
            return (string) ($this->event_name ?: $this->competition_name ?: 'Tournoi Rocket League');
        }

        $teamA = (string) ($this->team_a_name ?: $this->home_team ?: 'Equipe A');
        $teamB = (string) ($this->team_b_name ?: $this->away_team ?: 'Equipe B');

        return $teamA.' vs '.$teamB;
    }

    public function displaySubtitle(): ?string
    {
        if ($this->isTournamentRun()) {
            return collect([
                $this->competition_name,
                $this->competition_split,
                $this->competition_stage,
            ])->filter()->implode(' • ') ?: null;
        }

        return null;
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

    public function parentMatch(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_match_id');
    }

    public function childMatches(): HasMany
    {
        return $this->hasMany(self::class, 'parent_match_id');
    }
}
