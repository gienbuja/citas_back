<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\JwtToken;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Sin datos de sesion'], 401);
        }

        try {
            $credentials = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
            $user = User::where('id', $credentials->sub)->first();
            
            if (!$user) {
                return response()->json(['error' => 'Usuario no encontrado'], 401);
            }

            // Verificar si el token está en la base de datos
            $tokenExists = JwtToken::where('token', $token)->exists();

            if (!$tokenExists) {
                return response()->json(['error' => 'Sesion cerrada'], 401);
            }

            Auth::login($user);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Sesion invalida', 'e' => $e->getMessage()], 401);
        }

        return $next($request);
    }
}