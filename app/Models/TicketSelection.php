<?php
declare(strict_types=1);

namespace App\Models;

use App\Enums\SelectionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketSelection extends Model
{
    protected $fillable = [
        'ticket_id',
        'market_id',
        'option_id',
        'odds_decimal_snapshot',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'odds_decimal_snapshot' => 'decimal:2',
            'status' => SelectionStatus::class,
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class);
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(MarketOption::class, 'option_id');
    }
}

