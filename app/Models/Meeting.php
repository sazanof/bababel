<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Meeting
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting query()
 * @property int $id
 * @property int $status
 * @property int $userId
 * @property string $date
 * @property string $name
 * @property string $meetingID
 * @property string $welcome
 * @property string|null $dialNumber
 * @property int|null $voiceBridge
 * @property int $maxParticipants
 * @property string|null $logoutURL
 * @property int $record
 * @property int $duration
 * @property int $isBreakout
 * @property string|null $parentMeetingID
 * @property int|null $sequence
 * @property int $freeJoin
 * @property int $breakoutRoomsPrivateChatEnabled
 * @property int $breakoutRoomsRecord
 * @property string|null $meta
 * @property string|null $moderatorOnlyMessage
 * @property int $autoStartRecording
 * @property int $allowStartStopRecording
 * @property string $webcamsOnlyForModerator
 * @property string|null $bannerText
 * @property string|null $bannerColor
 * @property int $muteOnStart
 * @property int $allowModsToUnmuteUsers
 * @property int $lockSettingsDisableCam
 * @property int $lockSettingsDisableMic
 * @property int $lockSettingsDisablePrivateChat
 * @property int $lockSettingsDisablePublicChat
 * @property int $lockSettingsDisableNotes
 * @property int $lockSettingsHideUserList
 * @property int $lockSettingsLockOnJoin
 * @property int $lockSettingsLockOnJoinConfigurable
 * @property int $lockSettingsHideViewersCursor
 * @property string $guestPolicy
 * @property int $meetingKeepEvents
 * @property int $endWhenNoModerator
 * @property int $endWhenNoModeratorDelayInMinutes
 * @property string $meetingLayout
 * @property int $learningDashboardCleanupDelayInMinutes
 * @property int $allowModsToEjectCameras
 * @property int $allowRequestsWithoutSession
 * @property int $userCameraCap
 * @property int $meetingCameraCap
 * @property int $meetingExpireIfNoUserJoinedInMinutes
 * @property int $meetingExpireWhenLastUserLeftInMinutes
 * @property string|null $groups
 * @property string|null $logo
 * @property string|null $disabledFeatures
 * @property string|null $disabledFeaturesExclude
 * @property int $preUploadedPresentationOverrideDefault
 * @property int $notifyRecordingIsOn
 * @property string|null $presentationUploadExternalUrl
 * @property string|null $presentationUploadExternalDescription
 * @property int $recordFullDurationMedia
 * @property string|null $preUploadedPresentation
 * @property string|null $preUploadedPresentationName
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereAllowModsToEjectCameras($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereAllowModsToUnmuteUsers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereAllowRequestsWithoutSession($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereAllowStartStopRecording($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereAutoStartRecording($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereBannerColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereBannerText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereBreakoutRoomsPrivateChatEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereBreakoutRoomsRecord($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereDialNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereDisabledFeatures($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereDisabledFeaturesExclude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereEndWhenNoModerator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereEndWhenNoModeratorDelayInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereFreeJoin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereGroups($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereGuestPolicy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereIsBreakout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereLearningDashboardCleanupDelayInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereLockSettingsDisableCam($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereLockSettingsDisableMic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereLockSettingsDisableNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereLockSettingsDisablePrivateChat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereLockSettingsDisablePublicChat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereLockSettingsHideUserList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereLockSettingsHideViewersCursor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereLockSettingsLockOnJoin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereLockSettingsLockOnJoinConfigurable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereLogoutURL($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereMaxParticipants($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereMeetingCameraCap($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereMeetingExpireIfNoUserJoinedInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereMeetingExpireWhenLastUserLeftInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereMeetingID($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereMeetingKeepEvents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereMeetingLayout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereModeratorOnlyMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereMuteOnStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereNotifyRecordingIsOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereParentMeetingID($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting wherePreUploadedPresentation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting wherePreUploadedPresentationName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting wherePreUploadedPresentationOverrideDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting wherePresentationUploadExternalDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting wherePresentationUploadExternalUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereRecord($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereRecordFullDurationMedia($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereSequence($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereUserCameraCap($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereVoiceBridge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereWebcamsOnlyForModerator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereWelcome($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $participants
 * @property-read int|null $participants_count
 * @method static \Database\Factories\MeetingFactory factory($count = null, $state = [])
 * @property-read \App\Models\User|null $owner
 * @property int|null $hookId
 * @method static \Illuminate\Database\Eloquent\Builder|Meeting whereHookId($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Document> $documents
 * @property-read int|null $documents_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Recording> $records
 * @property-read int|null $records_count
 * @mixin \Eloquent
 */
class Meeting extends Model
{
    use HasFactory;

    /**
     * id
     * status
     * userId
     * date
     * name
     * meetingID
     * welcome
     * dialNumber
     * voiceBridge
     * maxParticipants
     * logoutURL
     * record
     * duration
     * isBreakout
     * parentMeetingID
     * sequence
     * freeJoin
     * breakoutRoomsPrivateChatEnabled
     * breakoutRoomsRecord
     * meta
     * moderatorOnlyMessage
     * autoStartRecording
     * allowStartStopRecording
     * webcamsOnlyForModerator
     * bannerText
     * bannerColor
     * muteOnStart
     * allowModsToUnmuteUsers
     * lockSettingsDisableCam
     * lockSettingsDisableMic
     * lockSettingsDisablePrivateChat
     * lockSettingsDisablePublicChat
     * lockSettingsDisableNotes
     * lockSettingsHideUserList
     * lockSettingsLockOnJoin
     * lockSettingsLockOnJoinConfigurable
     * lockSettingsHideViewersCursor
     * guestPolicy
     * meetingKeepEvents
     * endWhenNoModerator
     * endWhenNoModeratorDelayInMinutes
     * meetingLayout
     * learningDashboardCleanupDelayInMinutes
     * allowModsToEjectCameras
     * allowRequestsWithoutSession
     * userCameraCap
     * meetingCameraCap
     * meetingExpireIfNoUserJoinedInMinutes
     * meetingExpireWhenLastUserLeftInMinutes
     * groups
     * logo
     * disabledFeatures
     * disabledFeaturesExclude
     * preUploadedPresentationOverrideDefault
     * notifyRecordingIsOn
     * presentationUploadExternalUrl
     * presentationUploadExternalDescription
     * recordFullDurationMedia
     * preUploadedPresentation
     * preUploadedPresentationName
     **/

    public const STATUS_NEW = 0;
    public const STATUS_CREATED = 1;
    public const STATUS_PENDING = 2;
    public const STATUS_CLOSED = 3;

    public const ALWAYS_ACCEPT = 'ALWAYS_ACCEPT';
    public const ALWAYS_DENY = 'ALWAYS_DENY';
    public const ASK_MODERATOR = 'ASK_MODERATOR';

    protected $fillable = [
        'userId',
        'meetingID',
        'date',
        'name',
        'welcome',
        'record',
        'autoStartRecording',
        'webcamsOnlyForModerator',
        'muteOnStart',
        'lockSettingsDisableMic',
        'allowModsToUnmuteUsers',
        'allowModsToEjectCameras',
        'meetingLayout',
        'status',
        'guestPolicy'
    ];

    protected $casts = [
        'record' => 'boolean',
        'autoStartRecording' => 'boolean',
        'webcamsOnlyForModerator' => 'boolean',
        'muteOnStart' => 'boolean',
        'lockSettingsDisableMic' => 'boolean',
        'allowModsToUnmuteUsers' => 'boolean',
        'allowModsToEjectCameras' => 'boolean',
    ];

    public static array $selectableFields = [
        'meetings.id',
        'meetings.userId',
        'meetings.meetingID',
        'meetings.date',
        'meetings.name',
        'meetings.welcome',
        'meetings.record',
        'meetings.autoStartRecording',
        'meetings.webcamsOnlyForModerator',
        'meetings.muteOnStart',
        'meetings.lockSettingsDisableMic',
        'meetings.allowModsToUnmuteUsers',
        'meetings.allowModsToEjectCameras',
        'meetings.meetingLayout',
        'meetings.status',
        'meetings.guestPolicy'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function participants()
    {
        return $this->hasManyThrough(
            User::class,
            Participant::class,
            'meetingId',
            'id',
            'id',
            'userId'
        )
            ->select([
                'participants.isModerator',
                'participants.isOrganizer',
                'participants.link',
                'users.id',
                'users.firstname',
                'users.lastname',
                'users.email',
                'users.photo',
                'users.phone',
                'users.position',
                'users.department'
            ])
            ->selectRaw('participants.id as participants_tid')
            ->orderBy('isOrganizer', 'DESC')
            ->orderBy('isModerator', 'DESC')
            ->orderBy('users.lastname');
    }

    public function owner()
    {
        return $this->hasOne(User::class, 'id', 'userId')->select([
            'users.id',
            'users.firstname',
            'users.lastname',
            'users.photo',
            'users.phone',
            'users.email',
            'users.position',
            'users.department'
        ]);
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'meetingId', 'id');
    }

    public function records()
    {
        return $this->hasMany(Recording::class, 'meetingId', 'id');
    }
}
