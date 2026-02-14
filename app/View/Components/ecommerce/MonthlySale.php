<?php

namespace App\View\Components\ecommerce;

use App\Models\PointLog;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class MonthlySale extends Component
{
    /**
     * @var array<int, string>
     */
    public array $labels = [];

    /**
     * @var array<int, int>
     */
    public array $gains = [];

    /**
     * @var array<int, int>
     */
    public array $losses = [];

    public int $totalGained = 0;

    public int $totalLost = 0;

    public int $netPoints = 0;

    public function __construct()
    {
        $this->labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $this->gains = array_fill(0, 12, 0);
        $this->losses = array_fill(0, 12, 0);

        $user = Auth::user();
        if (!$user) {
            return;
        }

        $startOfYear = Carbon::now()->startOfYear();

        /** @var \Illuminate\Support\Collection<int, object> $rows */
        $rows = PointLog::query()
            ->selectRaw('MONTH(created_at) as month_num')
            ->selectRaw('SUM(CASE WHEN amount > 0 THEN amount ELSE 0 END) as gained_points')
            ->selectRaw('SUM(CASE WHEN amount < 0 THEN ABS(amount) ELSE 0 END) as lost_points')
            ->where('user_id', $user->id)
            ->where('created_at', '>=', $startOfYear)
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->get();

        foreach ($rows as $row) {
            $index = max(0, min(11, ((int) $row->month_num) - 1));
            $this->gains[$index] = (int) $row->gained_points;
            $this->losses[$index] = (int) $row->lost_points;
        }

        $this->totalGained = array_sum($this->gains);
        $this->totalLost = array_sum($this->losses);
        $this->netPoints = $this->totalGained - $this->totalLost;
    }

    public function render(): View|Closure|string
    {
        return view('components.ecommerce.monthly-sale');
    }
}
