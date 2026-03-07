<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClipVoteCampaign extends Model
{
    public const TYPE_WEEKLY = 'weekly';
    public const TYPE_MONTHLY = 'monthly';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_SETTLED = 'settled';

    protected $fillable = [
        'type',
        'title',
        'starts_at',
        'ends_at',
        'status',
        'winner_clip_id',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'winner_clip_id' => 'integer',
        ];
    }

    public static function types(): array
    {
        return [self::TYPE_WEEKLY, self::TYPE_MONTHLY];
    }

    public static function statuses(): array
    {
        return [self::STATUS_DRAFT, self::STATUS_ACTIVE, self::STATUS_CLOSED, self::STATUS_SETTLED];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_ACTIVE)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }

    public function entries(): HasMany
    {
        return $this->hasMany(ClipVoteEntry::class, 'campaign_id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(ClipVote::class, 'campaign_id');
    }

    public function winnerClip(): BelongsTo
    {
        return $this->belongsTo(Clip::class, 'winner_clip_id');
    }
}
