<?php

namespace App\Http\Middleware;

use App\Exceptions\NotMeetingOwnerException;
use App\Models\Meeting;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsMeetingOwner
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
        // TODO check not only meeting->userId, but moderator
        if (empty($meeting) || $user->id !== $meeting->userId) {
            throw new NotMeetingOwnerException();
        }
        return $next($request);
    }
}
