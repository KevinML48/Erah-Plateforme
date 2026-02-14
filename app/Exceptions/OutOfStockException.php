<?php
declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class OutOfStockException extends RuntimeException
{
    public function __construct(string $message = 'Stock insuffisant.')
    {
        parent::__construct($message);
    }
}

