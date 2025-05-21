<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class GroupController extends Controller
{
    public static function getGroupsByArea($semester, $area)
    {
        // Puedes usar valores por defecto o recibirlos desde el request
        $request = Request::create('https://servicios.ing.uaslp.mx/ws_cacei/Horario.php', 'POST');
        $endpoint = $request->input('endpoint', 'https://servicios.ing.uaslp.mx/ws_cacei/Horario.php');
        $payload = $request->input('payload', [
            "key" => "B3E06D96-1562-4713-BCD7-7F762A87F205",
            "cve_area" => $area,
            "semestre" => $semester
        ]);

        try {
            $response = Http::withoutVerifying() // Desactiva verificación SSL (como en cURL)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($endpoint, $payload);

            // Devuelve la respuesta como JSON
            return response()->json([
                'status' => 'success',
                'data' => $response->json(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al consultar el servicio: ' . $e->getMessage(),
            ], 500);
        }
    }
}
