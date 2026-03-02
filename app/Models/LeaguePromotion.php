<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class LeaguePromotion extends Model
{
    protected $fillable = [
        'user_id',
        'from_league_id',
        'to_league_id',
        'points_transaction_id',
        'rank_points',
        'promoted_at',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'from_league_id' => 'integer',
            'to_league_id' => 'integer',
            'points_transaction_id' => 'integer',
            'rank_points' => 'integer',
            'promoted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fromLeague(): BelongsTo
    {
        return $this->belongsTo(League::class, 'from_league_id');
    }

    public function toLeague(): BelongsTo
    {
        return $this->belongsTo(League::class, 'to_league_id');
    }

    public function pointsTransaction(): BelongsTo
    {
        return $this->belongsTo(PointsTransaction::class);
    }
}
