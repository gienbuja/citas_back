<?php

namespace App\Http\Controllers;

use App\Models\JwtToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Firebase\JWT\JWT;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $payload = [
                'iss' => "jwt-auth", 
                'sub' => $user->id, 
                'iat' => time(), 
                'exp' => time() + 36000 
            ];

            $token = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

            // Guardar el token en la base de datos
            JwtToken::create([
                'user_id' => $user->id,
                'token' => $token,
            ]);

            return response()->json([
                'token' => $token,
                'user' => $user
            ]);
        } else {
            return response()->json(['error' => 'No tiene permisos de ingreso'], 401);
        }
    }

    public function logout()
    {
        $user = Auth::user();
        $token = request()->bearerToken();

        // Revocar el token
        JwtToken::where('user_id', $user->id)->where('token', $token)->delete();

        Auth::logout();
        return response()->json(['message' => 'Session cerrada']);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json(['message' => 'Creado con exito', 'user' => $user->refresh()]);
    }
}