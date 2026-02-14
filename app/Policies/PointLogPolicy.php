<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\PointLog;
use App\Models\User;

class PointLogPolicy
{
    public function view(User $user, PointLog $pointLog): bool
    {
        return (int) $pointLog->user_id === (int) $user->id
            || $user->can('points.adjust')
            || $user->can('users.view')
            || $user->isAdmin();
    }

    public function viewAny(User $user): bool
    {
        return $user->can('points.adjust') || $user->can('users.view') || $user->isAdmin();
    }
}
