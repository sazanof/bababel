<?php

namespace App\Http\Middleware;

use App\Exceptions\AlreadyJoinedException;
use App\Exceptions\JoinMeetingException;
use App\Exceptions\MeetingsException;
use App\Helpers\BababelHelper;
use App\Models\Meeting;
use App\Models\Participant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Logging\Exception;
use Symfony\Component\HttpFoundation\Response;

class AlreadyJoined
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return Response
     * @throws JoinMeetingException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $parameters = $request->route()->parameters();
        $id = (int)$parameters['id'];
        $meeting = Meeting::find($id);
        $user = Auth::user();
        /** @var Participant $participant */
        $participant = $meeting
            ->participants()
            ->where('meetingId', $meeting->id)
            ->where('userId', $user->id)
            ->first();
        $meetingInfo = BababelHelper::getMeetingInfo($meeting);
        if ($meetingInfo->isSuccessful()) {
            $data = $meetingInfo->getData();
            if (count($data->attendees) === 0 && count($data->moderators) === 0) {
                return $next($request);
            } else {
                foreach ($data->attendees as $attendee) {
                    if ($attendee->userId === $user->id) {
                        $e = new AlreadyJoinedException();
                        return response()->json([
                            'message' => $e->getMessage(),
                            'link' => $participant->link,
                            'join' => [
                                'id' => $participant->participants_tid,
                                'isModerator' => $participant->isModerator,
                                'isOrganizer' => $participant->isOrganizer,
                                'url' => $participant->link,
                                'meetingId' => $meeting->id,
                                'userId' => $user->id
                            ]
                        ], 409);
                    }
                }
            }
            return $next($request);
        }
        throw new Exception(__('exceptions.join.error'));

    }
}
