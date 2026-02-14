<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function matches(): HasMany
    {
        return $this->hasMany(EsportMatch::class, 'game_id');
    }
}

