<?php

namespace App\View\Components\ecommerce;

use App\Models\EsportMatch;
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
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ecommerce.statistics-chart');
    }
}
