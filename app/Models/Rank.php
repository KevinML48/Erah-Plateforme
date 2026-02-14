<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rank extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'min_points',
        'badge_color',
    ];

    protected function casts(): array
    {
        return [
            'min_points' => 'integer',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function scopeForPoints(Builder $query, int $points): Builder
    {
        return $query
            ->where('min_points', '<=', $points)
            ->orderByDesc('min_points');
    }

    public static function getRankForPoints(int $points): ?self
    {
        return self::query()->forPoints($points)->first();
    }
}

