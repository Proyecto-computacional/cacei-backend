<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Section;
use Illuminate\Support\Facades\DB;
class SectionController extends Controller
{
    public function getByCategory(Request $request)
    {
        $sections = Section::where('category_id', $request->category_id)->orderBy('indice')->get();
        return response()->json($sections);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|int',
            'section_name' => 'required|string|max:100',
            'section_description' => 'required|string|max:1500',
            'is_standard' => 'required|boolean'
        ]);

        $indice = 0;
        do {
            $indice = $indice + 1;
        } while (Section::where('indice', $indice)->where('category_id', $request->input('category_id'))->exists());

        $section = new Section();
        $section->category_id = $request->input('category_id');
        $section->section_name = $request->input('section_name');
        $section->section_description = $request->input('section_description');
        $section->indice = $indice;
        $section->is_standard = $request->input('is_standard');

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
            'section_name' => 'nullable|string|max:100',
            'section_description' => 'nullable|string|max:1500',
            'is_standard' => 'required|boolean'
        ]);

        $section = Section::find($request->section_id);

        if (!$section) {
            return response()->json([
                'message' => 'Registro no encontrado.'
            ], 404);
        }

        $section->section_name = $request->input('section_name');
        $section->section_description = $request->input('section_description');
        $section->is_standard = $request->input('is_standard');

        $section->save();

        return response()->json([
            'message' => 'Registro actualizado correctamente.',
            'data' => $section
        ], 201);
    }

    public function reorder(Request $request)
    {
        $ordered = $request->validate(['ordered_ids' => 'required|array']);

        DB::transaction(function () use ($ordered) {
            foreach ($ordered['ordered_ids'] as $i => $id) {
                Section::where('section_id', $id)->update(['indice' => $i + 1]);
            }
        });
        return response()->json(['Ordenado correctamente' => true]);
    }
}
