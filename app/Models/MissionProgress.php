<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissionProgress extends Model
{
    protected $table = 'mission_progress';

    protected $fillable = [
        'mission_id',
        'user_id',
        'period_key',
        'accepted_at',
        'completed_at',
        'progress_json',
        'awarded_points',
        'awarded_at',
    ];

    protected function casts(): array
    {
        return [
            'accepted_at' => 'datetime',
            'completed_at' => 'datetime',
            'progress_json' => 'array',
            'awarded_points' => 'boolean',
            'awarded_at' => 'datetime',
        ];
    }

    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
