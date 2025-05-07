<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class CustomJwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // PESAN ERROR
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            return response()->json([
                'error' => 'Unauthorized, token kadaluarsa XC'
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'error' => 'Unauthorized, token salah :C'
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Unauthorized, token diperlukan :D'
            ], 401);
        }

        // HEADER ONLY
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['error' => 'Token hanya dapat digunakan pada header :P'], 401);
        }

        return $next($request);
    }
}
