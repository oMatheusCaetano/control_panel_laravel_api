<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class UnableToResolveModelClassException extends Exception
{
    public function __construct(
        string $message = 'Unable to resolve model class.',
        int $code = 400,
        ?Throwable $previous = null
    ) {
        $this->message = $message;
        $this->code = $code;
        $this->previous = $previous;
    }
}
