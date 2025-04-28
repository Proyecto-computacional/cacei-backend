<?php

namespace App\Http\Controllers;

use App\Models\Evidence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class EvidenceController extends Controller
{
    public function allEvidence(Request $request)
    {
        error_log('llega aquí al evidence');
        $user = auth()->user();
        $role = $user->user_role;

        $query = Evidence::query()
            ->leftJoin('standards', 'evidences.standard_id', '=', 'standards.standard_id')
            ->leftJoin('users as evidence_owner', 'evidences.user_rpe', '=', 'evidence_owner.user_rpe')
            ->leftJoin('accreditation_processes', 'evidences.process_id', '=', 'accreditation_processes.process_id')->leftJoin('careers', 'accreditation_processes.career_id', '=', 'careers.career_id')
            ->leftJoin('areas', 'careers.area_id', '=', 'areas.area_id')
            ->leftJoin('users as career_coordinator', 'careers.user_rpe', '=', 'career_coordinator.user_rpe')
            ->leftJoin('users as area_manager', 'areas.user_rpe', '=', 'area_manager.user_rpe')
            ->leftJoin('revisers', 'evidences.evidence_id', '=', 'revisers.evidence_id') // Relación con revisores
            ->leftJoin('users as professors', 'revisers.user_rpe', '=', 'professors.user_rpe') // Profesores revisores
            ->select(
                'evidences.*',
                'standards.standard_name as standard_name',
                'evidence_owner.user_name as evidence_owner_name',
                'accreditation_processes.process_name as process_name',
                'career_coordinator.user_rpe as career_admin_rpe',
                'area_manager.user_rpe as area_admin_rpe',
                'professors.user_rpe as revisor_rpe', // Profesor revisor de la evidencia
            );
        // Filtrar por rol de usuario
        if ($role === 'ADMINISTRADOR') {
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
                $q->where('standard_name', 'LIKE', "%$search%")
                    ->orWhere('evidence_owner.user_name', 'LIKE', "%$search%")
                    ->orWhere('evidences.user_rpe', 'LIKE', "%$search%");
            });
        }

        if ($request->has('sort_by')) {
            $column = $request->input('sort_by');  // Columna por la que se ordenará
            $direction = $request->input('sort_order', $request->input('order')); // Orden (asc o desc)

            // Validar que la columna está permitida para evitar SQL Injection
            $allowedColumns = ['evidence_id', 'standard_name', 'process_name', 'file_url'];

            if (in_array($column, $allowedColumns)) {
                $query->orderBy($column, $direction);
            }
        }

        $evidences = $query->orderBy('evidence_id')->cursorPaginate(10);
        

        $evidences->each(function ($evidence) {
            $evidence->files = DB::table('files')
                ->where('evidence_id', $evidence->evidence_id)
                ->get()
                ->map(function ($file) {
                    $file->file_url = url($file->file_url); // Agregar URL completa
                    return $file;
                });
        });
        $evidences->each(function ($evidence) {
            $evidence->statuses = DB::table('statuses')
                ->join('users', 'statuses.user_rpe', '=', 'users.user_rpe')
                ->where('evidence_id', $evidence->evidence_id)
                ->select('statuses.*', 'user_role')
                ->get()
                ->map(callback: function ($status) {
                    return $status;
                });
        });

        return response()->json([
            'evidencias' => $evidences,
            'estatus' => ['APROBADO', 'NO APROBADO', 'PENDIENTE'],
            'Rol' => $role
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'standard_id' => 'required|int',
            'user_rpe' => 'required|string',
            'process_id' => 'required|int',
            'due_date' => 'required|date'
        ]);

        do {
            $randomId = rand(1, 100);
        } while (Evidence::where('evidence_id', $randomId)->exists()); // Verifica que no se repita

        $evidence = Evidence::create([
            'evidence_id' => $randomId,
            'standard_id' => $request->standard_id,
            'user_rpe' => $request->user_rpe,
            'process_id' => $request->process_id,
            'due_date' => $request->due_date
        ]);

        // Retornar la respuesta con la notificación creada
        return response()->json([
            'message' => 'Asignado exitosamente',
            'evidence' => $evidence
        ], 201);
    }
    public function getByStandard(Request $request)
    {
        $evidences = Evidence::where('standard_id', $request->standard_id)->get();
        return response()->json($evidences);
    }
}
