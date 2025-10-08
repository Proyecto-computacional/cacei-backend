<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FrameOfReference;

class FrameOfReferenceController extends Controller
{
    // Obtener todos los registros en formato JSON
    public function index()
    {
        $data = FrameOfReference::all();
        return response()->json($data);
    }

    // Insertar un nuevo registro
    public function store(Request $request)
    {
        $request->validate([
            'frame_name' => 'required|string|max:60',
        ]);

        // Generar un ID único
        do {
            $randomId = rand(1, 100);
        } while (FrameOfReference::where('frame_id', $randomId)->exists()); // Verifica que no se repita

        $frame = new FrameOfReference();
        $frame->frame_id = $randomId;
        $frame->frame_name = $request->input('frame_name');

        $frame->save();

        return response()->json([
            'message' => 'Registro creado correctamente',
            'data' => $frame
        ], 201);
    }

    //Actualizar un registro
    public function update(Request $request)
    {
        $request->validate([
            'frame_id' => 'required|int',
            'frame_name' => 'required|string|max:20'
        ]);

        $frame = FrameOfReference::find($request->frame_id);

        if (!$frame) {
            return response()->json([
                'message' => 'Registro no encontrado.'
            ], 404);
        }

        $frame->frame_name = $request->input('frame_name');

        $frame->save();

        return response()->json([
            'message' => 'Registro actualizado correctamente.',
            'data' => $frame
        ]);
    }

    public function getFrameById($id){
        $frame = FrameOfReference::with('categories.sections.standards')->find($id);

        if(!$frame){
            return response()->json(
                [
                    'message' => "Marco de referencia no encontrado",
                    'frame' => null
                ], 404
            );
        }

        return response()->json(
            [
                'message' => "Marco de referencia encontrado",
                'frame' => $frame
            ], 200
        );
    }
}
