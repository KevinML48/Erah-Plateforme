<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClipView extends Model
{
    protected $fillable = [
        'clip_id',
        'user_id',
        'session_id',
        'ip_hash',
        'meta',
        'viewed_at',
    ];

    protected function casts(): array
    {
        return [
            'clip_id' => 'integer',
            'user_id' => 'integer',
            'meta' => 'array',
            'viewed_at' => 'datetime',
        ];
    }

    public function clip(): BelongsTo
    {
        return $this->belongsTo(Clip::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
