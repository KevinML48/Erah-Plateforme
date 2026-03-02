<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class PointsTransaction extends Model
{
    public const KIND_XP = 'xp';
    public const KIND_RANK = 'rank';

    protected $fillable = [
        'user_id',
        'kind',
        'points',
        'source_type',
        'source_id',
        'meta',
        'before_xp',
        'after_xp',
        'before_rank_points',
        'after_rank_points',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'points' => 'integer',
            'meta' => 'array',
            'before_xp' => 'integer',
            'after_xp' => 'integer',
            'before_rank_points' => 'integer',
            'after_rank_points' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(LeaguePromotion::class);
    }
}
