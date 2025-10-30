<?php
namespace App\Http\Controllers;

use App\Models\Accreditation_Process;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Jobs\GenerateAcreditacionZip;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
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

    // # "Elimina" un proceso, haciendo que se oculte
    public function delete($processId)
    {
        $process = Accreditation_Process::findOrFail($processId);
        $process->deleted = true;
        $process->save();

        return response()->json(['message' => 'Proceso eliminado'], status: 201);
    }

    // # Modifica los datos un proceso
    public function modify(Request $request, $processId)
    {
        $data = $request->validate([
            'process_name' => 'required|string|max:150',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'due_date' => 'required|date|after:start_date|before_or_equal:end_date'
        ]);

        $process = Accreditation_Process::findOrFail($processId);
        if (!$process) {
            return response()->json(['message' => 'Proceso no encontrado', 404]);
        }

        // Cambia los datos del proceso con los que vienen en la solicitud
        $process->update($data);

        return response()->json(['message' => 'Proceso modificado'], status: 200);
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
            SELECT DISTINCT ap.process_id, ap.process_name, ap.start_date, ap.end_date, ap.due_date, c.career_name, a.area_name, fr.frame_name, ap.frame_id, ap.finished, ap.deleted
            FROM users u
            LEFT JOIN evidences e ON u.user_rpe = e.user_rpe
            LEFT JOIN accreditation_processes ap ON e.process_id = ap.process_id
            LEFT JOIN careers c ON ap.career_id = c.career_id
            LEFT JOIN areas a ON c.area_id = a.area_id
            LEFT JOIN frames_of_reference fr ON ap.frame_id = fr.frame_id
            WHERE u.user_rpe = ? AND ap.process_id IS NOT NULL
            UNION
            SELECT DISTINCT ap.process_id, ap.process_name, ap.start_date, ap.end_date, ap.due_date, c.career_name, a.area_name, fr.frame_name, ap.frame_id, ap.finished, ap.deleted
            FROM users u
            JOIN careers c ON u.user_rpe = c.user_rpe
            JOIN accreditation_processes ap ON c.career_id = ap.career_id
            JOIN areas a ON c.area_id = a.area_id
            LEFT JOIN frames_of_reference fr ON ap.frame_id = fr.frame_id
            WHERE u.user_rpe = ?
            UNION
            SELECT DISTINCT ap.process_id, ap.process_name, ap.start_date, ap.end_date, ap.due_date, c.career_name, a.area_name, fr.frame_name, ap.frame_id, ap.finished, ap.deleted
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
            return response()->download($zipPath)->deleteFileAfterSend();
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
                ap.finished,
                ap.deleted
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
                ap.deleted,
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
                ap.finished,
                ap.deleted
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

    public function getCVsProcess($processId)
    {

        $process = Accreditation_Process::with('career')->find($processId);


        if (!$process) {
            return response()->json(['message' => 'No se encontro proceso'], 404);
        }

        $semester = $process->getSemester();
        $area = $process->career->area->area_id;

        //usuarios que dan clase en el area de la carrera (Falta un filtro con plan educativo para que solo sean materias de la carrera)
        $users_area = self::getUsersOfAreaBySemester($semester, $area);
        //La api tiene un error al mandar el area 0 (DFM), detecta que los campos no estan completos.
        $users_dfm = self::getUsersOfAreaBySemester($semester, 0);
        //Area humanistica = 1
        $users_humanistic = self::getUsersOfAreaBySemester($semester, 1);
        //La api no tiene datos de los profesores del DUI
        //$users_english = self::getUsersOfAreaBySemester($semester, '7');


        return response()->json(['rpes_area' => $users_area, 'rpes_dfm' => $users_dfm, 'rpes_humanistic' => $users_humanistic]);

    }

    public static function getUsersOfAreaBySemester($semester, $area)
    {
        //Obtener los grupos de clase de un area en un semestre.
        $area_groups = GroupController::getGroupsByArea($semester, $area);
        $area_groups_data = json_decode($area_groups->getContent(), true);

        if (isset($area_groups_data['data']['datos'])) {

            //Eliminar claves repetidas para obtener el listado de profesores que están dando clase.
            $teachers_area = array_unique(array_column($area_groups_data['data']['datos'], 'nombre', 'rpe'));
            //Castear rpes a string para la consulta
            $rpes_area = array_map('strval', array_keys($teachers_area));

            $users = User::whereIn('user_rpe', $rpes_area)
                ->get(['user_rpe', 'user_name'])
                ->keyBy('user_rpe');

            $users_area = collect($rpes_area)->map(function ($rpe) use ($users, $teachers_area) {
                if (isset($users[$rpe])) {
                    return [
                        'user_rpe' => $users[$rpe]->user_rpe,
                        'user_name' => $users[$rpe]->user_name,
                    ];
                }

                // si no existe en la BD
                return [
                    'user_rpe' => null, //devolver rpe null para indicar en el front que no tenemos información del profesor.
                    'user_name' => $teachers_area[$rpe],
                ];
            })->toArray();

        } else {
            $rpes_area = [];
            $users_area = [];
        }

        return $users_area;
    }

    public function findProcessById($processId)
    {
        $process = DB::select("
            SELECT 
                ap.process_id,
                ap.process_name,
                ap.start_date,
                ap.end_date,
                ap.due_date,
                ap.finished,
                ap.deleted,
                
                c.career_name,
                c.career_id,
                u_career.user_name AS career_owner,
                u_career.user_rpe AS career_owner_rpe,
                
                a.area_name,
                a.area_id,
                u_area.user_name AS area_owner,
                u_area.user_rpe AS area_owner_rpe,
                
                fr.frame_name,
                ap.frame_id
                
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
            return response()->json([
                'message' => 'Proceso no encontrado.',
                'process_id' => $processId,
                'success' => false
            ], 404);  
        }

        return response()->json([
            'data' => $process[0],
            'success' => true,
            'message' => 'Proceso encontrado exitosamente.'
        ]);
    }
}
