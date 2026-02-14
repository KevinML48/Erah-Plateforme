<?php
declare(strict_types=1);

namespace App\Models;

use App\Enums\MarketStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Market extends Model
{
    protected $fillable = [
        'match_id',
        'code',
        'name',
        'status',
        'settle_rule',
        'settled_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => MarketStatus::class,
            'settle_rule' => 'array',
            'settled_at' => 'datetime',
        ];
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(EsportMatch::class, 'match_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(MarketOption::class);
    }

    public function selections(): HasMany
    {
        return $this->hasMany(TicketSelection::class);
    }
}

