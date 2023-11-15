<?php

namespace App\Helpers;

use App\Mail\NewMeetingMail;
use App\Models\Meeting;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
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

    /**
     * @param Meeting $meeting
     * @param string $key
     * @return ?array
     */
    public static function getMeetingRecipientsByNotificationKey(Meeting $meeting, string $key): ?array
    {
        $participants = null;
        if ($meeting->participants->isNotEmpty()) {
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
}
