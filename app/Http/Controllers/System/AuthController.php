<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllerWrapper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // Login: valida credenciales y retorna el token de acceso
    public function login(Request $request)
    {
        return ControllerWrapper::execApiResponse(function () use ($request) {
            $credentials = $request->validate([
                'email'    => 'required|email',
                'password' => 'required|string'
            ]);

            if (!Auth::attempt($credentials)) {
                throw new \Exception('Credenciales inválidas', 401);
            }

            $user = Auth::user();

            // Crea el token
            $newAccessToken = $user->createToken('auth_token');

            $expirationMinutes = config('sanctum.expiration', 60*24);
            $expirationDate = now()->addMinutes($expirationMinutes);

            $newAccessToken->accessToken->update([
                'expires_at' => $expirationDate,
            ]);

            return [
                'data' => [
                    "user"       => $user,
                    "token"      => $newAccessToken->plainTextToken,
                    "expires_at" => $expirationDate->toDateTimeString(),
                ],
                'message' => 'Login exitoso',
                'code'    => 200
            ];
        }, [
            'state' => false,
            'error' => 'No se pudo realizar el login'
        ]);
    }


    // Logout: revoca el token actual del usuario autenticado
    public function logout(Request $request)
    {
        return ControllerWrapper::execApiResponse(function () use ($request) {
            $request->user()->currentAccessToken()->delete();
            return [
                'data' => null,
                'message' => 'logout exitoso',
                'code' => 200
            ];
        }, [
            'state' => false,
            'error' => 'No se pudo realizar el logout'
        ]);
    }
}
