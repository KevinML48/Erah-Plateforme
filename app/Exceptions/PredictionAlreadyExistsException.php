<?php
declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class PredictionAlreadyExistsException extends RuntimeException
{
    public function __construct(string $message = 'You already placed a prediction for this match.')
    {
        parent::__construct($message);
    }
}
