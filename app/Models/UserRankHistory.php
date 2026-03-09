<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRankHistory extends Model
{
    protected $fillable = [
        'user_id',
        'league_key',
        'league_name',
        'xp_threshold',
        'total_xp',
        'meta',
        'assigned_at',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'xp_threshold' => 'integer',
            'total_xp' => 'integer',
            'meta' => 'array',
            'assigned_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
