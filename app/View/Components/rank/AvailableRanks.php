<?php
declare(strict_types=1);

namespace App\View\Components\rank;

use App\Models\Rank;
use App\Models\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class AvailableRanks extends Component
{
    public Collection $ranks;
    public ?User $user;
    public ?Rank $nextRank;
    public ?Rank $currentRank;
    public ?Rank $maxRank;
    public int $progressToNextRank;
    public int $pointsToNextRank;
    public int $unlockedRanksCount;
    public int $totalRanksCount;
    public int $pointsToMaxRank;
    public int $currentRankPosition;
    /** @var array<string, string> */
    public array $rankIcons;

    public function __construct(?User $user = null)
    {
        $this->user = $user ?? auth()->user() ?? User::query()->first();
        $this->user?->loadMissing('rank');

        $this->ranks = Rank::query()
            ->orderBy('min_points')
            ->get(['id', 'name', 'slug', 'min_points', 'badge_color']);

        $this->currentRank = $this->user?->rank;
        $this->nextRank = $this->user?->getNextRank();
        $this->maxRank = $this->ranks->last();
        $this->progressToNextRank = $this->user?->getProgressToNextRank() ?? 0;
        $this->pointsToNextRank = $this->nextRank
            ? max(0, (int) $this->nextRank->min_points - (int) ($this->user?->points_balance ?? 0))
            : 0;
        $this->totalRanksCount = $this->ranks->count();
        $this->unlockedRanksCount = $this->ranks
            ->filter(fn (Rank $rank): bool => (int) ($this->user?->points_balance ?? 0) >= $rank->min_points)
            ->count();
        $this->pointsToMaxRank = $this->maxRank
            ? max(0, (int) $this->maxRank->min_points - (int) ($this->user?->points_balance ?? 0))
            : 0;
        $this->currentRankPosition = $this->currentRank
            ? max(1, $this->ranks->search(fn (Rank $rank): bool => $rank->id === $this->currentRank?->id) + 1)
            : 1;
        $this->rankIcons = [
            'bronze' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 3L14.3 8.2L20 9.1L15.8 13.1L16.8 19L12 16.2L7.2 19L8.2 13.1L4 9.1L9.7 8.2L12 3Z" fill="#CD7F32"/></svg>',
            'silver' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 2.5L15.1 8.8L22 9.8L17 14.5L18.2 21.2L12 17.9L5.8 21.2L7 14.5L2 9.8L8.9 8.8L12 2.5Z" fill="#C0C0C0"/></svg>',
            'gold' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="8" fill="#FFD700"/><path d="M12 7L13.5 10.2L17 10.7L14.5 13.1L15.1 16.5L12 14.8L8.9 16.5L9.5 13.1L7 10.7L10.5 10.2L12 7Z" fill="#B8860B"/></svg>',
            'elite' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 3L20 8V16L12 21L4 16V8L12 3Z" fill="#3B82F6"/><path d="M12 8L14 12H10L12 8Z" fill="#DBEAFE"/></svg>',
            'champion' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M8 4H16V7C16 9.2 14.2 11 12 11C9.8 11 8 9.2 8 7V4Z" fill="#8B5CF6"/><path d="M10 12H14V15C14 16.1 13.1 17 12 17C10.9 17 10 16.1 10 15V12Z" fill="#A78BFA"/><rect x="8" y="18" width="8" height="2.5" rx="1.25" fill="#6D28D9"/></svg>',
            'default' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 3L14.5 8H20L15.5 11.5L17.2 17.5L12 14.2L6.8 17.5L8.5 11.5L4 8H9.5L12 3Z" fill="#64748B"/></svg>',
        ];
    }

    public function render(): View|Closure|string
    {
        return view('components.rank.available-ranks');
    }
}
