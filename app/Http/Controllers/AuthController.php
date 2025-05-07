<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;

/**
 * @OA\Info(title="Movies API", version="1.0")
 * @OA\Tag(name="Movies", description="Endpoint Untuk Manage Movies")
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Authentication"},
     *     summary="Register user baru",
     *     description="Membuat user baru dan mengembalikan JWT token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *                 @OA\Property(property="password", type="string", example="secret123")
     *             ),
     *             @OA\Property(property="token", type="string", example="jwt_token_here"),
     *             @OA\Property(property="expires_at", type="string", example="2025-01-01 12:00:00 ( +1 Jam )")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Authentication"},
     *     summary="Login user",
     *     description="Melakukan login dan mengembalikan JWT token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User login successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User login successfully"),
     *             @OA\Property(property="token", type="string", example="jwt_token_here"),
     *             @OA\Property(property="expires_at", type="string", example="2025-01-01 12:00:00 ( +1 Jam )")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid email or password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
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
