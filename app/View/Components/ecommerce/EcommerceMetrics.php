<?php
declare(strict_types=1);

namespace App\View\Components\ecommerce;

use App\Models\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EcommerceMetrics extends Component
{
    public int $currentUserPoints;
    public int $totalPlatformPoints;
    public string $currentRankName;
    public ?string $nextRankName;
    public int $progressToNextRank;

    public function __construct()
    {
        $user = auth()->user();
        $user?->loadMissing('rank');

        $this->currentUserPoints = (int) ($user?->points_balance ?? 0);
        $this->totalPlatformPoints = (int) User::query()->sum('points_balance');
        $this->currentRankName = $user?->rank?->name ?? 'Bronze';
        $this->nextRankName = $user?->getNextRank()?->name;
        $this->progressToNextRank = $user?->getProgressToNextRank() ?? 0;
    }

    public function render(): View|Closure|string
    {
        return view('components.ecommerce.ecommerce-metrics');
    }
}
