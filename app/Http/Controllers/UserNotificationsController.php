<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class UserNotificationsController extends Controller
{
    /**
     * Get USER enabled notifications
     * @param Request $request
     * @return array
     */
    public function getNotificationSettings(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $notifications = Notification::all()->map(function (Notification $notification) {
            return [
                'id' => $notification->id,
                'title' => __($notification->transKey)
            ];
        });
        $enabled = UserNotification::where('userId', $user->id)->get();
        if ($enabled->isNotEmpty()) {
            $enabled = $enabled->map(function (UserNotification $userNotification) {
                return $userNotification->notificationId;
            });
        }
        return [
            'list' => $notifications,
            'enabled' => $enabled
        ];
    }

    public function addNotification($id, Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        UserNotification::insertOrIgnore([
            'userId' => $user->id,
            'notificationId' => $id
        ]);
        return [
            'success' => true
        ];
    }

    public function removeNotification($id, Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        UserNotification::where([
            'userId' => $user->id,
            'notificationId' => $id
        ])->delete();
        return [
            'success' => true
        ];
    }
}
