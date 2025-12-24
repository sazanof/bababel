<?php

namespace App\Http\Controllers;

use App\Enums\MeetingGuestPolicy;
use App\Exceptions\JoinMeetingException;
use App\Helpers\BababelHelper;
use App\Helpers\NotificationHelper;
use App\Models\Meeting;
use App\Models\Participant;
use App\Models\Recording;
use App\Models\User;
use Carbon\Carbon;
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
     * @throws \Exception
     */
    public function joinMeeting(int $id, Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        /** @var Meeting $meeting */
        $meeting = Meeting::select(Meeting::$selectableFields)->find($id);
        if (!$user instanceof User) {
            switch ($meeting->guestPolicy) {
                case MeetingGuestPolicy::ASK_MODERATOR:
                case MeetingGuestPolicy::ALWAYS_DENY:
                    throw new \Exception('Guest access not allowed for this meeting', 66);
            }
        }

        // ok: user is User, guest policy is ALWAYS ACCEPT
        $res = BababelHelper::joinMeeting($meeting, $request->get('visibleName'), $request->user());
        if ($res->isSuccessful()) {
            try {
                Log::info(sprintf(
                    'BBB-JOIN-MEETING meeting - %d (%s), visibleName - %s,  DB username - %s',
                    $meeting->id,
                    $meeting->name,
                    $request->get('visibleName'),
                    $user?->username
                ));
            } catch (\Exception $exception) {
                Log::error($exception);
            }
            $data = $res->getData();
            $url = $data->join->url;
            $data->join->mid = $meeting->id;
            // let's find that user is participant of meeting
            if ($user instanceof User) {
                $participant = Participant::where('meetingId', $meeting->id)->where('userId', $user->id)->first();
                if (!is_null($participant)) {
                    $data->join->pid = $participant->id;
                    $participant->link = $url;
                    $participant->save();
                } else {
                    $data->join->pid = $user->id;
                }
            }

            $meeting->status = $meeting::STATUS_PENDING;
            $meeting->save();
            return $data;
        }
        throw new JoinMeetingException($res);
        //dd($res->getData(), $id, $request->get('visibleName'));
    }

    public function joinMeetingAsGuest(int $id, Request $request)
    {
        /** @var Meeting $meeting */
        $meeting = Meeting::select(Meeting::$selectableFields)->find($id);
        if (in_array($meeting->guestPolicy, [Meeting::ALWAYS_ACCEPT, Meeting::ASK_MODERATOR])) {
            $res = BababelHelper::joinMeeting($meeting, $request->get('visibleName'));
            if ($res->isSuccessful()) {
                $meeting->status = $meeting::STATUS_PENDING;
                $meeting->save();
                return $res;
            }
        }

        throw new JoinMeetingException($res);
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
        // change meeting state only if callback date > meeting date
        $currentDate = Carbon::now();
        if ($currentDate > $meeting->date) {
            $meeting->status = $meeting::STATUS_CLOSED;
            $meeting->save();
        }

        Participant::where('meetingId', $meeting->id)->update([
            'link' => null
        ]);

        Log::info('[BBB CALLBACK ' . $id . '] ' . __METHOD__ . ':' . $request->getQueryString());
        //}
        //throw new HookDeleteException();

    }

    private function makeState(string $state)
    {
        return match ($state) {
            'unpublished' => 0,
            'published' => 1,
            default => throw new \Exception('Unexpected match value')
        };
    }

    public function callbackRecordReady(int $id, Request $request)
    {
        //TODO notificate organizer
        /**
         * $rec = Recording::first();
         * $id = $rec->recordId;
         * $res = BababelHelper::getRecording($id);
         * $record = $res->getData()->recordings->recording;
         */
        $parameters = $request->get('signed_parameters');
        if (!empty($parameters)) {
            $signedParameters = BababelHelper::parseSignedParameters($parameters);
            if (!is_null($signedParameters)) {
                try {
                    $p = BababelHelper::parseSignedParameters($parameters);
                    $r = BababelHelper::getRecording($p->getRecordId());
                    $r = $r->getData()->recordings->recording;
                    $recording = new Recording();
                    $meeting = Meeting::where('meetingId', $r->meetingID)->firstOrFail();
                    $recording->meetingId = $meeting->id;
                    $recording->recordId = $r->recordID;
                    $recording->startTime = \Illuminate\Support\Carbon::createFromTimestampMs($r->startTime);
                    $recording->endTime = \Illuminate\Support\Carbon::createFromTimestampMs($r->endTime);
                    $recording->processingTime = $r->playback->format->processingTime;
                    $recording->size = $r->size;
                    $recording->url = $r->playback->format->url;
                    $recording->state = $this->makeState($r->state);
                    $recording->save();
                    // Send notification
                    NotificationHelper::sendNotificationsOnRecordingReady($recording);

                } catch (\Exception $e) {
                    Log::error('[BBB] [callbackRecordReady] - error while adding recording for recordId ' . $signedParameters->getRecordId());
                    Log::error(json_encode($e));
                    dump($e);
                }
            }

        }
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
