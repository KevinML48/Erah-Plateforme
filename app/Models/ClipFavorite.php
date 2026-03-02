<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClipFavorite extends Model
{
    protected $fillable = [
        'clip_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'clip_id' => 'integer',
            'user_id' => 'integer',
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
