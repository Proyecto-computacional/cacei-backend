<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Jobs\BackupJob;

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
            'files.*' => 'required|file',
            'justification' => 'nullable|string'
        ]);
    
        $evidence = \App\Models\Evidence::where('evidence_id', $request->evidence_id)->first();
        $standard_id = $evidence->standard_id;
        $evidence_id = $evidence->evidence_id;
        $group_id = $evidence->group_id;
    
        $savedFiles = [];
    
        foreach ($request->file('files') as $file) {
            // Generar un ID único por archivo
            //porque no autoincremento?
            do {
                $randomId = rand(1, 1000000);
            } while (File::where('file_id', $randomId)->exists());
    
            // Preparar nuevo nombre y path
            $extension = $file->getClientOriginalExtension();
            $newName = $standard_id . '_' . $evidence_id . '_' . $group_id . '-' . $randomId . '.' . $extension;
            $path_name = 'uploads/' . $evidence_id;
    
            // Guardar archivo
            $path = $file->storeAs($path_name, $newName, 'public');
    
            // Crear registro
            $newFile = File::create([
                'file_id' => $randomId,
                'file_url' => $path,
                'upload_date' => now(),
                'evidence_id' => $evidence_id,
                'justification' => $request->justification
            ]);
    
            $savedFiles[] = $newFile;
        }
    
        BackupJob::dispatch();
    
        return response()->json($savedFiles, 201); // Retornar todos los archivos subidos
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

        BackupJob::dispatch();

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

        BackupJob::dispatch();

        return response()->json(['message' => 'Archivo eliminado correctamente']);
    }
}