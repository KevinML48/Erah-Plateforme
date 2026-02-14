<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'logo_url',
    ];

    public function matchTeams(): HasMany
    {
        return $this->hasMany(MatchTeam::class);
    }
}

