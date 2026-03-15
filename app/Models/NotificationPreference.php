<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class NotificationPréférence extends Model
{
    protected $fillable = [
        'user_id',
        'category',
        'email_enabled',
        'push_enabled',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'email_enabled' => 'boolean',
            'push_enabled' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
