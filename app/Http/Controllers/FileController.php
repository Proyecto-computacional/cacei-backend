<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Jobs\BackupJob;
use Mews\Purifier\Facades\Purifier as FacadesPurifier;

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
            'files.*' => 'file'
        ]);
    
        $evidence = \App\Models\Evidence::where('evidence_id', $request->evidence_id)->first();
        $standard_id = $evidence->standard_id;
        $evidence_id = $evidence->evidence_id;
        $group_id = $evidence->group_id;
    
        // Actualizar la justificación en la evidencia si se proporciona
        if ($request->has('justification')) {
            $evidence->justification = FacadesPurifier::clean($request->justification);
            $evidence->save();
        }
    
        $savedFiles = [];
        
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                // Generar un ID único por archivo
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
                    'file_name' => $file->getClientOriginalName()
                ]);
        
                $savedFiles[] = $newFile;
            }
        
            return response()->json($savedFiles, 201); // Retornar todos los archivos subidos
        }

        return response()->json(['message' => 'No se han subido archivos'], 201);
    }

    //Actualizar un archivo
    public function update(Request $request, $evidence_id)
    {
        $file = File::where('evidence_id', $evidence_id)->first();

        if (!$file) {
            return response()->json(['message' => 'Archivo no encontrado'], 404);
        }

        $request->validate([
            'justification' => 'nullable|string',
        ]);

        // Actualizar la justificación en la evidencia si se proporciona
        if ($request->has('justification')) {
            $evidence = \App\Models\Evidence::where('evidence_id', $evidence_id)->first();
            $evidence->justification = FacadesPurifier::clean($request->justification);
            $evidence->save();
        }

        if ($request->hasFile('file')) {
            // Eliminar el archivo anterior (opcional)
            Storage::disk('public')->delete($file->file_url);
            // Guardar el nuevo archivo
            $file->file_url = $request->file('file')->store('uploads', 'public');
            $file->upload_date = now();
            $file->save();
        }

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

    public function deleteFile($file_id)
{
    $file = File::find($file_id);

    if (!$file) {
        return response()->json(['message' => 'Archivo no encontrado'], 404);
    }

    $path = public_path('uploads/' . $file->file_url);
    if (file_exists($path)) {
        unlink($path);
    }

    $file->delete();

    return response()->json(['message' => 'Archivo eliminado correctamente']);
}
}