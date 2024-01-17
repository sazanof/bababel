<?php

namespace App\Helpers;

use App\Models\Document;
use App\Models\Meeting;
use App\Models\Participant;
use App\Models\Recording;
use App\Models\User;
use BigBlueButton\Parameters\CreateMeetingParameters;
use BigBlueButton\Parameters\DeleteRecordingsParameters;
use BigBlueButton\Parameters\EndMeetingParameters;
use BigBlueButton\Parameters\GetMeetingInfoParameters;
use App\Helpers\Parameters\GetRecordingsParameters;
use BigBlueButton\Parameters\HooksCreateParameters;
use BigBlueButton\Parameters\InsertDocumentParameters;
use BigBlueButton\Parameters\IsMeetingRunningParameters;
use BigBlueButton\Parameters\JoinMeetingParameters;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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

    protected const STATE_PUBLISHED = 'published';

    public function __construct()
    {
        $this->url = Config::get('app.bbb_base_url');
        $this->token = Config::get('app.bbb_secret');
        $this->bbb = new Bababel($this->url, $this->token);
    }

    /**
     * @return string
     */
    public static function generateMeetingId(): string
    {
        return uniqid("", true);
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
            //$meeting->date = Carbon::createFromTimeString($response->getCreationDate());
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

    public function insertDocumentParameters(Meeting $meeting)
    {
        $params = new InsertDocumentParameters();
        $params->setMeetingId($meeting->meetingID);
        $documents = Document::where('meetingId', $meeting->id)->get();
        if ($documents->isNotEmpty()) {
            foreach ($documents as $document) {
                $params->addPresentation(URL::to(Storage::url($document->path)));
            }
        } else {
            //Upload default presentation
            //$params->addPresentation();
        }
        return $params;
    }

    public static function insertDocument(Meeting $meeting)
    {
        $inst = self::getInstance();
        $response = $inst->bbb->insertDocument($inst->insertDocumentParameters($meeting));
        return BigBlueButtonApiResponse::output($response)->merge($meeting);
    }

    /**
     * @param Meeting $meeting
     * @return CreateMeetingParameters
     */
    public function createMeetingParameters(Meeting $meeting)
    {
        $params = new CreateMeetingParameters($meeting->meetingID, $meeting->name);
        $params->setAllowRequestsWithoutSession(true);
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
        $params->setGuestPolicy($meeting->guestPolicy);
        $params->setBannerText(env('APP_NAME'));


        $documents = Document::where('meetingId', $meeting->id)->get();
        if ($documents->isNotEmpty()) {
            foreach ($documents as $document) {
                $params->addPresentation(URL::to(Storage::url($document->path)));
            }
        } else {
            $params->addPresentation(URL::route('make_cover', ['id' => $meeting->id, 'format' => 'jpg']));
        }
        $logoutUrl = URL::to('/#/meetings/' . $meeting->id . '/logout');
        $params->setLogoutUrl($logoutUrl);

        $recordUrl = URL::route('callback_end', ['id' => $meeting->id]);
        $params->setEndCallbackUrl($recordUrl);

        $recordUrl = URL::route('callback_record_ready', ['id' => $meeting->id]);
        $params->setRecordingReadyCallbackUrl($recordUrl);
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
        return new IsMeetingRunningParameters($meeting->meetingID);
    }

    public function getMeetingInfoParameters(Meeting $meeting)
    {
        return new GetMeetingInfoParameters($meeting->meetingID);
    }

    public function joinMeetingParameters(Meeting $meeting, User $user = null, string $visibleName = null)
    {
        if ($user === null) {
            $user = Auth::user();
        }

        /** @var Participant $participant */
        $participant = $user instanceof User ? $meeting->participants()->where('userId', $user->id)->first() : null;
        //MODERATOR or VIEWER
        $role = !is_null($participant) && $participant->isModerator ? 'MODERATOR' : 'VIEWER';
        $fullName = $visibleName ?? $user->lastname . ' ' . $user->firstname;

        $parameters = new JoinMeetingParameters($meeting->meetingID);

        if ($user instanceof User) {
            $parameters->setUserId($user->id);
            $parameters->setAvatarURL(URL::route('avatar', ['id' => $user->id, 'size' => 128]));
            $parameters->setCustomParameter('errorRedirectUrl', URL::route('join_error', ['id' => $meeting->id, 'userId' => $user->id]));
        }
        $parameters->setRole($role);
        $parameters->setDefaultLayout(!is_null($participant) ? $participant->defaultLayout : $meeting->meetingLayout); // TODO change from frontend
        $parameters->setExcludeFromDashboard(!is_null($participant) ? $participant->excludeFromDashboard : false);
        // TODO - denied GUEST (is_guest)
        $parameters->setGuest(!is_null($participant) ? $participant->guest : true);
        $parameters->setUsername($fullName);
        $parameters->setRedirect(false);
        return $parameters;
    }

    public function hookParameters(Meeting $meeting)
    {
        $url = URL::route('callback_hooks', ['id' => $meeting->id]);
        $parameters = new HooksCreateParameters($url);
        $parameters->setMeetingId($meeting->meetingID);
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

    /** RECORDINGS MANAGEMENT */

    /**
     * @return GetRecordingsParameters
     */
    public function recordingParameters(): GetRecordingsParameters
    {
        $params = new GetRecordingsParameters();
        $params->setState(self::STATE_PUBLISHED);
        return $params;
    }

    public static function getRecordings(int $limit = null, int $offset = null, Meeting $meeting = null): BigBlueButtonApiResponse
    {
        $limit = $limit ?? 1;
        $offset = $offset ?? 0;
        $inst = self::getInstance();
        $parameters = $inst->recordingParameters();
        $parameters->setLimit($limit);
        $parameters->setOffset($offset);

        $response = $inst->bbb->getRecordings($parameters);
        return BigBlueButtonApiResponse::output($response);
    }

    public static function getRecording(string $recordId): BigBlueButtonApiResponse
    {
        $inst = self::getInstance();
        $parameters = $inst->recordingParameters();
        $parameters->setRecordId($recordId);
        $response = $inst->bbb->getRecordings($parameters);
        return BigBlueButtonApiResponse::output($response);
    }

    /**
     * @param string $parameters
     * @return BababelSignedParameters|null
     */
    public static function parseSignedParameters(string $parameters): ?BababelSignedParameters
    {
        try {
            $jwt = JWT::decode($parameters, new Key(env('BBB_SECRET'), 'HS256'));
            if ($jwt->meeting_id && $jwt->record_id) {
                return new BababelSignedParameters($jwt->meeting_id, $jwt->record_id);
            }
        } catch (SignatureInvalidException $e) {
            Log::error("[BBB] [parseSignedParameters]: " . $e->getMessage());
            return null;
        }
    }

    /**
     * @param Recording $recording
     * @return DeleteRecordingsParameters
     */
    public function deleteRecordingParameters(Recording $recording): DeleteRecordingsParameters
    {
        $params = new DeleteRecordingsParameters();
        $params->setRecordingId($recording->recordId);
        return $params;
    }

    /**
     * @param Recording $recording
     * @return BigBlueButtonApiResponse
     */
    public static function deleteRecording(Recording $recording): BigBlueButtonApiResponse
    {
        $inst = self::getInstance();
        $parameters = $inst->deleteRecordingParameters($recording);
        $response = $inst->bbb->deleteRecordings($parameters);
        return BigBlueButtonApiResponse::output($response);
    }
}
