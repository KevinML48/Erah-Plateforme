<?php
declare(strict_types=1);

namespace App\Models;

use App\Enums\PredictionChoice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'user_id',
        'prediction',
        'stake_points',
        'potential_points',
        'is_correct',
        'points_awarded',
    ];

    protected function casts(): array
    {
        return [
            'prediction' => PredictionChoice::class,
            'is_correct' => 'boolean',
            'points_awarded' => 'boolean',
            'match_id' => 'integer',
            'user_id' => 'integer',
            'stake_points' => 'integer',
            'potential_points' => 'integer',
        ];
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(EsportMatch::class, 'match_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
