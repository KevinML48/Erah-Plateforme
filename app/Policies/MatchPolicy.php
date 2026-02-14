<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\EsportMatch;
use App\Models\User;

class MatchPolicy
{
    public function manageMatch(User $user, ?EsportMatch $match = null): bool
    {
        return $user->can('matches.manage') || $user->can('settlements.manage') || $user->isAdmin();
    }
}
