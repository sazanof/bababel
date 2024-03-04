<?php

namespace App\Helpers;

use App\Mail\AddParticipantMail;
use App\Mail\DeleteMeetingMail;
use App\Mail\DeleteParticipantMail;
use App\Mail\NewMeetingMail;
use App\Mail\RecordingReadyMail;
use App\Mail\UpdateMeetingDateMail;
use App\Models\Meeting;
use App\Models\Notification;
use App\Models\Participant;
use App\Models\Recording;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Mail\Mailables\Address;

/**
 * @method static bool subscribeOnMeetingCreate(User $user = null)
 * @method static bool subscribeOnMeetingUpdateDate(User $user = null)
 * @method static bool subscribeOnMeetingDelete(User $user = null)
 * @method static bool subscribeOnParticipantCreate(User $user = null)
 * @method static bool subscribeOnParticipantDelete(User $user = null)
 */
class NotificationHelper
{
    public const NOTY_MEETING_CREATE = 'meeting.create';
    public const NOTY_MEETING_UPDATE_DATE = 'meeting.update.date';
    public const NOTY_MEETING_DELETE = 'meeting.delete';
    public const NOTY_PARTICIPANT_CREATE = 'participant.create';
    public const NOTY_PARTICIPANT_DELETE = 'participant.delete';
    public const NOTY_RECORDING_READY = 'recording.ready';

    public static function getSystemNotificationEmails()
    {
        $emails = env('FEEDBACK_EMAILS', '');
        return explode(',', $emails);
    }

    /**
     * @param string $key
     * @param User|null $user
     * @return bool
     */
    public static function subscribeOn(string $key, User $user = null): bool
    {
        if (is_null($user)) {
            $user = Auth::user();
        }
        $notification = Notification::where('key', $key)->first();
        if (!is_null($notification)) {
            return UserNotification
                    ::where('notificationId', $notification->id)
                    ->where('userId', $user->id)
                    ->count() === 1;
        }
        return false;
    }

    public static function __callStatic(string $name, array $arguments)
    {
        $needle = 'subscribeOn';
        if (Str::startsWith($name, $needle)) {
            $key = Str::replace($needle, '', $name);
            $key = preg_split('/(?=[A-Z])/', $key, -1, PREG_SPLIT_NO_EMPTY);
            $ar = Arr::map($key, function ($el) {
                return Str::lower($el);
            });
            if (!empty($key)) {
                $key = implode('.', $ar);
            }
            return self::subscribeOn($key, !empty($arguments) && $arguments[0] instanceof User ? $arguments[0] : null);
        }
        return false;
    }

    public static function getMeetingModerators(Meeting $meeting, string $key)
    {
        $moderators = null;
        $participants = $meeting->participants()->where('isModerator', true)->get();
        if ($participants->isNotEmpty()) {
            foreach ($participants as $participant) {
                if (
                    filter_var($participant->email, FILTER_VALIDATE_EMAIL) &&
                    self::subscribeOn($key, $participant)
                ) {
                    $moderators[] = new Address($participant->email, $participant->lastname . ' ' . $participant->firstname);
                }
            }
        }
        return $moderators;
    }

    /**
     * @param array|Meeting $meeting
     * @param string $key
     * @return ?array
     */
    public static function getMeetingRecipientsByNotificationKey(array|Meeting $meeting, string $key): ?array
    {
        $participants = null;
        if ($meeting instanceof Meeting) {
            $db_participants = $meeting->participants;
        } else {
            $id = $meeting['id'];
            $db_participants = Participant::where('meetingId', $id)->get();
        }
        if ($db_participants->isNotEmpty()) {
            foreach ($meeting->participants as $participant) {
                if (
                    filter_var($participant->email, FILTER_VALIDATE_EMAIL) &&
                    self::subscribeOn($key, $participant)
                ) {
                    $participants[] = new Address($participant->email, $participant->lastname . ' ' . $participant->firstname);
                }
            }
        }

        return $participants;
    }

    public static function sendNotificationsToMeetingParticipants(Meeting $meeting, string $key)
    {
        $participants = $meeting->participants;
    }

    /**
     * @param Meeting $meeting
     * @return void
     */
    public static function sendNotificationsOnMeetingCreate(Meeting $meeting): void
    {
        $recipients = NotificationHelper::getMeetingRecipientsByNotificationKey($meeting, NotificationHelper::NOTY_MEETING_CREATE);

        if ($recipients !== null) {
            /** @var Address $recipient */
            foreach ($recipients as $recipient) {
                $user = User::where('email', $recipient->address)->first();
                Mail
                    ::to($recipient)
                    ->queue(new NewMeetingMail($meeting, $user));
            }
        }
    }

    public static function sendNotificationsOnRecordingReady(Recording $recording): void
    {
        try {
            $recording->load('meeting');
            $recipients = NotificationHelper::getMeetingModerators($recording->meeting, NotificationHelper::NOTY_RECORDING_READY);
            if (!is_null($recipients)) {
                /** @var Address $recipient */
                foreach ($recipients as $recipient) {
                    Mail
                        ::to($recipient)
                        ->queue(new RecordingReadyMail($recording));
                }
            }
        } catch (\Exception $e) {
            Log::error('[BBB] [sendNotificationsOnRecordingReady] failed with message: ' . $e->getMessage());
        }

    }

    /**
     * @param Meeting $meeting
     * @return void
     */
    public static function sendNotificationsOnMeetingUpdateDate(Meeting $meeting): void
    {
        $recipients = NotificationHelper::getMeetingRecipientsByNotificationKey($meeting, NotificationHelper::NOTY_MEETING_UPDATE_DATE);
        if ($recipients !== null) {
            /** @var Address $recipient */
            foreach ($recipients as $recipient) {
                $user = User::where('email', $recipient->address)->first();
                Mail
                    ::to($recipient)
                    ->queue(new UpdateMeetingDateMail($meeting, $user));
            }
        }
    }

    /**
     * @param array|Meeting $meeting
     * @param array|null $recipients
     * @return void
     */
    public static function sendNotificationsOnMeetingDelete(array|Meeting $meeting, ?array $recipients = null): void
    {
        if ($meeting instanceof Meeting) {
            if (is_null($recipients)) {
                $recipients = NotificationHelper::getMeetingRecipientsByNotificationKey($meeting, NotificationHelper::NOTY_MEETING_DELETE);
            }
        }


        if ($recipients !== null) {
            /** @var Address $recipient */
            foreach ($recipients as $recipient) {
                $user = User::where('email', $recipient->address)->first();
                Mail
                    ::to($recipient)
                    ->queue(new DeleteMeetingMail($meeting, $user));
            }
        }
    }

    /**
     * @param User $user
     * @param Meeting $meeting
     * @return void
     */
    public static function sendNotificationsOnParticipantCreate(User $user, Meeting $meeting): void
    {
        if (self::subscribeOnParticipantCreate($user)) {
            Mail
                ::to($user)
                ->queue(new AddParticipantMail($user, $meeting));
        }
    }

    /**
     * @param User $user
     * @param Meeting $meeting
     * @return void
     */
    public static function sendNotificationsOnParticipantDelete(User $user, Meeting $meeting): void
    {
        if (self::subscribeOnParticipantDelete($user)) {
            Mail
                ::to($user)
                ->queue(new DeleteParticipantMail($user, $meeting));
        }
    }
}
