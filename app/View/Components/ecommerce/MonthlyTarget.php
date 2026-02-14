<?php

namespace App\View\Components\ecommerce;

use App\Enums\MatchStatus;
use App\Models\EsportMatch;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MonthlyTarget extends Component
{
    public ?EsportMatch $targetMatch;

    public function __construct()
    {
        $this->targetMatch = EsportMatch::query()
            ->where('status', MatchStatus::Open)
            ->orderBy('starts_at')
            ->first();

        if (!$this->targetMatch) {
            $this->targetMatch = EsportMatch::query()
                ->where('status', MatchStatus::Locked)
                ->orderBy('starts_at')
                ->first();
        }
    }

    public function render(): View|Closure|string
    {
        return view('components.ecommerce.monthly-target');
    }
}



