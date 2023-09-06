<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $token = Auth::attempt([
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);

        if (!$token) {
            return response()->json([
                'message' => 'Incorrect email or password.',
            ], 422);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        return response()->json([
            'data' => [
                'user' => UserResource::make($user),
                'accessToken' => $token,
            ]
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->password),
        ]);

        $token = Auth::login($user);

        return response()->json([
            'data' => [
                'user' => UserResource::make($user),
                'accessToken' => $token,
            ]
        ]);
    }

    public function logout()
    {
        return Auth::logout();
    }

    public function getAuthenticatedUser()
    {
        if (Auth::check()) {
            return UserResource::make(Auth::user());
        }

        return response()->json([
            'message' => 'Unauthenticated.'
        ], 401);
    }
}
