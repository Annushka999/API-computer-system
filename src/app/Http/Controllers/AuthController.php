<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\{AuthRequest, RegistrationRequest};
use App\Models\User;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{Auth, Hash};

class AuthController extends Controller
{
    public function login(AuthRequest $request)
    {
        if (!Auth::attempt($request->all())) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed'
            ], 401, ['Content-type' => 'application/json']);
        }

        $token = Auth::user()
            ->createToken('auth_token', ['*'], (new DateTime())->modify('+10 day'))
        ->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'token' => $token
        ], 200, ['Content-type' => 'application/json']);
    }

    public function registration(RegistrationRequest $request)
    {
        $user = User::create([
            'last_name' => $request->last_name,
            'first_name' => $request->first_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('registration_token', ['*'], (new DateTime())->modify('+10 day'))
            ->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'token' => $token
        ]);
    }

    public function logout(): JsonResponse
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout'
        ], 200, ['Content-type' => 'application/json']);
    }

}
