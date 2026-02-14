<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\RewardRedemption;
use App\Models\User;

class RedemptionPolicy
{
    public function manageRedemptions(User $user): bool
    {
        return $user->can('redemptions.manage') || $user->isAdmin();
    }

    public function cancelOwnRedemption(User $user, RewardRedemption $redemption): bool
    {
        return $redemption->user_id === $user->id && $redemption->canCancel();
    }
}
