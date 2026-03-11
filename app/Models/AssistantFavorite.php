<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssistantFavorite extends Model
{
    protected $fillable = [
        'user_id',
        'fingerprint',
        'question',
        'answer',
        'details',
        'sources',
        'next_steps',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'details' => 'array',
            'sources' => 'array',
            'next_steps' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
