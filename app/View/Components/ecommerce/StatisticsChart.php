<?php

namespace App\View\Components\ecommerce;

use App\Models\EsportMatch;
use Illuminate\Support\Str;
use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class StatisticsChart extends Component
{
    public Collection $matches;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->matches = EsportMatch::query()
            ->with([
                'teams:id,match_id,team_id,side',
                'teams.team:id,name,logo_url',
            ])
            ->withCount(['markets', 'tickets', 'predictions'])
            ->orderByDesc('starts_at')
            ->get([
                'id',
                'title',
                'game',
                'format',
                'starts_at',
                'status',
                'points_reward',
            ]);
    }

    /**
     * @return array{left:string,right:string}
     */
    public function resolveCardTeams(EsportMatch $match): array
    {
        $left = null;
        $right = null;

        $teams = $match->teams;
        if ($teams->isNotEmpty()) {
            $home = $teams->firstWhere('side', 'home');
            $away = $teams->firstWhere('side', 'away');

            $left = $home?->team?->name ?? $teams->first()?->team?->name;
            $right = $away?->team?->name ?? $teams->where('id', '!=', $teams->first()?->id)->first()?->team?->name;
        }

        if (!$left || !$right) {
            [$parsedLeft, $parsedRight] = $this->resolveTeamsFromTitle((string) $match->title);

            $left ??= $parsedLeft;
            $right ??= $parsedRight;
        }

        $left = trim((string) $left) !== '' ? (string) $left : 'ERAH';
        $right = trim((string) $right) !== '' ? (string) $right : 'Opponent';

        if ($this->containsErah($right) && !$this->containsErah($left)) {
            [$left, $right] = [$right, $left];
        }

        return [
            'left' => $left,
            'right' => $right,
        ];
    }

    /**
     * @return array{0:string,1:string}
     */
    private function resolveTeamsFromTitle(string $title): array
    {
        $normalized = trim($title);
        if ($normalized === '') {
            return ['ERAH', 'Opponent'];
        }

        $parts = preg_split('/\s+(?:vs|x)\s+/i', $normalized, 2);
        if (is_array($parts) && count($parts) === 2) {
            return [
                trim((string) $parts[0]) !== '' ? trim((string) $parts[0]) : 'ERAH',
                trim((string) $parts[1]) !== '' ? trim((string) $parts[1]) : 'Opponent',
            ];
        }

        return ['ERAH', $normalized];
    }

    private function containsErah(string $value): bool
    {
        return Str::contains(Str::lower($value), 'erah');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ecommerce.statistics-chart');
    }
}
