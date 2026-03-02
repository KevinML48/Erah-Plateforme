<?php

namespace App\Jobs;

use App\Application\Actions\Duels\ExpireDuelAction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExpireDuelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly int $duelId
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(ExpireDuelAction $expireDuelAction): void
    {
        $expireDuelAction->execute($this->duelId);
    }
}
