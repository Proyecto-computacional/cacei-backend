<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Standard;
use Illuminate\Support\Facades\DB;

class StandardController extends Controller
{
    public function getBySection(Request $request)
    {
        $standards = Standard::where('section_id', $request->section_id)->orderBy('indice')->get();
        return response()->json($standards);
    }

    public function store(Request $request)
    {
        // cuando se guarda un standard 'transversal' no hace nada especial
        $indice = 0;
        $request->validate([
            'section_id' => 'required|int',
            'standard_name' => 'required|string|max:25',
            'standard_description' => 'required|string|max:50',
            'is_transversal' => 'required|boolean',
            'help' => 'required|string|max:255'
        ]);

        // Generar un ID único
        do {
            $randomId = rand(1, 100);
        } while (Standard::where('standard_id', $randomId)->exists()); // Verifica que no se repita

        do {
            $indice = $indice + 1;
        } while (Standard::where('indice', $indice)->where('section_id', $request->input('section_id'))->exists());

        $standard = new Standard();
        $standard->standard_id = $randomId;
        $standard->section_id = $request->input('section_id');
        $standard->standard_name = $request->input('standard_name');
        $standard->standard_description = $request->input('standard_description');
        $standard->is_transversal = $request->input('is_transversal');
        $standard->help = $request->input('help');
        $standard->indice = $indice;

        $standard->save();

        return response()->json([
            'message' => 'Registro creado correctamente',
            'data' => $standard
        ], 201);
    }

    public function update(Request $request)
    {
        $request->validate([
            'standard_id' => 'required|int',
            'standard_name' => 'nullable|string|max:25',
            'standard_description' => 'nullable|string|max:50',
            'is_transversal' => 'nullable|boolean',
            'help' => 'nullable|string|max:255'
        ]);

        $standard = Standard::find($request->standard_id);

        if (!$standard) {
            return response()->json([
                'message' => 'Registro no encontrado.'
            ], 404);
        }

        $standard->standard_name = $request->input('standard_name');
        $standard->standard_description = $request->input('standard_description');
        $standard->is_transversal = $request->input('is_transversal');
        $standard->help = $request->input('help');

        $standard->save();

        return response()->json([
            'message' => 'Registro actualizado correctamente.',
            'data' => $standard
        ], 201);
    }

    public function destroy($id)
    {
        try {
            $standard = Standard::findOrFail($id);
            $standard->delete();

            return response()->json([
                'message' => 'Criterio eliminado correctamente',
                'data' => $standard
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el criterio',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function reorder(Request $request)
    {
        $ordered = $request->validate(['ordered_ids' => 'required|array']);

        DB::transaction(function () use ($ordered) {
            foreach ($ordered['ordered_ids'] as $i => $id) {
                Standard::where('standard_id', $id)->update(['indice' => $i + 1]);
            }
        });
        return response()->json(['Ordenado correctamente' => true]);
    }
}
