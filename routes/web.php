<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BababelController;
use App\Http\Controllers\MeetingsController;
use App\Http\Controllers\UsersController;
use App\Http\Middleware\AlreadyJoined;
use App\Http\Middleware\BbbAllowedHosts;
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
        Route::prefix('search')->group(function () {
            Route::get('users', [UsersController::class, 'searchUsers']);
        });
        Route::prefix('dashboard')->group(function () {
            Route::get('meetings', [MeetingsController::class, 'getDashboardMeetings']);

        });
        Route::prefix('meetings')->group(function () {
            /** ADMIN ROUTES TO START, STOP, END, CREATE MEETING */
            Route::post('', [MeetingsController::class, 'addMeeting']);
            Route::post('{criteria}', [MeetingsController::class, 'getMeetings'])->where('criteria', '[a-z]+');
            Route::middleware(IsMeetingOwner::class)->group(function () {
                Route::post('{id}', [MeetingsController::class, 'editMeeting'])
                    ->where('id', '[0-9]+');
                Route::get('{id}', [MeetingsController::class, 'getMeeting'])
                    ->where('id', '[0-9]+');
                Route::get('{id}/start', [BababelController::class, 'startMeeting'])
                    ->where('id', '[0-9]+');
                Route::get('{id}/stop', [BababelController::class, 'stopMeeting'])
                    ->where('id', '[0-9]+');
                Route::get('{id}/running', [BababelController::class, 'isRunning'])
                    ->where('id', '[0-9]+');
                Route::get('{id}/logout', function (int $id) {
                    return 'this is a logout page. meeting id: ' . $id;
                })
                    ->name('meeting_logout')
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
    });
Route::get('/', [AuthController::class, 'index']);



/** ADMIN ROUTES (ACCESS ONLY ADMINS) */
