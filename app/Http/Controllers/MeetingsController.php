<?php

namespace App\Http\Controllers;

use App\Exceptions\MeetingsException;
use App\Helpers\MeetingFormRequest;
use App\Http\Requests\MeetingRequest;
use App\Models\Meeting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeetingsController extends Controller
{
    /**
     * @param MeetingRequest $request
     * @return Model|Meeting
     * @throws MeetingsException
     * @throws \Throwable
     */
    public function addMeeting(MeetingRequest $request): Model|Meeting
    {
        $payload = $request->getPayload();
        if (is_null($payload->get('id')) && Auth::id() === intval($payload->get('userId'))) {
            return (new MeetingFormRequest($request))->add();
        }
        throw new MeetingsException();
    }

    public function editMeeting(int $id, Request $request)
    {
        if (is_numeric($request->get('id')) && Auth::id() === $request->get('userId')) {

        }
    }
}
