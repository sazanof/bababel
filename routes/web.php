<?php

use App\Http\Controllers\AuthController;
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
    });
Route::get('/', [AuthController::class, 'index']);


/** PANEL ROUTES (ACCESS ONLY AUTHENTICATED USERS AND ADMINS) */

/** ADMIN ROUTES (ACCESS ONLY ADMINS) */
