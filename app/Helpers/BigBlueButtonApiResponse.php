<?php

namespace App\Helpers;

use App\Models\Meeting;
use BigBlueButton\Core\Attendee;
use BigBlueButton\Responses\BaseResponse;
use BigBlueButton\Responses\GetMeetingInfoResponse;
use BigBlueButton\Responses\IsMeetingRunningResponse;
use BigBlueButton\Responses\JoinMeetingResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class BigBlueButtonApiResponse extends JsonResponse
{
    protected bool $bbbSuccess;
    protected string $bbbMessage;

    /**
     * @param BaseResponse $response
     * @param int $status
     * @param array $headers
     * @param int $options
     * @param bool $json
     */
    public function __construct(BaseResponse $response, array $headers = [], int $options = 0, bool $json = false)
    {
        $this->bbbSuccess = $response->success();
        $this->bbbMessage = $response->getMessage();

        $data = [
            'code' => $response->getReturnCode(),
            'success' => $response->success(),
            'key' => $response->getMessageKey(),
            'message' => $response->getMessage()
        ];
        $status = $this->bbbSuccess ? 200 : 422;
        if ($response instanceof GetMeetingInfoResponse && $response->success()) {
            $meeting = $response->getMeeting();
            $data['meeting'] = [
                'id' => (int)$meeting->getMeetingId(),
                'name' => $meeting->getMeetingName(),
                'running' => $meeting->isRunning(),
                'hasUserJoined' => $meeting->hasUserJoined(),
            ];
            $data['count'] = [
                'moderators' => $meeting->getModeratorCount(),
                'participants' => $meeting->getParticipantCount(),
                'listeners' => $meeting->getListenerCount(),
                'video' => $meeting->getVideoCount(),
                'voice' => $meeting->getVoiceParticipantCount()
            ];
            $data['attendees'] = $this->mapParticipants((array)$meeting->getAttendees());
            $data['moderators'] = $this->mapParticipants((array)$meeting->getModerators());
        } else if ($response instanceof JoinMeetingResponse && $response->success()) {
            $data['join'] = [
                'meeting_id' => $response->getMeetingId(),
                'user_id' => $response->getUserId(),
                'auth_token' => $response->getAuthToken(),
                'session_token' => $response->getSessionToken(),
                'url' => $response->getUrl()
            ];
        } else if ($response instanceof IsMeetingRunningResponse && $response->success()) {
            $data['running'] = $response->isRunning();
        }
        parent::__construct($data, $status, $headers, $options, $json);
    }

    /**
     * @return bool
     */
    public function isBbbSuccess(): bool
    {
        return $this->bbbSuccess;
    }

    /**
     * @return string
     */
    public function getBbbMessage(): string
    {
        return $this->bbbMessage;
    }

    /**
     * @param BaseResponse $response
     * @param int $status
     * @param array $headers
     * @param int $options
     * @param bool $json
     * @return BigBlueButtonApiResponse
     */
    public static function output(BaseResponse $response, array $headers = [], int $options = 0, bool $json = false): BigBlueButtonApiResponse
    {
        return (new self($response, $headers, $options, $json));
    }

    public function merge(Meeting $meeting)
    {
        $this->setData(
            array_merge(
                $this->getData(true), [
                    'meeting' => $meeting->only($this->selectableFields())
                ]
            )
        );
        return $this;
    }

    private function selectableFields()
    {
        return array_map(function ($item) {
            return Str::replace('meetings.', '', $item);
        }, Meeting::$selectableFields);
    }

    private function mapParticipants(array $participants)
    {
        return array_map(function (Attendee $participant) {
            return [
                'userId' => (int)$participant->getUserId(),
                'fullName' => $participant->getFullName(),
                'clientType' => $participant->getClientType(),
                'role' => $participant->getRole(),
                'data' => $participant->getCustomData(),
                'isListeningOnly' => $participant->isListeningOnly(),
                'isPresenter' => $participant->isPresenter(),
                'hasVideo' => $participant->hasVideo(),
                'hasJoinedVoice' => $participant->hasJoinedVoice()
            ];
        }, $participants);
    }
}
