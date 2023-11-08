<?php

namespace App\Exceptions;

use Exception;

class HookCreateException extends Exception
{
    public function __construct(string $message = "Error create a hook for meeting", int $code = 3, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
