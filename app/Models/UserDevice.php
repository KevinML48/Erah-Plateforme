<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    protected $fillable = [
        'user_id',
        'platform',
        'device_token',
        'device_name',
        'is_active',
        'meta',
        'last_seen_at',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'is_active' => 'boolean',
            'meta' => 'array',
            'last_seen_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
