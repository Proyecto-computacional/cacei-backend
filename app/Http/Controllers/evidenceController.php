<?php

namespace App\Http\Controllers;

use App\Models\Evidence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class evidenceController extends Controller
{
    public function allEvidence(Request $request)
    {
        $user = auth()->user();
        $role = $user->user_role;

        $query = Evidence::query()
            ->leftJoin('standards', 'evidences.standard_id', '=', 'standards.standard_id')
            ->leftJoin('users', 'evidences.user_rpe', '=', 'users.user_rpe')
            ->leftJoin('accreditation_processes', 'evidences.process_id', '=', 'accreditation_processes.process_id')
            ->leftJoin('files', 'evidences.evidence_id', '=', 'files.evidence_id')
            ->leftJoin('careers', 'accreditation_processes.career_id', '=', 'careers.career_id')
            ->leftJoin('areas', 'careers.area_id', '=', 'areas.area_id')
            ->leftJoin('users as career_coordinator', 'careers.user_rpe', '=', 'career_coordinator.user_rpe')
            ->leftJoin('users as area_manager', 'areas.user_rpe', '=', 'area_manager.user_rpe')
            ->leftJoin('revisers', 'evidences.evidence_id', '=', 'revisers.evidence_id') // Relación con revisores
            ->leftJoin('users as professors', 'revisers.user_rpe', '=', 'professors.user_rpe') // Profesores revisores
            ->select(
                'evidences.*',
                'standards.standard_name as standard_name',
                'users.user_name as user_name',
                'accreditation_processes.process_name as process_name',
                'files.file_url as file_url',
                'career_coordinator.user_rpe as career_admin_rpe',
                'area_manager.user_rpe as area_admin_rpe',
                'professors.user_rpe as revisor_rpe' // Profesor revisor de la evidencia
            )
            ->whereNull('files.file_url') // Filtrar los registros que no tienen archivo
        ;
        // Filtrar por rol de usuario
        if ($role === 'ADMINISTADOR') {
            // Todas las evidencias (sin filtro adicional)
        } elseif ($role === 'JEFE DE AREA') {
            $query->where('area_manager.user_rpe', $user->user_rpe);
        } elseif ($role == 'COORDINADOR DE CARRERA') {
            $query->where('career_coordinator.user_rpe', $user->user_rpe);
        } elseif ($role == 'PROFESOR') {
            $query->where(function ($q) use ($user) {
                $q->where('evidences.user_rpe', $user->user_rpe)
                    ->orWhereExists(function ($subquery) use ($user) {
                        $subquery->select(DB::raw(1))
                            ->from('revisers')
                            ->whereColumn('revisers.evidence_id', 'evidences.evidence_id')
                            ->where('revisers.user_rpe', $user->user_rpe);
                    });
            });
        }

        if ($request->has('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('standards.name', 'LIKE', "%$search%")
                    ->orWhere('users.name', 'LIKE', "%$search%")
                    ->orWhere('evidences.user_rpe', 'LIKE', "%$search%");
            });
        }

        if ($request->has('sort_by')) {
            $column = $request->input('sort_by');  // Columna por la que se ordenará
            $direction = $request->input('sort_order', $request->input('order')); // Orden (asc o desc)

            // Validar que la columna está permitida para evitar SQL Injection
            $allowedColumns = ['evidence_id', 'standard_name', 'user_name', 'process_name', 'file_url'];

            if (in_array($column, $allowedColumns)) {
                $query->orderBy($column, $direction);
            }
        }

        return response()->json([
            'evidencias' => $query->cursorPaginate(10), // Pagina los resultados
            'estatus' => ['APROBADO', 'NO APROBADO', 'PENDIENTE'],
            'Rol' => $role
        ]);
    }
}
