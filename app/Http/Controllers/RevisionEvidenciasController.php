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
    //Es para regresarla a pendiente si es por defaul o como opcion para un boton de pendiente
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

        if ($request->reviser_rpe == NULL) {
            $user = auth()->user();
            $reviser_rpe = $user->user_rpe;
        } else {
            $reviser_rpe = $request->reviser_rpe;
        }

        //solo aprovadp o rechazado puede tener retroalimentacIon
        $feedback = in_array($estado, ['APROBADA', 'NO APROBADA',]) ? $request->feedback : "";

        if ($estado === 'PENDIENTE') {
            //Carga el estado a la base
            $status = Status::create(
                [
                    'evidence_id' => $request->evidence_id,
                    'user_rpe' => $reviser_rpe,
                    'status_description' => $estado,
                    'status_date' => Carbon::now(),
                    'feedback' => $feedback
                ]
            );
        } else {
            $status = Status::where('user_rpe', $reviser_rpe)
                ->where('evidence_id', $request->evidence_id)
                ->where('status_description', 'PENDIENTE');

            $status->update([
                'status_description' => $estado,
                'feedback' => $feedback,
                'status_date' => now()
            ]);

            if ($estado === 'APROBADA') {
                $evidence = Evidence::where('evidence_id', $request->evidence_id)->first();
                $user = User::where('user_rpe', $reviser_rpe)->first();

                $nextRevisors = (new EvidenceController)->nextRevisor($user, $evidence);

                if ($nextRevisors && count($nextRevisors) > 0) {
                    foreach ($nextRevisors as $nextRpe) {
                        Status::create([
                            'evidence_id' => $evidence->evidence_id,
                            'user_rpe' => $nextRpe,
                            'status_description' => 'PENDIENTE',
                            'feedback' => '',
                            'status_date' => now()
                        ]);
                    }
                }

            }

        }

        //crea la notificacion y carga el comentario..
        Notification::create([
            'title' => "Evidencia {$estado}",
            'evidence_id' => $request->evidence_id,
            'notification_date' => Carbon::now(),
            'user_rpe' => $request->user_rpe,
            'reviser_id' => $reviser_rpe,
            'description' => $feedback ? "Tu evidencia ha sido marcada como {$estado} con el siguiente comentario: {$feedback}" : "Tu evidencia ha sido marcada como {$estado}",
            'seen' => false,
            'pinned' => false
        ]);

        return response()->json([
            'message' => "Evidencia marcada como {$estado}",
            'status' => $status
        ], 200);
    }
}
