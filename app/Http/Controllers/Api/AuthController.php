<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // POST /api/register
    public function register()
    {
        $user = User::create([
            'username'      => request('username'),
            'email'         => request('email'),
            'password_hash' => Hash::make(request('password')),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token
        ], 201);
    }

    // POST /api/login
    public function login()
    {
        $user = User::where('email', request('email'))->first();

        if (!$user || !Hash::check(request('password'), $user->password_hash)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token
        ]);
    }

    // POST /api/logout
    public function logout()
    {
        request()->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}