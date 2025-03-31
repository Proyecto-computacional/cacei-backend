<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class ProcessController extends Controller
{
    public function linkedProcesses()
    {
        return response()->json([
            ['frame_id' => 2025, 'process_id' => 1, 'career_id' => 1],
            ['frame_id' => 2025, 'process_id' => 2, 'career_id' => 3]
        ]);
    }

    public function checkUser(Request $request)
    {
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

        $responseApi = $responseApi->json();

        $data = $responseApi['datos'][0];

        $user = $data['rpe'];

        $linkedProcesses = [
            ['frame_id' => 2025, 'process_id' => 1, 'career_id' => 1],
            ['frame_id' => 2025, 'process_id' => 2, 'career_id' => 3]
        ];

        return response()->json([
            'message' => 'Checando procesos vinculados...',
            'user_data' => $user,
            'linked_processes' => $linkedProcesses
        ]);
    }
}
