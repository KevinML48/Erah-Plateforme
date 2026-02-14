<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Reward;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class RewardCatalogService
{
    public function listActiveRewards(?User $user, int $perPage = 20): LengthAwarePaginator
    {
        $query = Reward::query()
            ->where('is_active', true)
            ->where(function ($q): void {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q): void {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->orderBy('points_cost');

        return $query->paginate($perPage);
    }

    public function getRewardBySlug(string $slug): Reward
    {
        return Reward::query()->where('slug', $slug)->firstOrFail();
    }
}

