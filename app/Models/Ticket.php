<?php
declare(strict_types=1);

namespace App\Models;

use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $fillable = [
        'user_id',
        'match_id',
        'stake_points',
        'total_odds_decimal',
        'potential_payout_points',
        'status',
        'locked_at',
        'settled_at',
        'payout_points',
        'refunded_points',
    ];

    protected function casts(): array
    {
        return [
            'stake_points' => 'integer',
            'total_odds_decimal' => 'decimal:3',
            'potential_payout_points' => 'integer',
            'status' => TicketStatus::class,
            'locked_at' => 'datetime',
            'settled_at' => 'datetime',
            'payout_points' => 'integer',
            'refunded_points' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(EsportMatch::class, 'match_id');
    }

    public function selections(): HasMany
    {
        return $this->hasMany(TicketSelection::class);
    }
}

