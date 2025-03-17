<?php

namespace App\Http\Controllers;

use App\Models\JwtToken;
use App\Models\User;
use Gate;
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

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $authUser = Auth::user();
            $payload = [
                'iss' => "jwt-auth",
                'sub' => $authUser->id,
                'iat' => time(),
                'exp' => time() + 36000
            ];

            $token = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

            // Guardar el token en la base de datos
            JwtToken::create([
                'user_id' => $authUser->id,
                'token' => $token,
            ]);

            return response()->json(['message' => 'Creado con exito', 'token' => $token, 'user' => $authUser]);
        } else {
            return response()->json(['error' => 'No se pudo autenticar al usuario'], 401);
        }
    }

    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8',
        ]);

        $user = User::find($id);

        if (!$user || !Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json(['error' => 'La contraseña actual no es correcta'], 401);
        }

        $user->password = bcrypt($request->new_password);
        $user->save();

        return response()->json(['message' => 'Contraseña actualizada con éxito']);
    }

    public function getUsers()
    {
        Gate::authorize('admin-only');
        $users = User::with('citas')->get();
        return response()->json($users);
    }

    public function userChangeRol(User $user)
    {
        Gate::authorize('admin-only');

        $user->rol = $user->rol == 'Admin' ? 'Cliente' : 'Admin';
        $user->save();
        $user->refresh();
        $user->citas = $user->citas;
        return response()->json($user);
    }

    public function me(Request $request)
    {
        return response()->json(['message' => 'Usuario autenticado', 'user' =>$request->user()], 200);
    }
}