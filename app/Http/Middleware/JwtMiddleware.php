<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }
        try {
            $credentials = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
            $user = User::find($credentials->sub);

            if (!$user) {
                return response()->json(['error' => 'User not found'], 401);
            }

            Auth::login($user);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid token', 'e'=>$e->getMessage()], 401);
        }

        return $next($request);
    }
}