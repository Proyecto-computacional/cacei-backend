<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EvidenciaEstadisticaController extends Controller
{
    
    public function estadisticasPorRPE($rpe){
        $estadisticas = DB::select("
        SELECT
            c.career_name,
            rf.frame_name,
            COALESCE(SUM(CASE WHEN st.status_description = 'Aprobado' THEN 1 ELSE 0 END), 0) AS aprobadas,
            COALESCE(SUM(CASE WHEN st.status_description = 'Desaprobado' THEN 1 ELSE 0 END), 0) AS desaprobadas,
            (
                SELECT COUNT(*)
                FROM standards s
                WHERE s.standard_id NOT IN (
                    SELECT DISTINCT e.standard_id
                    FROM evidences e
                    JOIN revisers r ON r.evidence_id = e.evidence_id
                    WHERE r.user_rpe = ?
                )
            ) AS sin_evidencia
        FROM revisers r
        JOIN evidences e ON r.evidence_id = e.evidence_id
        JOIN standards s ON s.standard_id = e.standard_id
        JOIN accreditation_processes ap ON ap.process_id = e.process_id
        JOIN frames_of_reference rf ON rf.frame_id = ap.frame_id
        JOIN careers c ON c.career_id = ap.career_id
        LEFT JOIN (
            SELECT DISTINCT ON (evidence_id) *
            FROM statuses
            ORDER BY evidence_id, status_date DESC
        ) st ON st.evidence_id = e.evidence_id
        WHERE r.user_rpe = ?
        GROUP BY c.career_name, rf.frame_name
        ORDER BY c.career_name, rf.frame_name
    ", [$rpe, $rpe]);

$resultado = [];
foreach ($estadisticas as $e) {
    $total = $e->aprobadas + $e->desaprobadas + $e->sin_evidencia;
    $total = $total > 0 ? $total : 1; // evitar división entre 0

    $resultado[] = [
        'career_name' => $e->career_name,
        'frame_name' => $e->frame_name,
        'aprobado' => round(($e->aprobadas / $total) * 100, 2),
        'desaprobado' => round(($e->desaprobadas / $total) * 100, 2),
        'sin_evidencia' => round(($e->sin_evidencia / $total) * 100, 2),
    ];
}

return response()->json($resultado);
}


    public function resumenGeneralPorRPE($rpe)
    {
        $datos = DB::select("
            SELECT
                COALESCE(SUM(CASE WHEN st.status_description = 'Aprobado' THEN 1 ELSE 0 END), 0) AS aprobadas,
                COALESCE(SUM(CASE WHEN st.status_description = 'Desaprobado' THEN 1 ELSE 0 END), 0) AS desaprobadas,
                (
                    SELECT COUNT(*)
                    FROM standards s
                    WHERE s.standard_id NOT IN (
                        SELECT DISTINCT e.standard_id
                        FROM evidences e
                        JOIN revisers r ON r.evidence_id = e.evidence_id
                        WHERE r.user_rpe = ?
                    )
                ) AS sin_evidencia
            FROM evidences e
            JOIN revisers r ON r.evidence_id = e.evidence_id
            LEFT JOIN (
                SELECT DISTINCT ON (evidence_id) *
                FROM statuses
                ORDER BY evidence_id, status_date DESC
            ) st ON st.evidence_id = e.evidence_id
            WHERE r.user_rpe = ?
        ", [$rpe, $rpe]);
    
        $d = $datos[0];
    
        $total = $d->aprobadas + $d->desaprobadas + $d->sin_evidencia;
        $total = $total > 0 ? $total : 1; // evitar división entre 0
    
        return response()->json([
            'aprobado' => round(($d->aprobadas / $total) * 100, 2),
            'desaprobado' => round(($d->desaprobadas / $total) * 100, 2),
            'sin_evidencia' => round(($d->sin_evidencia / $total) * 100, 2),
        ]);
    }
    

    public function notificacionesNoVistas($rpe)
    {
        $count = DB::table('notifications')
            ->where('user_rpe', $rpe)
            ->where('seen', false)
            ->count();

        return response()->json(['no_vistas' => $count]);
    }

 
    public function ultimaActualizacionCV($rpe)
    {
        $cv = DB::table('users')
    ->join('cvs', 'users.cv_id', '=', 'cvs.cv_id')
    ->where('users.user_rpe', $rpe)
    ->orderBy('cvs.update_date', 'desc') // ← usa `update_date`, no `updated_at`
    ->select('cvs.update_date')
    ->first();


        if ($cv) {
            return response()->json([
                'rpe' => $rpe,
                'ultima_actualizacion_cv' => $cv->update_date,
            ]);
        } else {
            return response()->json([
                'rpe' => $rpe,
                'mensaje' => 'CV no encontrado para este usuario',
            ], 404);
        }
    }
}
