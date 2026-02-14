<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\Reward;
use App\Models\User;

class RewardPolicy
{
    public function manageRewards(User $user, ?Reward $reward = null): bool
    {
        return $user->can('rewards.manage') || $user->isAdmin();
    }
}
