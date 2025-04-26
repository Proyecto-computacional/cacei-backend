<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EvidenciaEstadisticaController extends Controller
{
    public function estadisticasPorCarrera()
    {
        $datos = DB::select("
            SELECT
                c.career_name,
                fr.frame_name,
                COUNT(e.evidence_id) AS total,
                SUM(CASE WHEN s.status_description = 'Aprobado' THEN 1 ELSE 0 END) AS aprobadas,
                SUM(CASE WHEN s.status_description = 'Pendiente' THEN 1 ELSE 0 END) AS pendientes,
                COUNT(e.evidence_id) - COUNT(s.status_id) AS sin_subir
            FROM careers c
            JOIN accreditation_processes ap ON ap.career_id = c.career_id
            JOIN frames_of_reference fr ON fr.frame_id = ap.frame_id
            JOIN evidences e ON e.process_id = ap.process_id
            LEFT JOIN (
                SELECT DISTINCT ON (evidence_id) *
                FROM statuses
                ORDER BY evidence_id, status_date DESC
            ) s ON s.evidence_id = e.evidence_id
            GROUP BY c.career_name, fr.frame_name
        ");
//en el left join trae el último estatus registrado de cada evidencia (por evidence_id), gracias a DISTINCT ON (...) y ORDER BY status_date DESC


        $resumen = collect($datos)->map(function ($item) {// $resumen es una nueva colección como arreglo y map te permite modificarlos
            $total = $item->total ?: 1;//(Numero de evidencias) Si $item->total tiene un valor (distinto de null, 0, "", etc.), úsalo.Si no tiene valor (o es cero), usa 1 en su lugar.
            return [
                'carrera' => $item->career_name,
                'marco_referencia' => $item->frame_name,
                'aprobado' => round(($item->aprobadas / $total) * 100, 2),
                'pendiente' => round(($item->pendientes / $total) * 100, 2),
                'sin_subir' => round(($item->sin_subir / $total) * 100, 2),
            ];
        });

        return response()->json($resumen);
    }


    public function resumenGeneral()
    {
        $datos = DB::select("
            SELECT
                COUNT(e.evidence_id) AS total,
                SUM(CASE WHEN s.status_description = 'Aprobado' THEN 1 ELSE 0 END) AS aprobadas,
                SUM(CASE WHEN s.status_description = 'Pendiente' THEN 1 ELSE 0 END) AS pendientes,
                COUNT(e.evidence_id) - COUNT(s.status_id) AS sin_subir
            FROM evidences e
            LEFT JOIN (
                SELECT DISTINCT ON (evidence_id) *
                FROM statuses
                ORDER BY evidence_id, status_date DESC
            ) s ON s.evidence_id = e.evidence_id
        ");
          //en el left join trae el último estatus registrado de cada evidencia (por evidence_id), gracias a DISTINCT ON (...) y ORDER BY status_date DESC
        $d = $datos[0];// el unico dato de la lista o arreglo
        $total = $d->total ?: 1;

        return response()->json([
            'aprobado' => round(($d->aprobadas / $total) * 100, 2),
            'pendiente' => round(($d->pendientes / $total) * 100, 2),
            'sin_subir' => round(($d->sin_subir / $total) * 100, 2),
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
}