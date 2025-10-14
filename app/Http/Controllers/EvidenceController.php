<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Career;
use App\Models\Evidence;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mews\Purifier\Facades\Purifier as FacadesPurifier;

class EvidenceController extends Controller
{
    public function show($id)
    {
        $evidence = Evidence::with([
            'process:process_id,process_name,career_id',
            'standard:standard_id,standard_name,section_id,help,is_transversal',
            'standard.section:section_id,section_name,category_id',
            'standard.section.category:category_id,category_name',
            'files:file_id,evidence_id,file_url,upload_date,file_name',
            'status' => function ($query) {
                $query->orderByDesc('status_date')
                    ->orderByDesc('status_id');
            },
            'status.user:user_rpe,user_name,user_role' // 
        ])
            ->where('evidence_id', $id)
            ->first();


        if (!$evidence) {
            return response()->json(['message' => 'Evidencia no encontrada'], 404);
        }

        $responsable = User::where('user_rpe', $evidence->user_rpe)->first();

        $primerRevisor = EvidenceController::nextRevisor($responsable, $evidence);

        return response()->json([
            'evidence' => $evidence,
            'first_revisor' => $primerRevisor
        ]);
    }

    public function nextRevisor($user, $evidence)
    {
        $nextRevisor = null;
        if ($evidence->standard->is_transversal === true) {
            $nextRevisor = User::where('user_role', 'ADMINISTRADOR')->pluck('user_rpe');
        } else {
            $evidenceCareer = $evidence->process->career;
            $evidenceArea = $evidenceCareer->area;

            if ($user->user_role === 'COORDINADOR DE CARRERA') {
                if ($evidenceCareer->user_rpe == $user->user_rpe) {
                    $nextRevisor = [$evidenceCareer->area->user_rpe];
                }
            }

            if ($user->user_role === 'JEFE DE AREA') {
                if ($evidenceArea->user_rpe == $user->user_rpe) {
                    //$nextRevisor = User::where('user_role', 'DIRECTIVO')->pluck('user_rpe');
                    $nextRevisor = User::where('user_role', 'ADMINISTRADOR')->pluck('user_rpe');
                }
            }

            if ($user->user_role === 'DIRECTIVO') {
                if ($evidenceCareer->user_rpe == $user->user_rpe) {
                    $nextRevisor = User::where('user_role', 'ADMINISTRADOR')->pluck('user_rpe');
                }
            }

            if (
                $user->user_role === 'PROFESOR' ||
                $user->user_role === 'DEPARTAMENTO UNIVERSITARIO' ||
                $nextRevisor === null
            ) {

                $nextRevisor = [$evidenceCareer->user_rpe];
            }
            if ($user->user_role === 'ADMINISTRADOR') {
                $nextRevisor = [];
            }
        }
        return $nextRevisor;
    }
    public function allEvidence(Request $request)
    {

        $user = auth()->user();
        $role = $user->user_role;

        $query = Evidence::query()
            ->leftJoin('standards', 'evidences.standard_id', '=', 'standards.standard_id')
            ->leftJoin('sections', 'standards.section_id', '=', 'sections.section_id')
            ->leftJoin('categories', 'sections.category_id', '=', 'categories.category_id')
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
                'standards.is_transversal as is_transversal',
                'sections.section_name as section_name',
                'categories.category_name as category_name',
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

        /*if ($request->has('search')) {
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
        }*/

        //CAmbiat
        $evidences = $query->orderBy('evidence_id')->get();


        $evidences->each(function ($evidence) {
            $evidence->files = DB::table('files')
                ->where('evidence_id', $evidence->evidence_id)
                ->get()
                ->map(function ($file) {
                    $file->file_url = asset('storage/' . $file->file_url); // Use asset() helper to generate correct public URL
                    return $file;
                });
        });
        $evidences->each(function ($evidence) {
            $evidence->statuses = DB::table('statuses')
                ->join('users', 'statuses.user_rpe', '=', 'users.user_rpe')
                ->where('evidence_id', $evidence->evidence_id)
                ->select(
                    'statuses.*',
                    'users.user_role',
                    'users.user_name',
                    DB::raw("statuses.status_date AT TIME ZONE 'UTC' AT TIME ZONE 'America/Mexico_City' as status_date")
                )
                ->orderBy(DB::raw("statuses.status_date AT TIME ZONE 'UTC' AT TIME ZONE 'America/Mexico_City'"), 'desc')
                ->get()
                ->map(callback: function ($status) {
                    return $status;
                });
        });

        return response()->json([
            'evidencias' => $evidences,
            'estatus' => ['APROBADA', 'NO APROBADA', 'PENDIENTE'],
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

    public function update(Request $request, $evidence_id)
    {
        $request->validate([
            'justification' => 'nullable|string'
        ]);

        $evidence = Evidence::where('evidence_id', $evidence_id)->first();

        if (!$evidence) {
            return response()->json(['message' => 'Evidencia no encontrada'], 404);
        }

        if ($request->has('justification')) {
            $evidence->justification = FacadesPurifier::clean($request->justification);
            $evidence->save();
        }

        return response()->json([
            'message' => 'Evidencia actualizada correctamente',
            'evidence' => $evidence
        ]);
    }

    public function getByStandardUpload($standard_id)
    {
        $evidences = Evidence::with([
            'process:process_id,process_name,career_id',
            'standard:standard_id,standard_name,section_id,help,is_transversal',
            'files:file_id,evidence_id,file_url,upload_date,file_name'
        ])
            ->where('standard_id', $standard_id)
            ->get();

        return response()->json($evidences);
    }
}
