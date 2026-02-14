<?php
declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class MatchNotOpenException extends RuntimeException
{
    public function __construct(string $message = 'Predictions are not open for this match.')
    {
        parent::__construct($message);
    }
}
