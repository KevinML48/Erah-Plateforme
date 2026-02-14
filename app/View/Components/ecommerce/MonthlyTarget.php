<?php

namespace App\View\Components\ecommerce;

use App\Models\Reward;
use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MonthlyTarget extends Component
{
    public Collection $rewards;

    public function __construct()
    {
        $this->rewards = Reward::query()
            ->where('is_active', true)
            ->where(function ($query): void {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query): void {
                $query->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->where(function ($query): void {
                $query->whereNull('stock')->orWhere('stock', '>', 0);
            })
            ->orderBy('points_cost')
            ->limit(8)
            ->get([
                'id',
                'name',
                'slug',
                'description',
                'points_cost',
                'stock',
                'image_url',
            ]);
    }

    public function render(): View|Closure|string
    {
        return view('components.ecommerce.monthly-target');
    }
}


