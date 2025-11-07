<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(Category::all());
    }

    public function getByFrame(Request $request)
    {
        $categories = Category::where('frame_id', $request->frame_id)->orderBy('indice')->get();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $indice = 0;
        $request->validate([
            'frame_id' => 'required|int',
            'category_name' => 'required|string|max:100'
        ]);

        // Generar un ID único
        do {
            $randomId = rand(1, 100);
        } while (Category::where('category_id', $randomId)->exists()); // Verifica que no se repita

        do {
            $indice = $indice + 1;
        } while (Category::where('indice', $indice)->where('frame_id', $request->input('frame_id'))->exists());

        $category = new Category();
        $category->category_id = $randomId;
        $category->frame_id = $request->input('frame_id');
        $category->category_name = $request->input('category_name');
        $category->indice = $indice;

        $category->save();

        return response()->json([
            'message' => 'Registro creado correctamente',
            'data' => $category
        ], 201);
    }

    public function update(Request $request)
    {
        $request->validate([
            'category_id' => 'required|int',
            'category_name' => 'required|string|max:100'
        ]);

        $category = Category::find($request->category_id);

        if (!$category) {
            return response()->json([
                'message' => 'Registro no encontrado.'
            ], 404);
        }

        $category->category_name = $request->input('category_name');

        $category->save();

        return response()->json([
            'message' => 'Registro actualizado correctamente.',
            'data' => $category
        ]);
    }

    public function reorder(Request $request)
    {
        $ordered = $request->validate(['ordered_ids' => 'required|array']);

        DB::transaction(function () use ($ordered) {
            foreach ($ordered['ordered_ids'] as $i => $id) {
                Category::where('category_id', $id)->update(['indice' => $i + 1]);
            }
        });
        return response()->json(['Ordenado correctamente' => true]);
    }

    public function getProgressByProcess($processId, Request $request)
    {
        // First verify the process exists
        $process = DB::table('accreditation_processes')
            ->where('process_id', $processId)
            ->first();

        if (!$process) {
            return response()->json(['error' => 'Proceso no encontrado'], 404);
        }

        $user = auth()->user();
        $userRole = $user->user_role;
        $userRpe = $user->user_rpe;

        // Modificar la consulta base para incluir el filtro de usuario si es profesor
        $evidenceFilter = $userRole === 'PROFESOR' ? "AND e.user_rpe = ?" : "";
        $evidenceParams = $userRole === 'PROFESOR'
            ? [$processId, $userRpe, $processId, $userRpe]
            : [$processId, $processId];

        // Get all categories with their evidences for this process
        $categories = DB::select("
            WITH evidence_status AS (
                SELECT 
                    e.evidence_id,
                    s.status_description,
                    ROW_NUMBER() OVER (PARTITION BY e.evidence_id ORDER BY s.status_date DESC) as rn
                FROM evidences e
                LEFT JOIN statuses s ON e.evidence_id = s.evidence_id
                WHERE e.process_id = ? $evidenceFilter
            )
            SELECT 
                c.category_id,
                c.category_name,
                COUNT(e.evidence_id) as total_evidences,
                COUNT(CASE WHEN es.status_description = 'APROBADA' THEN 1 END) as approved_count,
                COUNT(CASE WHEN es.status_description = 'NO APROBADA' THEN 1 END) as rejected_count,
                COUNT(CASE WHEN es.status_description = 'PENDIENTE' OR es.status_description IS NULL THEN 1 END) as pending_count,
                COUNT(CASE WHEN NOT EXISTS (
                    SELECT 1 FROM files f WHERE f.evidence_id = e.evidence_id
                ) THEN 1 END) as not_uploaded_count
            FROM categories c
            JOIN sections s ON c.category_id = s.category_id
            JOIN standards st ON s.section_id = st.section_id
            LEFT JOIN evidences e ON st.standard_id = e.standard_id AND e.process_id = ? $evidenceFilter
            LEFT JOIN evidence_status es ON e.evidence_id = es.evidence_id AND es.rn = 1
            GROUP BY c.category_id, c.category_name
        ", $evidenceParams);

        $result = [];
        foreach ($categories as $category) {
            $total = $category->total_evidences;
            if ($total > 0) {
                $approved = round(($category->approved_count / $total) * 100);
                $rejected = round(($category->rejected_count / $total) * 100);
                $pending = round(($category->pending_count / $total) * 100);
                $not_uploaded = round(($category->not_uploaded_count / $total) * 100);
            } else {
                $approved = $rejected = $pending = $not_uploaded = 0;
            }

            // Modificar la consulta de evidencias para incluir el filtro de usuario si es profesor
            $evidenceParams = $userRole === 'PROFESOR'
                ? [$processId, $userRpe, $processId, $category->category_id, $userRpe]
                : [$processId, $processId, $category->category_id];

            $evidences = DB::select("
                WITH latest_status AS (
                    SELECT 
                        e.evidence_id,
                        s.status_description,
                        ROW_NUMBER() OVER (PARTITION BY e.evidence_id ORDER BY s.status_date DESC) as rn
                    FROM evidences e
                    LEFT JOIN statuses s ON e.evidence_id = s.evidence_id
                    WHERE e.process_id = ? $evidenceFilter
                )
                SELECT 
                    e.evidence_id,
                    c.category_name,
                    s.section_name,
                    st.standard_name,
                    u.user_name as responsible,
                    CONCAT(s.section_name, ' - ', st.standard_name) as info,
                    COALESCE(f.file_name, 'No file uploaded') as file,
                    COALESCE(ls.status_description, 'PENDIENTE') as verified
                FROM categories c
                JOIN sections s ON c.category_id = s.category_id
                JOIN standards st ON s.section_id = st.section_id
                LEFT JOIN evidences e ON st.standard_id = e.standard_id AND e.process_id = ?
                LEFT JOIN users u ON e.user_rpe = u.user_rpe
                LEFT JOIN files f ON e.evidence_id = f.evidence_id
                LEFT JOIN latest_status ls ON e.evidence_id = ls.evidence_id AND ls.rn = 1
                WHERE c.category_id = ? $evidenceFilter
                ORDER BY s.section_name, st.standard_name
            ", $evidenceParams);

            $result[] = [
                'category_name' => $category->category_name,
                'approved' => $approved,
                'rejected' => $rejected,
                'pending' => $pending,
                'not_uploaded' => $not_uploaded,
                'evidences' => $evidences
            ];
        }

        return response()->json($result);
    }
}
