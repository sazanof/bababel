<?php

namespace App\Http\Middleware;

use App\Exceptions\NotMeetingOwnerException;
use App\Models\Meeting;
use App\Models\Recording;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanDeleteRecord
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
        $id = (int)$parameters['id']; // record id
        $record = Recording::find($id);
        if (is_null($record)) {
            throw new \Exception('Record not found');
        }
        /** @var User $user */
        $user = $request->user();
        $meeting = Meeting::find($record->meetingId);
        if ($user->id !== $meeting->userId) {
            throw new NotMeetingOwnerException();
        }
        return $next($request);
    }
}
