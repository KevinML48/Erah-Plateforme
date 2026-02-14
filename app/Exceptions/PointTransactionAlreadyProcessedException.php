<?php
declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class PointTransactionAlreadyProcessedException extends RuntimeException
{
    public function __construct(string $message = 'This point transaction has already been processed.')
    {
        parent::__construct($message);
    }
}

