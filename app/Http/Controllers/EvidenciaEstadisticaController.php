<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class EvidenciaEstadisticaController extends Controller
{
    
    public function estadisticasPorRPE($rpe, $frame_name, $career_name)
    {
        $estadisticas = DB::select("
            WITH latest_admin_status AS (
                SELECT 
                    e.evidence_id,
                    e.standard_id,
                    e.process_id,
                    s.status_description,
                    ROW_NUMBER() OVER (PARTITION BY e.evidence_id ORDER BY s.status_date DESC) as rn
                FROM evidences e
                JOIN statuses s ON e.evidence_id = s.evidence_id
                JOIN users u ON s.user_rpe = u.user_rpe
                WHERE u.user_role = 'ADMINISTRADOR'
            ),
            career_processes AS (
                SELECT 
                    ap.process_id
                FROM accreditation_processes ap
                JOIN frames_of_reference fr ON ap.frame_id = fr.frame_id
                JOIN careers c ON ap.career_id = c.career_id
                WHERE fr.frame_name = ?
                AND c.career_name = ?
            ),
            career_standards AS (
                SELECT 
                    std.standard_id,
                    cp.process_id
                FROM standards std
                JOIN sections sec ON std.section_id = sec.section_id
                JOIN categories cat ON sec.category_id = cat.category_id
                JOIN frames_of_reference fr ON cat.frame_id = fr.frame_id
                CROSS JOIN career_processes cp
                WHERE fr.frame_name = ?
            )
            SELECT
                c.career_name,
                fr.frame_name,
                cp.process_id,
                COUNT(DISTINCT cs.standard_id) AS total_standards,
                COALESCE(SUM(CASE WHEN las.status_description = 'APROBADA' AND e.process_id = cp.process_id THEN 1 ELSE 0 END), 0) AS aprobadas,
                COALESCE(SUM(CASE WHEN las.status_description = 'NO APROBADA' AND e.process_id = cp.process_id THEN 1 ELSE 0 END), 0) AS desaprobadas,
                COALESCE(SUM(CASE WHEN (las.status_description IS NULL OR las.status_description = 'PENDIENTE') AND e.process_id = cp.process_id THEN 1 ELSE 0 END), 0) AS pendientes
            FROM career_standards cs
            JOIN career_processes cp ON cs.process_id = cp.process_id
            CROSS JOIN (SELECT DISTINCT career_name FROM careers WHERE career_name = ?) c
            CROSS JOIN (SELECT DISTINCT frame_name FROM frames_of_reference WHERE frame_name = ?) fr
            LEFT JOIN standards std ON cs.standard_id = std.standard_id
            LEFT JOIN evidences e ON std.standard_id = e.standard_id AND e.process_id = cp.process_id
            LEFT JOIN latest_admin_status las ON e.evidence_id = las.evidence_id AND las.rn = 1 AND las.process_id = cp.process_id
            GROUP BY c.career_name, fr.frame_name, cp.process_id
        ", [$frame_name, $career_name, $frame_name, $career_name, $frame_name]);

        $resultado = [];
        foreach ($estadisticas as $e) {
            $total = $e->total_standards > 0 ? $e->total_standards : 1;

            $resultado[] = [
                'career_name' => $e->career_name,
                'frame_name' => $e->frame_name,
                'process_id' => $e->process_id,
                'aprobado' => round(($e->aprobadas / $total) * 100, 2),
                'desaprobado' => round(($e->desaprobadas / $total) * 100, 2),
                'pendientes' => round(($e->pendientes / $total) * 100, 2),
                'total_standards' => $e->total_standards
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
        $estadisticas = DB::select("
            WITH latest_status AS (
                SELECT 
                    s.evidence_id,
                    s.status_description,
                    ROW_NUMBER() OVER (PARTITION BY s.evidence_id ORDER BY s.status_date DESC) as rn
                FROM statuses s
            )
            SELECT
                c.career_name,
                rf.frame_name,
                COUNT(e.evidence_id) AS total_evidencias,
                COALESCE(SUM(CASE WHEN ls.status_description = 'APROBADA' THEN 1 ELSE 0 END), 0) AS aprobadas,
                COALESCE(SUM(CASE WHEN ls.status_description = 'NO APROBADA' THEN 1 ELSE 0 END), 0) AS desaprobadas,
                COALESCE(SUM(CASE WHEN ls.status_description IS NULL OR ls.status_description = 'PENDIENTE' THEN 1 ELSE 0 END), 0) AS pendientes
            FROM evidences e
            JOIN accreditation_processes ap ON ap.process_id = e.process_id
            JOIN frames_of_reference rf ON rf.frame_id = ap.frame_id
            JOIN careers c ON c.career_id = ap.career_id
            LEFT JOIN latest_status ls ON e.evidence_id = ls.evidence_id AND ls.rn = 1
            WHERE e.user_rpe = ?
            AND rf.frame_name = ?
            AND c.career_name = ?
            GROUP BY c.career_name, rf.frame_name
        ", [$rpe, $frame_name, $career_name]);

        $resultado = [];

        foreach ($estadisticas as $e) {
            $total = $e->total_evidencias > 0 ? $e->total_evidencias : 1;

            $resultado[] = [
                'career_name' => $e->career_name,
                'frame_name' => $e->frame_name,
                'aprobado' => round(($e->aprobadas / $total) * 100, 2),
                'desaprobado' => round(($e->desaprobadas / $total) * 100, 2),
                'pendientes' => round(($e->pendientes / $total) * 100, 2),
                'total_evidencias' => $e->total_evidencias
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
