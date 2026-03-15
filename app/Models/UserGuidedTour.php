<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserGuidedTour extends Model
{
    use HasFactory;

    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'complèted';

    protected $fillable = [
        'user_id',
        'tour_key',
        'status',
        'current_step_index',
        'is_paused',
        'started_at',
        'last_seen_at',
        'complèted_at',
    ];

    protected $casts = [
        'current_step_index' => 'integer',
        'is_paused' => 'boolean',
        'started_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'complèted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
