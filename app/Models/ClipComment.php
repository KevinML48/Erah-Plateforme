<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClipComment extends Model
{
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_HIDDEN = 'hidden';

    protected $fillable = [
        'clip_id',
        'parent_id',
        'user_id',
        'body',
        'status',
        'moderated_at',
    ];

    protected function casts(): array
    {
        return [
            'clip_id' => 'integer',
            'parent_id' => 'integer',
            'user_id' => 'integer',
            'moderated_at' => 'datetime',
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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
