<?php

namespace App\Http\Middleware;

use App\Exceptions\NotMeetingOwnerException;
use App\Models\Meeting;
use App\Models\Participant;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsMeetingModerator
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return Response
     * @throws NotMeetingOwnerException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $parameters = $request->route()->parameters();
        $id = (int)$parameters['id'];
        /** @var User $user */
        $user = $request->user();
        $meeting = Meeting::find($id);
        $isModerator = false;
        if ($user instanceof User) {
            /** @var Participant $participant */
            $participant =
                $meeting
                    ->participants()
                    ->where('userId', $user->id)
                    ->first();
            $isModerator = $participant->isModerator;
        }
        // TODO check not only meeting->userId, but moderator
        if (empty($meeting) || !$isModerator) {
            throw new NotMeetingOwnerException();
        }
        return $next($request);
    }
}
