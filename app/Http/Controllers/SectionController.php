<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Section;
class SectionController extends Controller
{
    public function getByCategory(Request $request)
    {
        $sections = Section::where('category_id', $request->category_id)->get();
        return response()->json($sections);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|int',
            'section_name' => 'required|string|max:25',
            'section_description' => 'required|string|max:50'
        ]);

        // Generar un ID único
        do {
            $randomId = rand(1, 100);
        } while (Section::where('section_id', $randomId)->exists()); // Verifica que no se repita

        $section = new Section();
        $section->section_id = $randomId;
        $section->category_id = $request->input('category_id');
        $section->section_name = $request->input('section_name');
        $section->section_description = $request->input('section_description');

        $section->save();

        return response()->json([
            'message' => 'Registro creado correctamente',
            'data' => $section
        ], 201);
    }

    public function update(Request $request)
    {
        $request->validate([
            'section_id' => 'required|int',
            'section_name' => 'nullable|string|max:25',
            'section_description' => 'nullable|string|max:50'
        ]);

        $section = Section::find($request->section_id);

        if (!$section) {
            return response()->json([
                'message' => 'Registro no encontrado.'
            ], 404);
        }

        $section->section_name = $request->input('section_name');
        $section->section_description = $request->input('section_description');

        $section->save();

        return response()->json([
            'message' => 'Registro actualizado correctamente.',
            'data' => $section
        ]);
    }
}
