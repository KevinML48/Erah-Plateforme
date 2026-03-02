<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class UserNotificationChannel extends Model
{
    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'email_opt_in',
        'push_opt_in',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'email_opt_in' => 'boolean',
            'push_opt_in' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
