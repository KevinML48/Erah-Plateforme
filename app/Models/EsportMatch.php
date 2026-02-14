<?php
declare(strict_types=1);

namespace App\Models;

use App\Enums\MatchResult;
use App\Enums\MatchStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EsportMatch extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = [
        'game_id',
        'game',
        'title',
        'format',
        'starts_at',
        'lock_at',
        'status',
        'result',
        'result_json',
        'points_reward',
        'predictions_locked_at',
        'completed_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'lock_at' => 'datetime',
            'predictions_locked_at' => 'datetime',
            'completed_at' => 'datetime',
            'points_reward' => 'integer',
            'status' => MatchStatus::class,
            'result' => MatchResult::class,
            'result_json' => 'array',
        ];
    }

    public function gameModel(): BelongsTo
    {
        return $this->belongsTo(Game::class, 'game_id');
    }

    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class, 'match_id');
    }

    public function markets(): HasMany
    {
        return $this->hasMany(Market::class, 'match_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'match_id');
    }

    public function teams(): HasMany
    {
        return $this->hasMany(MatchTeam::class, 'match_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isOpen(): bool
    {
        return $this->status === MatchStatus::Open;
    }

    public function isLocked(): bool
    {
        return $this->status === MatchStatus::Locked;
    }

    public function isCompleted(): bool
    {
        return $this->status === MatchStatus::Completed;
    }

    public function isBetLockPassed(): bool
    {
        if ($this->lock_at === null) {
            $minutes = (int) config('betting.default_lock_minutes_before_start', 10);

            return $this->starts_at !== null && now()->greaterThanOrEqualTo($this->starts_at->copy()->subMinutes($minutes));
        }

        return now()->greaterThanOrEqualTo($this->lock_at);
    }
}
