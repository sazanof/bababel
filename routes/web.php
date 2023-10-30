<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MeetingsController;
use App\Http\Controllers\UsersController;
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
Route
    ::prefix('panel')->group(function () {
        Route::prefix('search')->group(function () {
            Route::get('users', [UsersController::class, 'searchUsers']);
        });
        Route::prefix('meetings')->group(function () {
            Route::post('', [MeetingsController::class, 'addMeeting']);
            Route::post('id', [MeetingsController::class, 'editMeeting'])->where('id', '[0-9]+');
        });
    });
Route::get('/', [AuthController::class, 'index']);


/** PANEL ROUTES (ACCESS ONLY AUTHENTICATED USERS AND ADMINS) */

/** ADMIN ROUTES (ACCESS ONLY ADMINS) */
