<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EvidenciaEstadisticaController extends Controller
{
    
    public function estadisticasPorRPE($rpe, $frame_name, $career_name)
    {
        $estadisticas = DB::select("
            SELECT
                c.career_name,
                rf.frame_name,
                COALESCE(SUM(CASE WHEN st.status_description = 'APROBADA' THEN 1 ELSE 0 END), 0) AS aprobadas,
                COALESCE(SUM(CASE WHEN st.status_description = 'NO APROBADA' THEN 1 ELSE 0 END), 0) AS desaprobadas,
                COALESCE(SUM(CASE WHEN st.status_description = 'PENDIENTE' THEN 1 ELSE 0 END), 0) AS pendientes
            FROM (
                SELECT DISTINCT ON (e.evidence_id) st.*
                FROM statuses st
                JOIN evidences e ON e.evidence_id = st.evidence_id
                ORDER BY e.evidence_id, st.status_date DESC
            ) st
            JOIN evidences e ON e.evidence_id = st.evidence_id
            JOIN accreditation_processes ap ON ap.process_id = e.process_id
            JOIN frames_of_reference rf ON rf.frame_id = ap.frame_id
            JOIN careers c ON c.career_id = ap.career_id
            WHERE st.user_rpe = ?
            AND rf.frame_name = ?
            AND c.career_name = ?
            GROUP BY c.career_name, rf.frame_name
        ", [$rpe, $frame_name, $career_name]);
    
        $resultado = [];
        foreach ($estadisticas as $e) {
            $total = $e->aprobadas + $e->desaprobadas + $e->pendientes;
            $total = $total > 0 ? $total : 1;
    
            $resultado[] = [
                'career_name' => $e->career_name,
                'frame_name' => $e->frame_name,
                'aprobado' => round(($e->aprobadas / $total) * 100, 2),
                'desaprobado' => round(($e->desaprobadas / $total) * 100, 2),
                'pendientes' => round(($e->pendientes / $total) * 100, 2),
            ];
        }
    
        return response()->json($resultado);
    }
    public function resumenGeneralPorRPE($rpe)
    {
        $estadisticas = DB::select("
            SELECT
                COALESCE(SUM(CASE WHEN st.status_description = 'APROBADA' THEN 1 ELSE 0 END), 0) AS aprobadas,
                COALESCE(SUM(CASE WHEN st.status_description = 'NO APROBADA' THEN 1 ELSE 0 END), 0) AS desaprobadas,
                COALESCE(SUM(CASE WHEN st.status_description = 'PENDIENTE' THEN 1 ELSE 0 END), 0) AS pendientes
            FROM (
                SELECT DISTINCT ON (e.evidence_id) st.*
                FROM statuses st
                JOIN evidences e ON e.evidence_id = st.evidence_id
                ORDER BY e.evidence_id, st.status_date DESC
            ) st
            WHERE st.user_rpe = ?
        ", [$rpe]);

        $resultado = [];
        foreach ($estadisticas as $e) {
            $total = $e->aprobadas + $e->desaprobadas + $e->pendientes;
            $total = $total > 0 ? $total : 1;

            $resultado[] = [
                'aprobado' => round(($e->aprobadas / $total) * 100, 2),
                'desaprobado' => round(($e->desaprobadas / $total) * 100, 2),
                'pendientes' => round(($e->pendientes / $total) * 100, 2),
            ];
        }

        return response()->json($resultado);
    }    

    public function estadisticasPorAutor($rpe, $frame_name, $career_name)
    {
        error_log('llega a estadisticasPorAutor: ' . $rpe . ' ' . $frame_name . ' ' . $career_name);
        $estadisticas = DB::select("
            SELECT
                c.career_name,
                rf.frame_name,
                COALESCE(SUM(CASE WHEN st.status_description = 'APROBADA' THEN 1 ELSE 0 END), 0) AS aprobadas,
                COALESCE(SUM(CASE WHEN st.status_description = 'NO APROBADA' THEN 1 ELSE 0 END), 0) AS desaprobadas,
                COALESCE(SUM(CASE WHEN st.status_description = 'PENDIENTE' THEN 1 ELSE 0 END), 0) AS pendientes
            FROM (
                SELECT DISTINCT ON (e.evidence_id) st.*
                FROM statuses st
                JOIN evidences e ON e.evidence_id = st.evidence_id
                ORDER BY e.evidence_id, st.status_date DESC
            ) st
            JOIN evidences e ON e.evidence_id = st.evidence_id
            JOIN accreditation_processes ap ON ap.process_id = e.process_id
            JOIN frames_of_reference rf ON rf.frame_id = ap.frame_id
            JOIN careers c ON c.career_id = ap.career_id
            WHERE e.user_rpe = ?
            AND rf.frame_name = ?
            AND c.career_name = ?
            GROUP BY c.career_name, rf.frame_name
        ", [$rpe, $frame_name, $career_name]);
    
        $resultado = [];

        error_log('estadisticasPorAutor: ' . json_encode($estadisticas));
        foreach ($estadisticas as $e) {
            $total = $e->aprobadas + $e->desaprobadas + $e->pendientes;
            $total = $total > 0 ? $total : 1;
    
            $resultado[] = [
                'career_name' => $e->career_name,
                'frame_name' => $e->frame_name,
                'aprobado' => round(($e->aprobadas / $total) * 100, 2),
                'desaprobado' => round(($e->desaprobadas / $total) * 100, 2),
                'pendientes' => round(($e->pendientes / $total) * 100, 2),
            ];
        }
    
        return response()->json($resultado);
    }


    public function resumenGeneralPorRPEA($rpe)
    {
        $estadisticas = DB::select("
            
         SELECT
            COALESCE(SUM(CASE WHEN st.status_description = 'APROBADA' THEN 1 ELSE 0 END), 0) AS aprobadas,
            COALESCE(SUM(CASE WHEN st.status_description = 'NO APROBADA' THEN 1 ELSE 0 END), 0) AS desaprobadas,
            COALESCE(SUM(CASE WHEN st.status_description = 'PENDIENTE' THEN 1 ELSE 0 END), 0) AS pendientes
        FROM (
            SELECT DISTINCT ON (e.evidence_id) e.evidence_id, st.*  -- Incluir e.evidence_id
            FROM statuses st
            JOIN evidences e ON e.evidence_id = st.evidence_id
            JOIN users u ON u.user_rpe = e.user_rpe  -- Aseguramos la relación con users
            WHERE u.user_rpe = ?  -- Filtramos por el RPE del usuario
            ORDER BY e.evidence_id, st.status_date DESC
        ) st
        ", [$rpe]);

        $resultado = [];
        foreach ($estadisticas as $e) {
            $total = $e->aprobadas + $e->desaprobadas + $e->pendientes;
            $total = $total > 0 ? $total : 1;

            $resultado[] = [
                'aprobado' => round(($e->aprobadas / $total) * 100, 2),
                'desaprobado' => round(($e->desaprobadas / $total) * 100, 2),
                'pendientes' => round(($e->pendientes / $total) * 100, 2),
            ];
        }

        return response()->json($resultado);
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
