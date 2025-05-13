<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reviser;
class ReviserController extends Controller
{
    public function index(Request $request)
    {
        $processId = $request->query('process_id');

        $query = Reviser::with([
            'evidence.standard.section.category',
            'user'
        ]);

        if ($processId) {
            $query->whereHas('evidence', function($q) use ($processId) {
                $q->where('process_id', $processId);
            });
        }

        $revisers = $query->get()->map(function ($reviser) {
            return [
                'user_name' => $reviser->user->user_name,
                'evidence_id' => $reviser->evidence_id,
                'due_date' => $reviser->evidence->due_date,
                'standard_name' => $reviser->evidence->standard->standard_name,
                'section_name' => $reviser->evidence->standard->section->section_name,
                'category_name' => $reviser->evidence->standard->section->category->category_name,
                'process_id' => $reviser->evidence->process_id
            ];
        });

        return response()->json($revisers);
    }
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'user_rpe' => 'required|string',
            'evidence_id' => 'required|integer',
        ]);

        do {
            $randomId = rand(1, 100);
        } while (Reviser::where('reviser_id', $randomId)->exists()); // Verifica que no se repita

        $revisor = Reviser::create([
            'reviser_id' => $randomId,
            'evidence_id' => $request->evidence_id,
            'user_rpe' => $request->user_rpe,
        ]);

        // Retornar la respuesta con la notificación creada
        return response()->json([
            'message' => 'Asignado exitosamente',
            'notification' => $revisor
        ], 201);
    }
}
