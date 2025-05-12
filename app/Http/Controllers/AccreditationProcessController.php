<?php
namespace App\Http\Controllers;

use App\Models\Accreditation_Process;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Jobs\GenerateAcreditacionZip;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccreditationProcessController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'career_id' => 'required|string|exists:careers,career_id',
            'frame_id' => 'nullable|integer|exists:frames_of_reference,frame_id',
            'process_name' => 'required|string|max:150',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'due_date' => 'required|date|after:start_date|before_or_equal:end_date'
        ]);

        // Generate a unique process_id
        do {
            $processId = rand(1, 1000);
        } while (Accreditation_Process::where('process_id', $processId)->exists());

        $process = Accreditation_Process::create([
            'process_id' => $processId,
            'career_id' => $request->career_id,
            'frame_id' => $request->frame_id,
            'process_name' => $request->process_name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'due_date' => $request->due_date
        ]);

        return response()->json([
            'message' => 'Proceso de acreditación creado exitosamente',
            'process' => $process
        ], 201);
    }

    /* obtener los procesos de acreditación asociados a un usuario */
    public function getProcessesByUser(Request $request)
    {
        $userRpe = $request->query('userRpe');  // se obtiene el parámetro 'userRpe' desde la URL

        if (!$userRpe) {
            return response()->json(['message' => 'El parámetro userRpe es requerido.'], 400);
        }
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
            return response()->json(['processes' => [], 'message' => 'No se encontraron procesos para este usuario.'], 200);
        }        

        // retornar la respuesta JSON con los procesos encontrados
        return response()->json($processes);
    }

    public function downloadProcess($processId)
    {
        // Ejecutamos el job sin colas, de forma sincrónica
        GenerateAcreditacionZip::dispatchSync($processId);

        // Ruta del ZIP generado por el job
        $zipPath = storage_path("app/zips/proceso_{$processId}.zip");

        // Si el archivo existe, lo devolvemos para descarga
        if (file_exists($zipPath)) {
            error_log('genera el zip');
            return response()->download($zipPath)/*->deleteFileAfterSend(true)*/;
        }

        error_log('no genera el zip');
        return response()->json(['error' => 'No se pudo generar el archivo ZIP.'], 500);
    }

    public function getProcessById($processId)
    {
        $process = DB::select("
            SELECT ap.process_id, ap.process_name, ap.start_date, ap.end_date, ap.due_date, 
                   c.career_name, a.area_name, fr.frame_name
            FROM accreditation_processes ap
            JOIN careers c ON ap.career_id = c.career_id
            JOIN areas a ON c.area_id = a.area_id
            LEFT JOIN frames_of_reference fr ON ap.frame_id = fr.frame_id
            WHERE ap.process_id = ?
        ", [$processId]);

        if (empty($process)) {
            return response()->json(['message' => 'Proceso no encontrado.'], 404);
        }

        return response()->json($process[0]);
    }
}
