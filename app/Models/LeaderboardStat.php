<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaderboardStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'period',
        'points_total',
        'calculated_at',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'points_total' => 'integer',
            'calculated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

