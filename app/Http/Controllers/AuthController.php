<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Str;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validar los datos de entrada
       $request->validate([
            'rpe' => 'required|rpe',
            'password' => 'required|password', 
        ]);

        $url = "https://servicios.ing.uaslp.mx/ws_cacei/ValidaUsuario.php";
        $data = [
        'rpe' => $request->rpe,
        'password' => $request->password
        ];

        $response = Http::withHeaders([
        'Authorization' => 'Bearer B3E06D96-1562-4713-BCD7-7F762A87F205',
        'Content-Type' => 'application/x-www-form-urlencoded',
        ])->post($url, $data);

        if ($response->successful()) {

            $data = $response->json();

            $user = new User();
            $user->fill([
                'rpe' => $data['rpe'],
                'email' => $data['email'],
                'name' => $data['nombre'],
                'role' => $data['nombre'],
            ]);
            // Puedes devolverlo como JSON o usarlo como necesites
                return response()->json([
                'user' => $user,
                'message' => 'Login exitoso'
                ]);
            } else {
                return response()->json([
                    'message' => 'Error en el login',
                    'status' => $response->status()
                ], $response->status());
             }
        }
    }