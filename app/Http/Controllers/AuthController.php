<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'rpe' => 'required',
            'password' => 'required',
        ]);

        $endpoint = "https://servicios.ing.uaslp.mx/ws_cacei/ValidaUsuario.php";
        $clave = "B3E06D96-1562-4713-BCD7-7F762A87F205";
        $payload = [
            'rpe' => $request->rpe,
            'contra' => $request->password,
            'key' => $clave
        ];

        $responseApi = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($endpoint, $payload);

        if ($responseApi->successful()) {

            $responseApi = $responseApi->json();

            if ($responseApi['correcto']) {
                $data = $responseApi['datos'][0];

                // Verificar si el usuario ya existe en la base de datos
                $user = User::where('user_rpe', $data['rpe'])->first();

                // Si el usuario ya existe, no actualiza el rol
                if ($user) {
                    // Si el rol ya es el correcto, no actualiza nada
                    if ($user->user_role !== $data['cargo']) {
                        // Si el rol cambió, solo actualiza el correo
                        $user->update([
                            'user_mail' => $data['correo'],
                        ]);
                    }
                } else {
                    // Si el usuario no existe, lo crea
                    $user = User::create([
                        'user_rpe' => $data['rpe'],
                        'user_mail' => $data['correo'],
                        'user_role' => $data['cargo'],
                    ]);
                }

                $token = $user->createToken('auth_token');

                $token->accessToken->forceFill([
                    'expires_at' => Carbon::now()->addMinutes(20)
                ])->save();

                return response()->json([
                    'correct' => true,
                    'message' => 'Login exitoso',
                    'role' => $data['cargo'],
                    'name' => $data['nombre'],
                    'token' => $token,
                    'rpe' => $data['rpe']
                ]);
            } else {
                return response()->json([
                    'correct' => false,
                    'message' => 'Error en rpe o contra',
                ]);
            }

        } else {
            return response()->json([
                'message' => 'Error en api universitaria',
                'status' => $responseApi->status()
            ], $responseApi->status());
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logout exitoso']);
    }

    public function logoutAll(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        return response()->json(['message' => 'Todas las sesiones cerradas.']);
    }

    public function getUserToken(Request $request)
    {
        $user = $request->user();
        $token = $user->currentAccesstoken();
        return response()->json(compact('user', 'token'));
    }

    public function getAllTokens(Request $request)
    {
        $user = $request->user();

        $activeTokens = $user->tokens()->where(function ($query) {
            $query->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        })->get();

        return response()->json(compact('activeTokens'));
    }
}