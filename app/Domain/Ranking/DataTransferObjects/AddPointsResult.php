<?php

namespace App\Domain\Ranking\DataTransferObjects;

use App\Models\PointsTransaction;
use App\Models\UserProgress;
use Illuminate\Support\Collection;

class AddPointsResult
{
    public function __construct(
        public readonly bool $idempotent,
        public readonly PointsTransaction $transaction,
        public readonly UserProgress $progress,
        public readonly Collection $promotions
    ) {
    }
}
