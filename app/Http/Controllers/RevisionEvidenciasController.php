<?php

namespace App\Http\Controllers;
use App\Models\Status;
use App\Models\Notification;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RevisionEvidenciasController extends Controller
{
    public function aprobarEvidencia(Request $request)
    {

        return $this->actualizarEstado($request, 'Aprobado');
        
    }

    public function desaprobarEvidencia(Request $request)
    {
        return $this->actualizarEstado($request, 'Desaprobado');
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
        $reviser_rpe = $user->user_rpe;
 
        //solo aprovadp o rechazado puede tener retroalimentacIon
        $feedback = in_array($estado, ['Aprobada', 'No aprobada']) ? $request->feedback : null;
 
        //Carga el estado a la base
        $status = Status::updateOrCreate(
            [
                'evidence_id' => $request->evidence_id,
                'user_rpe' => $reviser_rpe,
            ],
            [
                'status_description' => $estado,
                'status_date' => Carbon::now(),
                'feedback' => $feedback
            ]
        );
 
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
