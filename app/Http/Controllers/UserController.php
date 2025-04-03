<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
class UserController extends Controller
{
    public function index(Request $request)
    {
        error_log('es esta función');
        $query = User::query();
        if ($request->has('search')) {
            $search = $request->input('search');
    
            $query->where(function ($q) use ($search) {
                $q->where('user_mail', 'LIKE', "%$search%")
                  ->orWhere('user_rpe', 'LIKE' , "%$search%");
            });
        }
        return response()->json([
            'usuarios' => $query->cursorPaginate(10), // Pagina los resultados si hay muchos
            'roles' => ['DIRECTIVO', 'JEFE DE AREA', 'COORDINADOR DE CARRERA', 'PROFESOR RESPONSABLE', 'PROFESOR', 'TUTOR ACADEMICO', 'DEPARTAMENTO UNIVERSITARIO', 'PERSONAL DE APOYO']
        ]);
    }

    public function actualizarRol(Request $request)
    {
        Log::debug("Datos recibidos en la solicitud:", $request->all());
        try {
            $validado = $request->validate([
                'user_id' => 'required|exists:users,user_rpe',
                'rol' => 'required|string|in:DIRECTIVO,JEFE DE AREA,COORDINADOR DE CARRERA,PROFESOR RESPONSABLE,PROFESOR,TUTOR ACADEMICO,DEPARTAMENTO UNIVERSITARIO,PERSONAL DE APOYO,ADMINISTRADOR',
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
}
