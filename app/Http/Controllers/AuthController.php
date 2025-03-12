<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Http;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validar los datos de entrada
        /*$request->validate([
            'rpe' => 'required',
            'password' => 'required',
        ]);*/

        $endpoint = "https://servicios.ing.uaslp.mx/ws_cacei/ValidaUsuario.php";
        $clave = "B3E06D96-1562-4713-BCD7-7F762A87F205";
        $payload = [
            'rpe' => $request->rpe,
            'contra' => $request->password,
            'key' => $clave
        ];

        $responseApi = Http::withHeaders([
            'Content-Type: application/json',
        ])->post($endpoint, $payload);

        if ($responseApi->successful()) {

            $responseApi = $responseApi->json();
            //indices response
            /*"correcto":true,
                "datos":[{
                    "rpe",
                    "nombre",
                    "correo",
                    "cve_area",
                    "area",
                    "cve_carrera",
                    "carrera",
                    "cve_cargo",
                    "cargo"

            "correcto":false,
            "mensaje"
            */

            if ($responseApi['correcto']) {
                //json de datos
                $data = $responseApi['datos'][0];
                //Crear registro en nuestra base de datos(pendiente)

                $user = User::updateOrCreate(
                    ['user_rpe' => $data['rpe']],
                    [
                        'user_mail' => $data['correo'],
                        'user_role' => $data['cargo'],
                    ]
                );
                $token = $user->createToken('auth_token')->plainTextToken;

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
}