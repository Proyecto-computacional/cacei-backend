<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccreditationProcessController extends Controller
{
    /* obtener los procesos de acreditación asociados a un usuario */
    public function getProcessesByUser(Request $request)
    {
        $userRpe = $request->query('userRpe');  // se obtiene el parámetro 'userRpe' desde la URL

        if (!$userRpe) {
            return response()->json(['message' => 'El parámetro userRpe es requerido.'], 400);
        }
        error_log("Si llego aquí");
        // consultar a la base de datos
        $processes = DB::select(" 
            SELECT DISTINCT ap.process_id, ap.start_date, ap.end_date, ap.due_date, c.career_name, a.area_name, fr.frame_name
            FROM users u
            JOIN evidences e ON u.user_rpe = e.user_rpe
            JOIN accreditation_processes ap ON e.process_id = ap.process_id
            JOIN careers c ON ap.career_id = c.career_id
            JOIN areas a ON c.area_id = a.area_id
            LEFT JOIN frames_of_reference fr ON ap.frame_id = fr.frame_id
            WHERE u.user_rpe = ?
        ", [$userRpe]);

        // verificar si hay procesos
        if (empty($processes)) {
            return response()->json(['message' => 'No se encontraron procesos para este usuario.'], 404);
        }

        // retornar la respuesta JSON con los procesos encontrados
        return response()->json($processes);
    }
}
