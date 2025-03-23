<?php

namespace App\Http\Controllers;

use App\Models\Evidence;
use Illuminate\Http\Request;

class evidenceController extends Controller
{
    public function allEvidence($request){
        $query = Evidence::query();
        if ($request->has('search')) {
            $search = $request->input('search');
    
            $query->where(function ($q) use ($search) {
                $q->where('user_mail', 'LIKE', "%$search%")
                  ->orWhere('user_rpe', 'LIKE' , "%$search%");
            });
        }
        return response()->json([
            'evidencias' => $query->cursorPaginate(10), // Pagina los resultados si hay muchos
            'estatus' => ['APROBADO', 'NO APROBADO', 'PENDIENTE']
        ]);
    }
}
