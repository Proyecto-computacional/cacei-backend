<?php

namespace App\Http\Controllers;
use App\Models\Reviser;
use App\Models\Status;
use App\Models\Notification;
use App\Models\User;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Jobs\BackupJob;

class RevisionEvidenciasController extends Controller
{
    public function aprobarEvidencia(Request $request)
    {
        $user = auth()->user();
        $evidenceId = $request->input('evidence_id');
        $feedback = $request->input('feedback');
        $reviser = Reviser::where('user_rpe', $request->user_rpe)->first();
        // Obtener RPEs de todos los roles
        $rolesRpe = [
            'ADMINISTRADOR' => User::where('user_role', 'ADMINISTRADOR')->first()->user_rpe,
            'JEFE DE AREA' => User::where('user_role', 'JEFE DE AREA')->first()->user_rpe,
            'COORDINADOR' => User::where('user_role', 'COORDINADOR')->first()->user_rpe,
            'PROFESOR' => User::where('user_role', 'PROFESOR')->whereHas('revisers', fn($q) => $q->where('evidence_id', $evidenceId))->first()?->user_rpe
        ];

        DB::transaction(function () use ($evidenceId, $user, $feedback, $rolesRpe) {
            // Si es administrador, actualiza TODOS los statuses
            if ($user->user_role === 'ADMINISTRADOR') {
                foreach ($rolesRpe as $rpe) {
                    Status::updateOrCreate(
                        [
                            'evidence_id' => $evidenceId,
                            'user_rpe' => $rpe
                        ],
                        [
                            'status_description' => 'Aprobado',
                            'status_date' => now(),
                            'feedback' => $feedback
                        ]
                    );
                }
            } else {
                // Para otros roles, solo actualizan su propio status
                Status::updateOrCreate(
                    [
                        'evidence_id' => $evidenceId,
                        'user_rpe' => $user->user_rpe
                    ],
                    [
                        'status_description' => 'Aprobado',
                        'status_date' => now(),
                        'feedback' => $feedback
                    ]
                );
            }
        });

         // Generar un ID único
         do {
            $randomId = rand(1, 100);
        } while (Notification::where('notification_id', $randomId)->exists()); // Verifica que no se repita

        //crea la notificacion y carga el comentario..
        Notification::create([
            'notification_id' => $randomId,
            'title' => "Evidencia Aprobada",
            'evidence_id' => $request->evidence_id,
            'notification_date' => Carbon::now(),
            'user_rpe' => $request->user_rpe,
            'reviser_id' => $reviser->reviser_id,
            'description' => $feedback ? "Tu evidencia ha sido marcada como Aprobada con el siguiente comentario: {$feedback}" : "Tu evidencia ha sido marcada como Aprobada",
            'seen' => false,
            'pinned' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => $user->user_role === 'ADMINISTRADOR' 
                ? 'Evidencia aprobada completamente (todos los roles)' 
                : 'Evidencia aprobada para tu rol'
        ]);
    }

    public function desaprobarEvidencia(Request $request)
    {
        $request->validate([
            'evidence_id' => 'required|integer|exists:evidences,evidence_id',
            'feedback' => 'required|string|max:255'
        ]);
        $reviser = Reviser::where('user_rpe', $request->user_rpe)->first();
        return DB::transaction(function () use ($request) {
            $user = auth()->user();
            $evidenceId = $request->evidence_id;

            // 1. Registrar el estado de desaprobación
            $status = Status::updateOrCreate(
                [
                    'evidence_id' => $evidenceId,
                    'user_rpe' => $user->user_rpe
                ],
                [
                    'status_description' => 'Desaprobado',
                    'status_date' => now(),
                    'feedback' => $request->feedback
                ]
            );

            // 2. Eliminar archivos relacionados usando el modelo File
            $files = File::where('evidence_id', $evidenceId)->get();
            $deletedFiles = [];

            foreach ($files as $file) {
                try {
                    // Eliminar el archivo físico
                    Storage::disk('public')->delete($file->file_url);
                    
                    // Registrar metadatos antes de eliminar
                    $deletedFiles[] = [
                        'name' => $file->file_name,
                        'url' => $file->file_url
                    ];
                    
                    // Eliminar de la base de datos
                    $file->delete();
                    
                } catch (\Exception $e) {
                    Log::error("Error eliminando archivo {$file->file_id}: " . $e->getMessage());
                    continue;
                }
            }

            // Generar un ID único
            do {
                $randomId = rand(1, 100);
            } while (Notification::where('notification_id', $randomId)->exists()); // Verifica que no se repita
            $reviser = Reviser::where('evidence_id', $evidenceId)->first();
            //crea la notificacion y carga el comentario..
            Notification::create([
                'notification_id' => $randomId,
                'title' => "Evidencia Rechazada",
                'evidence_id' => $request->evidence_id,
                'notification_date' => Carbon::now(),
                'user_rpe' => $request->user_rpe,
                'reviser_id' => $reviser->reviser_id,
                'description' => $request->feedback ? "Tu evidencia ha sido marcada como Desaprobado con el siguiente comentario: {$request->feedback}" : "Tu evidencia ha sido marcada como Desaprobado",
                'seen' => false,
                'pinned' => false
            ]);

            // 3. Disparar backup
            //BackupJob::dispatch();

            return response()->json([
                'success' => true,
                'message' => 'Evidencia desaprobada y archivos eliminados',
                'deleted_files_count' => count($deletedFiles),
                'deleted_files' => $deletedFiles
            ]);
        });
    }
    //Es para regresarla a pendiente si es por defaul o como opcion para un boton de pendiente
    public function marcarPendiente(Request $request)
    {
        return $this->actualizarEstado($request, 'Pendiente');
    }

    private function actualizarEstado(Request $request, $estado)
    {
        $request->validate([
            'evidence_id' => 'required|integer',
            'user_rpe' => 'required|string',
            'feedback' => 'nullable|string|max:255' //puede ser null
        ]);
 
        $user = auth()->user();


        $reviser = Reviser::where('user_rpe', $request->user_rpe)->first();

        if (!$reviser) {
            return response()->json([
                'error' => 'Revisor no encontrado'
            ], 404);
        }
        
        $feedback =$request->feedback;
 
        //Carga el estado a la base
        $status = Status::updateOrCreate(
            [
                'evidence_id' => $request->evidence_id,
                'user_rpe' => $request->user_rpe,
            ],
            [
                'status_description' => $estado,
                'status_date' => Carbon::now(),
                'feedback' => $feedback
            ]
        );
 
        // Generar un ID único
        do {
            $randomId = rand(1, 100);
        } while (Notification::where('notification_id', $randomId)->exists()); // Verifica que no se repita

        //crea la notificacion y carga el comentario..
        Notification::create([
            'notification_id' => $randomId,
            'title' => "Evidencia {$estado}",
            'evidence_id' => $request->evidence_id,
            'notification_date' => Carbon::now(),
            'user_rpe' => $request->user_rpe,
            'reviser_id' => $reviser->reviser_id,
            'description' => $feedback ? "Tu evidencia ha sido marcada como {$estado} con el siguiente comentario: {$feedback}" : "Tu evidencia ha sido marcada como {$estado}",
            'seen' => false,
            'pinned' => false
        ]);
 
        return response()->json([
            'message' => "Evidencia marcada como {$estado}",
            'status' => $status->load('user')
        ], 200);
    }
}
