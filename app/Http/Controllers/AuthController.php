<?php

namespace App\Http\Controllers;

use App\Exceptions\AuthFailedException;
use App\Http\Requests\LoginRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function index()
    {
        return view('index');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function check()
    {
        $status = 403;
        $user = null;
        if (Auth::check()) {
            $user = Auth::user();
            $status = 200;
        }
        return response()->json($user, $status);
    }

    /**
     * @param LoginRequest $request
     * @return \App\Models\User|\Illuminate\Contracts\Auth\Authenticatable|null
     * @throws AuthFailedException
     */
    public function login(LoginRequest $request): \App\Models\User|\Illuminate\Contracts\Auth\Authenticatable|null
    {
        $validated = $request->validated();
        $username = $validated['username'];
        $password = $validated['password'];
        $credentials = [
            'samaccountname' => $username,
            'password' => $password,
        ];
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            return $user;
        }
        throw new AuthFailedException();

    }

    /**
     * @return bool[]
     */
    public function logout(): array
    {
        Auth::logout();
        return ['success' => true];
    }
}
