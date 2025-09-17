<?php
namespace App\Http\Controllers;

use App\Models\Accreditation_Process;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Jobs\GenerateAcreditacionZip;
use Illuminate\Support\Facades\Log;
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
            'due_date' => $request->due_date,
            'finished' => false
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
            SELECT DISTINCT ap.process_id, ap.process_name, ap.start_date, ap.end_date, ap.due_date, c.career_name, a.area_name, fr.frame_name, ap.frame_id, ap.finished
            FROM users u
            LEFT JOIN evidences e ON u.user_rpe = e.user_rpe
            LEFT JOIN accreditation_processes ap ON e.process_id = ap.process_id
            LEFT JOIN careers c ON ap.career_id = c.career_id
            LEFT JOIN areas a ON c.area_id = a.area_id
            LEFT JOIN frames_of_reference fr ON ap.frame_id = fr.frame_id
            WHERE u.user_rpe = ? AND ap.process_id IS NOT NULL
            UNION
            SELECT DISTINCT ap.process_id, ap.process_name, ap.start_date, ap.end_date, ap.due_date, c.career_name, a.area_name, fr.frame_name, ap.frame_id, ap.finished
            FROM users u
            JOIN careers c ON u.user_rpe = c.user_rpe
            JOIN accreditation_processes ap ON c.career_id = ap.career_id
            JOIN areas a ON c.area_id = a.area_id
            LEFT JOIN frames_of_reference fr ON ap.frame_id = fr.frame_id
            WHERE u.user_rpe = ?
            UNION
            SELECT DISTINCT ap.process_id, ap.process_name, ap.start_date, ap.end_date, ap.due_date, c.career_name, a.area_name, fr.frame_name, ap.frame_id, ap.finished
            FROM users u
            JOIN areas a ON u.user_rpe = a.user_rpe
            JOIN careers c ON a.area_id = c.area_id
            JOIN accreditation_processes ap ON c.career_id = ap.career_id
            LEFT JOIN frames_of_reference fr ON ap.frame_id = fr.frame_id
            WHERE u.user_rpe = ?
        ", [$userRpe, $userRpe, $userRpe]);

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
        Log::debug($zipPath);

        // Si el archivo existe, lo devolvemos para descarga
        if (file_exists($zipPath)) {
            return response()->download($zipPath);
        }

        return response()->json(['error' => 'No se encontraron archivos para este proceso.'], 404);
    }

    public function getProcessById($processId)
    {
        $process = DB::select("
            SELECT 
                ap.process_id,
                ap.process_name,
                ap.start_date,
                ap.end_date,
                ap.due_date,

                c.career_name,
                u_career.user_name   AS career_owner,

                a.area_name,
                u_area.user_name     AS area_owner,

                fr.frame_name,
                ap.frame_id,
                ap.finished
            FROM accreditation_processes ap

            JOIN careers c
            ON ap.career_id = c.career_id
            JOIN users u_career
            ON c.user_rpe = u_career.user_rpe

            JOIN areas a
            ON c.area_id = a.area_id
            JOIN users u_area
            ON a.user_rpe = u_area.user_rpe

            LEFT JOIN frames_of_reference fr
            ON ap.frame_id = fr.frame_id

            WHERE ap.process_id = ?
        ", [$processId]);

        if (empty($process)) {
            return response()->json(['message' => 'Proceso no encontrado.'], 404);
        }

        return response()->json($process[0]);
    }

    public function getAllProcesses()
    {
        $processes = DB::select("
            SELECT 
                ap.process_id,
                ap.process_name,
                ap.start_date,
                ap.end_date,
                ap.due_date,
                c.career_name,
                a.area_name,
                fr.frame_name,
                fr.frame_id,
                ap.finished,
                CONCAT('/api/process/', ap.process_id) as process_path
            FROM accreditation_processes ap
            JOIN careers c ON ap.career_id = c.career_id
            JOIN areas a ON c.area_id = a.area_id
            LEFT JOIN frames_of_reference fr ON ap.frame_id = fr.frame_id
            ORDER BY ap.start_date DESC
        ");

        if (empty($processes)) {
            return response()->json(['message' => 'No se encontraron procesos de acreditación.'], 200);
        }

        return response()->json($processes);
    }

    public function getProcessesByFrameId(Request $request)
    {
        $request->validate([
            'frame_id' => 'required|int'
        ]);

        $processes = DB::select("
            SELECT 
                ap.process_id,
                ap.process_name,
                ap.start_date,
                ap.end_date,
                ap.due_date,
                c.career_name,
                a.area_name,
                fr.frame_name,
                fr.frame_id,
                ap.finished
            FROM accreditation_processes ap
            JOIN careers c ON ap.career_id = c.career_id
            JOIN areas a ON c.area_id = a.area_id
            LEFT JOIN frames_of_reference fr ON ap.frame_id = fr.frame_id
            WHERE ap.frame_id = ?
            ORDER BY ap.start_date DESC
        ", [$request->frame_id]);

        return response()->json($processes);
    }

    public function toggleFinished(Request $request)
    {
        $process = Accreditation_Process::findOrFail($request->process_id);  // Busca la notificación por su ID o lanza error si no se encuentra
        $process->finished = !$process->finished;  // Cambia el valor del campo 'favorite' (true/false)
        $process->save();  // Guarda el cambio en la base de datos

        return response()->json(['message' => 'Estado de proceso actualizado']);  // Devuelve una respuesta JSON indicando que se actualizó el estado de favorito
    }
}
