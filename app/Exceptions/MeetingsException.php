<?php

namespace App\Exceptions;

use Exception;

class MeetingsException extends Exception
{
    public function __construct(int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(__('exceptions.meeting.create'), $code, $previous);
    }
}
