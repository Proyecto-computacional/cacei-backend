<?php

namespace App\Http\Controllers;
use App\Models\Evidence;
use App\Models\Status;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RevisionEvidenciasController extends Controller
{
    public function aprobarEvidencia(Request $request)
    {
        return $this->actualizarEstado($request, 'APROBADA');
    }

    public function desaprobarEvidencia(Request $request)
    {
        return $this->actualizarEstado($request, 'NO APROBADA');
    }
    //Es para regresarla a pendiente si es por default o como opcion para un boton de pendiente
    public function marcarPendiente(Request $request)
    {
        return $this->actualizarEstado($request, 'PENDIENTE');

    }

    private function actualizarEstado(Request $request, $estado)
    {
        $request->validate([
            'evidence_id' => 'required|integer',
            'user_rpe' => 'required|string',
            'feedback' => 'nullable|string|max:1048', //puede ser null
            'reviser_rpe' => 'nullable|string'
        ]);

        //$reviser_rpe;

        if ($request->reviser_rpe == NULL) {
            $user = auth()->user();
            $reviser_rpe = $user->user_rpe;
        } else {
            $reviser_rpe = $request->reviser_rpe;
        }

        //solo aprovadp o rechazado puede tener retroalimentacIon
        $feedback = in_array($estado, ['APROBADA', 'NO APROBADA',]) ? $request->feedback : "";

        if ($estado === 'PENDIENTE') {
            // Buscar si ya existe un estado para este usuario y evidencia
            $existingStatus = Status::where('user_rpe', $reviser_rpe)
                ->where('evidence_id', $request->evidence_id)
                ->first();

            if ($existingStatus) {
                // Si existe, actualizar el estado existente
                $existingStatus->update([
                    'status_description' => $estado,
                    'feedback' => $feedback,
                    'status_date' => Carbon::now('America/Mexico_City')
                ]);
            } else {
                // Si no existe, crear uno nuevo
                Status::create([
                    'evidence_id' => $request->evidence_id,
                    'user_rpe' => $reviser_rpe,
                    'status_description' => $estado,
                    'status_date' => Carbon::now('America/Mexico_City'),
                    'feedback' => $feedback
                ]);
            }
        } else {
            $status = Status::where('user_rpe', $reviser_rpe)
                ->where('evidence_id', $request->evidence_id)
                ->where('status_description', 'PENDIENTE');

            if ($status) {
                $status->update([
                    'status_description' => $estado,
                    'feedback' => $feedback,
                    'status_date' => Carbon::now('America/Mexico_City')
                ]);
            } else {
                Status::create(
                    [
                        'evidence_id' => $request->evidence_id,
                        'user_rpe' => $reviser_rpe,
                        'status_description' => 'APROBADA',
                        'status_date' => Carbon::now('America/Mexico_City'),
                        'feedback' => 'Aprobado por administrador'
                    ]
                );
            }

            if ($estado === 'APROBADA') {
                $evidence = Evidence::where('evidence_id', $request->evidence_id)->first();
                $user = User::where('user_rpe', $reviser_rpe)->first();

                if ($user->user_role === "ADMINISTRADOR") {
                    // Primero, aprobar el propio status del administrador
                    Status::updateOrCreate(
                        [
                            'evidence_id' => $evidence->evidence_id,
                            'user_rpe' => $user->user_rpe
                        ],
                        [
                            'status_description' => 'APROBADA',
                            'status_date' => Carbon::now('America/Mexico_City'),
                            'feedback' => $feedback
                        ]
                    );

                    // Iniciar desde el responsable de la evidencia
                    $currentUser = User::where('user_rpe', $evidence->user_rpe)->first();

                    // Ir subiendo por la jerarquía hasta llegar al administrador
                    while ($currentUser && $currentUser->user_role !== 'ADMINISTRADOR') {
                        $nextRpes = (new EvidenceController)->nextRevisor($currentUser, $evidence);
                        if (!$nextRpes || count($nextRpes) === 0) {
                            break; // Por seguridad, detener si no hay siguiente
                        }

                        foreach ($nextRpes as $nextRpe) {
                            // Verificar si ya existe un status para este usuario
                            $existingStatus = Status::where('evidence_id', $evidence->evidence_id)
                                ->where('user_rpe', $nextRpe)
                                ->first();

                            // Solo crear un nuevo status si no existe uno previo
                            if (!$existingStatus) {
                                Status::create([
                                    'evidence_id' => $evidence->evidence_id,
                                    'user_rpe' => $nextRpe,
                                    'status_description' => 'APROBADA',
                                    'status_date' => Carbon::now('America/Mexico_City'),
                                    'feedback' => 'Aprobado por administrador'
                                ]);
                            }

                            $nextUser = User::where('user_rpe', $nextRpe)->first();
                            if ($nextUser->user_role === 'ADMINISTRADOR' || $nextUser == $currentUser) {
                                break 2; // Salir del ciclo while y foreach
                            }
                            $currentUser = $nextUser;
                        }
                    }
                } else {
                    $nextRevisors = (new EvidenceController)->nextRevisor($user, $evidence);

                    if ($nextRevisors && count($nextRevisors) > 0) {
                        foreach ($nextRevisors as $nextRpe) {
                            Status::create([
                                'evidence_id' => $evidence->evidence_id,
                                'user_rpe' => $nextRpe,
                                'status_description' => 'PENDIENTE',
                                'feedback' => '',
                                'status_date' => Carbon::now('America/Mexico_City')
                            ]);
                        }
                    }
                }
            } else if ($estado === 'NO APROBADA') {
                $evidence = Evidence::where('evidence_id', $request->evidence_id)->first();
                $user = User::where('user_rpe', $reviser_rpe)->first();
                Status::updateOrCreate(
                    [
                        'evidence_id' => $evidence->evidence_id,
                        'user_rpe' => $user->user_rpe
                    ],
                    [
                        'status_description' => 'NO APROBADA',
                        'status_date' => Carbon::now('America/Mexico_City'),
                        'feedback' => $feedback
                    ]
                );
            }
        }

        // Solo crear notificación si el estado no es PENDIENTE
        if ($estado !== 'PENDIENTE') {
            // Generar un ID único
            do {
                $randomId = rand(1, 100);
            } while (Notification::where('notification_id', $randomId)->exists()); // Verifica que no se repita

            Log::info('reviser_rpe: ' . $reviser_rpe);
            //crea la notificacion y carga el comentario..
            Notification::create([
                'notification_id' => $randomId,
                'title' => "Evidencia {$estado}",
                'evidence_id' => $request->evidence_id,
                'notification_date' => Carbon::now('America/Mexico_City'),
                'user_rpe' => $request->user_rpe,
                'reviser_id' => $reviser_rpe,
                'description' => $feedback ? "Tu evidencia ha sido marcada como {$estado} con el siguiente comentario: {$feedback}" : "Tu evidencia ha sido marcada como {$estado}",
                'seen' => false,
                'pinned' => false
            ]);
        }

        return response()->json([
            'message' => "Evidencia marcada como {$estado}",
            //'status' => $status->load('user')
        ], 200);
    }
}
