<?php

namespace App\Http\Controllers;

use App\Exceptions\JoinMeetingException;
use App\Helpers\BababelHelper;
use App\Models\Meeting;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BababelController extends Controller
{

    /**
     * Create hooks for meeting and if success - start meeting
     * @param int $id
     * @param Request $request
     * @return \App\Helpers\BigBlueButtonApiResponse
     */
    public function startMeeting(int $id, Request $request)
    {
        $meeting = Meeting::find($id);
        //if (BababelHelper::addHooks($meeting)) {
        return BababelHelper::createMeeting($meeting);
        //}
        //throw new HookCreateException();
    }

    public function stopMeeting(int $id, Request $request)
    {
        $meeting = Meeting::select(Meeting::$selectableFields)->find($id);
        BababelHelper::stopMeeting($meeting);
        return $meeting;
    }

    public function getInfo(int $id, Request $request)
    {
        $meeting = Meeting::select(Meeting::$selectableFields)->find($id);
        $info = BababelHelper::getMeetingInfo($meeting);
        $data = $info->getData();
        if (!$data->success && ($meeting->status === 1 || $meeting->status === 2)) { // meeting not found, status must be changed
            $meeting->status = 0;
            $meeting->save();
        }
        return $meeting;
    }

    public function isRunning(int $id)
    {
        $meeting = Meeting::select(Meeting::$selectableFields)->find($id);
        return BababelHelper::isMeetingRunning($meeting);
    }

    /**
     * @param int $id
     * @param Request $request
     * @return \App\Helpers\BigBlueButtonApiResponse
     * @throws JoinMeetingException
     */
    public function joinMeeting(int $id, Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        /** @var Meeting $meeting */
        $meeting = Meeting::select(Meeting::$selectableFields)->find($id);
        $res = BababelHelper::joinMeeting($meeting, $request->get('visibleName'));
        if ($res->isSuccessful()) {
            $data = $res->getData();
            $url = $data->join->url;
            $participant = Participant::where('meetingId', $meeting->id)->where('userId', $user->id)->first();
            $participant->link = $url;
            $participant->save();
            $meeting->status = $meeting::STATUS_PENDING;
            $meeting->save();
            return $res;
        }
        throw new JoinMeetingException($res);
        //dd($res->getData(), $id, $request->get('visibleName'));
    }

    /**
     * End meeting callback, delete hooks for meeting
     * @param int $id
     * @param Request $request
     * @return void
     */
    public function callbackEndMeeting(int $id, Request $request): void
    {
        $meeting = Meeting::find($id);
        //if (BababelHelper::deleteHooks($meeting)) {
        $meeting->status = $meeting::STATUS_CLOSED;
        Participant::where('meetingId', $meeting->id)->update([
            'link' => null
        ]);
        $meeting->save();
        Log::info('[BBB CALLBACK ' . $id . '] ' . __METHOD__ . ':' . $request->getQueryString());
        //}
        //throw new HookDeleteException();

    }

    public function callbackRecordReady(int $id, Request $request)
    {
        //TODO notificate organizer
        Log::info('[BBB CALLBACK RECORD ' . $id . '] ' . __METHOD__ . ': ' . json_encode($request->all()));
    }

    /**
     * Callback for hooks for meeting
     * @param int $id
     * @param Request $request
     * @return void
     */
    public function callbackHooks(int $id, Request $request)
    {
        Log::info('BBB HOOKS CALLBACK: [' . $id . '] - ', json_encode($request->all()));
    }

}
