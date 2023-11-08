<?php

namespace App\Exceptions;

use Exception;

class NotMeetingOwnerException extends Exception
{
    public function __construct(int $code = 0, ?\Throwable $previous = null)
    {
        $message = __('exceptions.meeting.owner');
        parent::__construct($message, $code, $previous);
    }
}
