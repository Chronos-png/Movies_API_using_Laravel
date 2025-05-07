<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        $decoded = JWTAuth::setToken($token)->getPayload();
        $expirationTime = (int) $decoded['exp'];

        try {
            $expirationDate = Carbon::createFromTimestamp($expirationTime)->setTimezone('Asia/Jakarta');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid expiration timestamp'], 500);
        }

        return response()->json([
            'message' => 'User registered successfully',
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'password' => $request->password,
            ],
            'token' => $token,
            'expires_at' => $expirationDate->toDateTimeString() . ' ( +1 Jam )',
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid email or password'], 401);
        }

        $decoded = JWTAuth::setToken($token)->getPayload();
        $expirationTime = (int) $decoded['exp'];

        try {
            $expirationDate = Carbon::createFromTimestamp($expirationTime)->setTimezone('Asia/Jakarta');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid expiration timestamp'], 500);
        }

        return response()->json([
            'message' => 'User login successfully',
            'token' => $token,
            'expires_at' => $expirationDate->toDateTimeString() . ' ( +1 Jam )',
        ]);
    }
}
