<?php

namespace App\Enums;

enum MeetingParticipantRole: string
{
    case VIEWER = 'VIEWER';
    case MODERATOR = 'MODERATOR';
}
