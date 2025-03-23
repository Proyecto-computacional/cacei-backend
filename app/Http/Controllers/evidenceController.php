<?php

namespace App\Http\Controllers;

use App\Models\Evidence;
use Illuminate\Http\Request;

class evidenceController extends Controller
{
    public function allEvidence(Request $request){
        $user = auth()->user();
        $role = $user->role; 

        $query = Evidence::query()
        ->leftJoin('standards', 'evidences.standard_id', '=', 'standards.standard_id')
        ->leftJoin('users', 'evidences.user_rpe', '=', 'users.user_rpe')
        ->leftJoin('accreditation_processes', 'evidences.process_id', '=', 'accreditation_processes.process_id')
        ->leftJoin('files', 'evidences.evidence_id', '=', 'files.evidence_id')
        ->select(
            'evidences.*',
            'standards.standard_name as standard_name',
            'users.user_rpe as user_rpe',
            'accreditation_processes.process_id as process_id',
            'files.file_url as file_url',
        )
        ->whereNull('files.file_url') // Filtrar los registros que no tienen archivo
        ;

        // Filtrar por rol de usuario
        if ($role === 'ADMINISTADOR') {
            // Todas las evidencias (sin filtro adicional)
        } elseif ($role === 'COORDINADOR') {
            // Si es usuario, solo puede ver sus propios registros
            $query->where('evidences.user_rpe', $user->rpe);
        }

    if ($request->has('search')) {
        $search = $request->input('search');

        $query->where(function ($q) use ($search) {
            $q->where('standards.name', 'LIKE', "%$search%")
              ->orWhere('users.name', 'LIKE', "%$search%")
              ->orWhere('evidences.user_rpe', 'LIKE', "%$search%");
        });
    }

    return response()->json([
        'evidencias' => $query->cursorPaginate(10), // Pagina los resultados
        'estatus' => ['APROBADO', 'NO APROBADO', 'PENDIENTE']
    ]);
    }
}
