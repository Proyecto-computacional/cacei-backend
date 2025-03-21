<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    //Ver archivos
    public function index(Request $request)
    {
        $files = File::where('evidence_id', $request->evidence_id)->get();
        return response()->json($files);
    }

    public function show(Request $request)
    {
        $file = File::find($request->file_id);
        if (!$file) {
            return response()->json(['message' => 'Archivo no encontrado'], 404);
        }
        return response()->json($file);
    }

    //Subir archivos
    public function store(Request $request)
    {

        $request->validate([
            'evidence_id' => 'required|exists:evidences,evidence_id',
            'justification' => 'nullable|string'
        ]);


        // Generar un ID único
        do {
            $randomId = rand(1, 100);
        } while (File::where('file_id', $randomId)->exists()); // Verifica que no se repita
        // Guardar el archivo en el servidor
        $path = $request->file('file')->store('uploads', 'public'); //Cambiar por la ruta designada en servidor

        $file = File::create([
            'file_id' => $randomId,
            'file_url' => $path,
            'upload_date' => now(),
            'evidence_id' => $request->evidence_id,
            'justification' => $request->justification
        ]);

        return response()->json($file, 201);
    }

    //Actualizar un archivo
    public function update(Request $request, $file_id)
    {
        $file = File::find($file_id);
        if (!$file) {
            return response()->json(['message' => 'Archivo no encontrado'], 404);
        }

        $request->validate([
            'justification' => 'nullable|string',
        ]);

        if ($request->hasFile('file')) {
            // Eliminar el archivo anterior (opcional)
            Storage::disk('public')->delete($file->file_url);
            // Guardar el nuevo archivo
            $file->file_url = $request->file('file')->store('uploads', 'public');
            $file->upload_date = now();
        }

        if ($request->has('justification')) {
            $file->justification = $request->justification;
        }

        $file->save();

        return response()->json($file);
    }

    //Eliminar un archivo
    public function destroy(Request $request)
    {
        $file = File::find($request->file_id);
        if (!$file) {
            return response()->json(['message' => 'Archivo no encontrado'], 404);
        }

        // Eliminar el archivo del almacenamiento
        Storage::disk('public')->delete($file->file_url);

        // Eliminar el registro de la base de datos
        $file->delete();

        return response()->json(['message' => 'Archivo eliminado correctamente']);
    }
}