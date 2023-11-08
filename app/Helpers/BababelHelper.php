<?php

namespace App\Helpers;

use App\Models\Meeting;
use App\Models\Participant;
use App\Models\User;
use BigBlueButton\Parameters\CreateMeetingParameters;
use BigBlueButton\Parameters\EndMeetingParameters;
use BigBlueButton\Parameters\GetMeetingInfoParameters;
use BigBlueButton\Parameters\HooksCreateParameters;
use BigBlueButton\Parameters\IsMeetingRunningParameters;
use BigBlueButton\Parameters\JoinMeetingParameters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class BababelHelper
{
    protected string $url;
    protected string $token;
    protected static ?BababelHelper $instance = null;
    protected Bababel $bbb;

    protected const KEY_DUPLICATE = 'duplicateWarning';
    protected const KEY_NOT_FOUND = 'notFound';
    protected const KEY_FORCIBLY_ENDED = 'meetingForciblyEnded';

    public function __construct()
    {
        $this->url = Config::get('app.bbb_base_url');
        $this->token = Config::get('app.bbb_secret');
        $this->bbb = new Bababel($this->url, $this->token);
    }

    /**
     * @return string
     */
    public function generateMeetingId($id): string
    {
        return 'conf_' . Meeting::count('id') . '_' . uniqid();
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            return new self();
        } else {
            return self::$instance;
        }
    }

    public static function createMeeting(Meeting $meeting)
    {
        $inst = self::getInstance();
        $parameters = $inst->createMeetingParameters($meeting);
        $response = $inst->bbb->createMeeting($parameters);
        if ($response->success()) {
            $meeting->status = $meeting::STATUS_CREATED;
            $meeting->save();
        }
        return BigBlueButtonApiResponse::output($response)->merge($meeting);
    }

    public static function stopMeeting(Meeting $meeting)
    {
        $inst = self::getInstance();
        $parameters = $inst->endMeetingParameters($meeting);
        $response = $inst->bbb->endMeeting($parameters);
        if ($response->success()) {
            $meeting->status = $meeting::STATUS_CLOSED;
            $meeting->save();
        }
        return BigBlueButtonApiResponse::output($response)->merge($meeting);
    }

    public static function isMeetingRunning(Meeting $meeting)
    {
        $inst = self::getInstance();
        $response = $inst->bbb->isMeetingRunning($inst->isMeetingRuninngParameters($meeting));
        return BigBlueButtonApiResponse::output($response)->merge($meeting);
    }

    /**
     * Check if meeting is created on server and get meeting info
     * @param Meeting $meeting
     * @return BigBlueButtonApiResponse
     */
    public static function getMeetingInfo(Meeting $meeting): BigBlueButtonApiResponse
    {
        $inst = self::getInstance();
        $response = $inst->bbb->getMeetingInfo($inst->getMeetingInfoParameters($meeting));
        return BigBlueButtonApiResponse::output($response)->merge($meeting);
    }

    public static function joinMeeting(Meeting $meeting, string $fullName = null)
    {
        $inst = self::getInstance();
        $response = $inst->bbb->joinMeeting($inst->joinMeetingParameters($meeting, null, $fullName));
        return BigBlueButtonApiResponse::output($response);
    }

    /**
     * @param Meeting $meeting
     * @return CreateMeetingParameters
     */
    public function createMeetingParameters(Meeting $meeting)
    {
        $params = new CreateMeetingParameters($meeting->id, $meeting->name);
        $params->setAllowRequestsWithoutSession($meeting->allowRequestsWithoutSession);
        //$params->setAttendeePassword(self::decrypt($meeting->attendeePW)); -> join role use instead
        //$params->setModeratorPassword(self::decrypt($meeting->moderatorPW));  -> join role use instead
        $params->setWelcomeMessage($meeting->welcome);
        $params->setRecord($meeting->record);
        $params->setAutoStartRecording($meeting->autoStartRecording);
        $params->setWebcamsOnlyForModerator($meeting->webcamsOnlyForModerator);
        $params->setMuteOnStart($meeting->muteOnStart);
        $params->setLockSettingsDisableMic($meeting->lockSettingsDisableMic);
        $params->setAllowModsToUnmuteUsers($meeting->allowModsToUnmuteUsers);
        $params->setAllowModsToEjectCameras($meeting->allowModsToEjectCameras);
        $params->setMeetingLayout($meeting->meetingLayout);
        $params->setEndWhenNoModeratorDelayInMinutes($meeting->endWhenNoModeratorDelayInMinutes);

        $logoutUrl = URL::route('meeting_logout', ['id' => $meeting->id]);
        $params->setLogoutUrl(Str::replace('http', 'https', $logoutUrl));

        $recordUrl = URL::route('callback_end', ['id' => $meeting->id]);
        $params->setEndCallbackUrl(Str::replace('http', 'https', $recordUrl));

        $recordUrl = URL::route('callback_record_ready', ['id' => $meeting->id]);
        $params->setRecordingReadyCallbackUrl(Str::replace('http', 'https', $recordUrl));
        return $params;
    }

    /**
     * @param Meeting $meeting
     * @return EndMeetingParameters
     */
    public function endMeetingParameters(Meeting $meeting): EndMeetingParameters
    {
        return new EndMeetingParameters($meeting->meetingID);
    }

    private static function decrypt(string $string)
    {
        return Crypt::decrypt($string);
    }

    /**
     * @param Meeting $meeting
     * @return IsMeetingRunningParameters
     */
    public function isMeetingRuninngParameters(Meeting $meeting): IsMeetingRunningParameters
    {
        return new IsMeetingRunningParameters($meeting->id);
    }

    public function getMeetingInfoParameters(Meeting $meeting)
    {
        return new GetMeetingInfoParameters($meeting->id);
    }

    public function joinMeetingParameters(Meeting $meeting, User $user = null, string $visibleName = null)
    {
        if ($user === null) {
            $user = Auth::user();
        }
        /** @var Participant $participant */
        $participant = $meeting->participants()->where('userId', $user->id)->first();
        //MODERATOR or VIEWER
        $role = $participant->isModerator ? 'MODERATOR' : 'VIEWER';
        $fullName = $visibleName ?? $user->lastname . ' ' . $user->firstname;

        $parameters = new JoinMeetingParameters($meeting->id);
        $parameters->setUserId($user->id);
        $parameters->setAvatarURL(URL::route('avatar', ['id' => $user->id, 'size' => 64]));
        $parameters->setRole($role);
        $parameters->setDefaultLayout($participant->defaultLayout); // TODO change from frontend
        $parameters->setExcludeFromDashboard($participant->excludeFromDashboard);
        $parameters->setGuest($participant->guest);
        $parameters->setUsername($fullName);
        $parameters->setRedirect(false);
        $parameters->setCustomParameter('errorRedirectUrl', URL::route('join_error', ['id' => $meeting->id, 'userId' => $user->id]));
        return $parameters;
    }

    public function hookParameters(Meeting $meeting)
    {
        $url = URL::route('callback_hooks', ['id' => $meeting->id]);
        $parameters = new HooksCreateParameters($url);
        $parameters->setMeetingId($meeting->id);
        $parameters->setGetRaw(true);
        return $parameters;
    }

    public static function listHooks()
    {
        $inst = self::getInstance();
        $inst->bbb->hooksList();
    }

    /**
     * @param Meeting $meeting
     * @return bool
     */
    public static function addHooks(Meeting $meeting): bool
    {
        $inst = self::getInstance();
        $res = $inst->bbb->hooksCreate($inst->hookParameters($meeting));
        dd($res);
        if ($res->success()) {
            $meeting->hookId = $res->getHookId();
            $meeting->save();
            Log::info('BBB HOOK CREATE [' . $meeting->id . '] with hookId ' . $meeting->hookId);
        }
        return $res->success();
    }

    /**
     * @param Meeting $meeting
     * @return bool
     */
    public static function deleteHooks(Meeting $meeting): bool
    {
        $inst = self::getInstance();
        $res = $inst->bbb->hooksDestroy($meeting->hookId);
        if ($res->success()) {
            $meeting->hookId = null;
            $meeting->save();
            Log::info('BBB HOOK DELETE [' . $meeting->id . '] for hookId ' . $meeting->hookId);
        }
        return $res->success();
    }

}
