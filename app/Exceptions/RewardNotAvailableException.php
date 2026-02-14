<?php
declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class RewardNotAvailableException extends RuntimeException
{
    public function __construct(string $message = 'Reward indisponible.')
    {
        parent::__construct($message);
    }
}

