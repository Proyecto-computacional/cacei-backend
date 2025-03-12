<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
class UserController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
<<<<<<< HEAD
            'usuarios' => User::paginate(10), // Pagina los resultados si hay muchos
=======
            'usuarios' => User::cursorPaginate(10), // Pagina los resultados si hay muchos
>>>>>>> f25029698d9b900333575d7ff35b77cb95cf6d49
            'roles' => ['DIRECTIVO', 'JEFE DE AREA', 'COORDINADOR DE CARRERA', 'PROFESOR RESPONSABLE', 'PROFESOR', 'TUTOR ACADEMICO', 'DEPARTAMENTO UNIVERSITARIO', 'PERSONAL DE APOYO']
        ]);
    }

    public function actualizarRol(Request $request)
    {
        Log::debug("Datos recibidos en la solicitud:", $request->all());
        try {
            $validado = $request->validate([
                'user_id' => 'required|exists:users,user_rpe',
                'rol' => 'required|string|in:DIRECTIVO,JEFE DE AREA,COORDINADOR,PROFESOR RESPONSABLE,PROFESOR,TUTOR ACADEMICO,DEPARTAMENTO UNIVERSITARIO,PERSONAL DE APOYO',
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
