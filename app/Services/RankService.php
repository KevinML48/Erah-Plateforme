<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Rank;
use App\Models\User;

class RankService
{
    public function updateUserRank(User $user): ?Rank
    {
        $rank = Rank::getRankForPoints((int) $user->points_balance);
        $newRankId = $rank?->id;

        if ($user->rank_id !== $newRankId) {
            $user->rank_id = $newRankId;
            $user->save();
        }

        return $rank;
    }
}

