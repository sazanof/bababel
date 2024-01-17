<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BababelController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\MeetingsController;
use App\Http\Controllers\RecordingsController;
use App\Http\Controllers\UserNotificationsController;
use App\Http\Controllers\UsersController;
use App\Http\Middleware\AlreadyJoined;
use App\Http\Middleware\BbbAllowedHosts;
use App\Http\Middleware\CanDeleteRecord;
use App\Http\Middleware\IsMeetingModerator;
use App\Http\Middleware\IsMeetingOwner;
use Illuminate\Support\Facades\Route;

/** COMMON ROUTES (ACCESS ALL) */
Route
    ::prefix('auth')
    ->controller(AuthController::class)
    ->group(function () {
        Route::get('check', 'check');
        Route::post('login', 'login');
        Route::get('logout', 'logout');
    });
Route::prefix('users')->group(function () {
    Route::get('{id}/avatar/{size?}', [UsersController::class, 'getAvatar'])->name('avatar');
});
/** PANEL ROUTES (ACCESS ONLY AUTHENTICATED USERS AND ADMINS) */
Route
    ::middleware('auth')
    ->prefix('panel')
    ->group(function () {
        Route::prefix('account')->group(function () {
            Route::get('notifications', [UserNotificationsController::class, 'getNotificationSettings']);
            Route::post('notifications/{id}', [UserNotificationsController::class, 'addNotification']);
            Route::delete('notifications/{id}', [UserNotificationsController::class, 'removeNotification']);
        });
        Route::prefix('search')->group(function () {
            Route::get('users', [UsersController::class, 'searchUsers']);
        });
        Route::prefix('dashboard')->group(function () {
            Route::get('meetings', [MeetingsController::class, 'getDashboardMeetings']);
        });
        Route::prefix('profile')->group(function () {
            Route::post('avatar', [UsersController::class, 'updateAvatar']);
            Route::get('avatar/{size?}', [UsersController::class, 'getProfileAvatar'])->name('avatar');
        });
        Route::prefix('meetings')->group(function () {
            /** ADMIN ROUTES TO START, STOP, END, CREATE MEETING */
            Route::post('', [MeetingsController::class, 'addMeeting']);
            Route::post('{criteria}', [MeetingsController::class, 'getMeetings'])->where('criteria', '[a-z]+');
            Route::middleware(IsMeetingModerator::class)->group(function () {
                Route::get('{id}/start', [BababelController::class, 'startMeeting'])
                    ->where('id', '[0-9]+');
                Route::get('{id}/stop', [BababelController::class, 'stopMeeting'])
                    ->where('id', '[0-9]+');
            });
            Route::middleware(IsMeetingOwner::class)->group(function () {
                Route::post('{id}', [MeetingsController::class, 'editMeeting'])
                    ->where('id', '[0-9]+');
                Route::delete('{id}', [MeetingsController::class, 'deleteMeeting'])
                    ->where('id', '[0-9]+');
                Route::get('{id}', [MeetingsController::class, 'getMeeting'])
                    ->where('id', '[0-9]+');
                Route::get('{id}/running', [BababelController::class, 'isRunning'])
                    ->where('id', '[0-9]+');
                Route::get('{id}/error-join-meeting', function (int $id, int $userId) {
                    return 'this is a join error page. meeting id: ' . $id . ' , userId ' . $userId;
                })
                    ->name('join_error')
                    ->where('id', '[0-9]+');
            });
            Route::middleware(AlreadyJoined::class)
                ->post('{id}/join', [BababelController::class, 'joinMeeting'])
                ->where('id', '[0-9]+');
            Route::get('{id}/info', [BababelController::class, 'getInfo'])
                ->where('id', '[0-9]+');
        });
        Route::delete('documents/{id}', [MeetingsController::class, 'removeDocument']);

        /** Recordings manager */
        Route::prefix('records')->group(function () {
            Route::middleware(CanDeleteRecord::class)
                ->delete('{id}', [RecordingsController::class, 'deleteRecording'])
                ->where('id', '[0-9]+');
        });
        Route::prefix('join')->group(function () {
            Route::get('{pid}/info', [MeetingsController::class, 'getParticipantInfo'])
                ->where('id', '[0-9]+');
        });
    });
/** CALLBACKS */
Route
    ::middleware(BbbAllowedHosts::class)
    ->prefix('meetings')->group(function () {
        Route::get('{id}/callback/end', [BababelController::class, 'callbackEndMeeting'])
            ->where('id', '[0-9]+')
            ->name('callback_end');
        Route::post('{id}/callback/record-ready', [BababelController::class, 'callbackRecordReady'])
            ->where('id', '[0-9]+')
            ->name('callback_record_ready');
        Route::post('{id}/callback/hooks', [BababelController::class, 'callbackHooks'])
            ->where('id', '[0-9]+')
            ->name('callback_hooks');
        Route::get('{id}/cover.{format?}', [MeetingsController::class, 'makeCover'])
            ->where('id', '[0-9]+')
            ->name('make_cover');
    });
//Route::get('/test', function () {
//    $parameters = 'eyJhbGciOiJIUzI1NiJ9.eyJtZWV0aW5nX2lkIjoiNjVhNzY4NDRhYThmODYuNDU5NTM3NzQiLCJyZWNvcmRfaWQiOiI5ZjM1MWRiYjg4MmE3YjQ1NDViMWY4NWI5MjlhMDkzYTg3OGJiZjUyLTE3MDU0NzE2MDM5MjMifQ.VEi6yMChnOUWR7Bqjl5g5mrANykrfeSqKEYSsk84XBk';
//
//
//});
Route::get('/', [AuthController::class, 'index']);
Route::prefix('mail')->group(function () {
    Route::get('logo', [MailController::class, 'getLogo']);
    Route::get('header', [MailController::class, 'getHeader']);
});
Route::get('/panel/meetings/{id}/view', [MeetingsController::class, 'viewMeeting'])->where('id', '[0-9]+');
// No middleware, case we do not know user`s id
Route::post('/panel/meetings/{id}/join-as-guest', [BababelController::class, 'joinMeetingAsGuest'])->where('id', '[0-9]+');




/** ADMIN ROUTES (ACCESS ONLY ADMINS) */
