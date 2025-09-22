<?php

namespace App\Http\Controllers;

use App\Models\Evidence;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('area');

        /*if ($request->has('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('user_mail', 'LIKE', "%$search%")
                ->orWhere('user_rpe', 'LIKE', "%$search%")
                ->orWhereHas('area', function ($q2) use ($search) {
                    $q2->where('nombre', 'LIKE', "%$search%"); // Ajusta el campo de área
                });
            });
        }*/

        return response()->json([
            'usuarios' => $query->get(),
            'roles' => ['DIRECTIVO', 'JEFE DE AREA', 'COORDINADOR DE CARRERA', 'PROFESOR RESPONSABLE', 'PROFESOR', 'DEPARTAMENTO UNIVERSITARIO', 'PERSONAL DE APOYO', 'ADMINISTRADOR']
        ]);
    }

    public function actualizarRol(Request $request)
    {
        Log::debug("Datos recibidos en la solicitud:", $request->all());
        try {
            $validado = $request->validate([
                'user_id' => 'required|exists:users,user_rpe',
                'rol' => 'required|string|in:DIRECTIVO,JEFE DE AREA,COORDINADOR DE CARRERA,PROFESOR,DEPARTAMENTO UNIVERSITARIO,PERSONAL DE APOYO,ADMINISTRADOR',
            ]);

            Log::debug("Datos validados correctamente:", $validado);

            $usuario = User::findOrFail($request->user_id);
            $usuario->user_role = $request->rol;
            $usuario->save();

            Log::debug("Rol actualizado para usuario ID {$usuario->user_rpe} a {$usuario->user_role}");

            return response()->json(['success' => true, 'message' => 'Rol actualizado correctamente']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Error de validación:", $e->errors());
            return response()->json(['error' => 'Datos inválidos', 'detalles' => $e->errors()], 422);
        }
    }


    public function myAssignments()
    {
        $user = auth()->user();

        $assignments = Evidence::with([
            'standard:standard_id,standard_name', // Traemos el criterio (nombre del standard)
            'status' => function ($query) {
                $query->orderByDesc('status_date'); // Solo el último estado
            }
        ])
            ->where('user_rpe', $user->user_rpe)
            ->select('evidence_id', 'standard_id') // Solo traemos lo necesario
            ->get()
            ->map(function ($evidence) {
                return [
                    'evidence_id' => $evidence->evidence_id,
                    'criterio' => $evidence->standard?->standard_name,
                    'estado' => $evidence->status->first()?->status_description ?? 'MALO', // El nombre del estado más reciente
                ];
            });

        return response()->json($assignments);
    }

    public function validateUser(Request $request)
    {
        $validated = $request->validate([
            'rpe' => 'required|string'
        ]);

        $endpoint = 'https://servicios.ing.uaslp.mx/ws_cacei/ValidaUsuario.php';
        $payload = [
            'key' => 'B3E06D96-1562-4713-BCD7-7F762A87F205',
            'rpe' => $validated['rpe'],
            'contra' => 'Cacei#FI@2025'
        ];

        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->post($endpoint, [
                'json' => $payload,
                'verify' => false // Solo para desarrollo, en producción deberías tener certificados válidos
            ]);

            return response()->json(json_decode($response->getBody(), true));
        } catch (\Exception $e) {
            return response()->json([
                'correcto' => false,
                'mensaje' => 'Error al validar el usuario: ' . $e->getMessage()
            ], 500);
        }
    }

}
