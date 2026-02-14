<?php
declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class MatchResultMissingException extends RuntimeException
{
    public function __construct(string $message = 'Match result is required.')
    {
        parent::__construct($message);
    }
}
