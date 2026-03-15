<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissionEventRecord extends Model
{
    protected $fillable = [
        'user_id',
        'event_key',
        'event_type',
        'subject_type',
        'subject_id',
        'amount',
        'context',
        'occurred_at',
        'processused_at',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'amount' => 'integer',
            'context' => 'array',
            'occurred_at' => 'datetime',
            'processused_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
