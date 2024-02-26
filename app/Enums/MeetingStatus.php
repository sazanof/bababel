<?php

namespace App\Enums;

enum MeetingStatus: int
{
    case NEW = 0;
    case CREATED = 1;
    case PENDING = 2;
    case CLOSED = 3;
}
