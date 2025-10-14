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
        Log::info('Iniciando store de archivos');
        Log::info('Request data:', $request->all());
        Log::info('Files:', $request->hasFile('files') ? ['present' => true] : ['present' => false]);

        // Esto ya se hace en el middleware de CheckFileMetadata
        
        try {
            $request->validate([
                'evidence_id' => 'required|exists:evidences,evidence_id',
                'files.*' => 'required|file|max:51200' // Máximo 50MB por archivo
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación:', $e->errors());
            throw $e;
        }

        $evidence = \App\Models\Evidence::where('evidence_id', $request->evidence_id)->first();
        Log::info('Evidence encontrada:', ['evidence_id' => $evidence ? $evidence->evidence_id : 'no encontrada']);

        if (!$evidence) {
            return response()->json(['message' => 'Evidencia no encontrada'], 404);
        }

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
                try {
                    // Verificar que el archivo es válido
                    if (!$file->isValid()) {
                        Log::error('Archivo inválido:', [
                            'error' => $file->getErrorMessage(),
                            'name' => $file->getClientOriginalName()
                        ]);
                        continue;
                    }

                    // Generar un ID único por archivo
                    do {
                        $randomId = rand(1, 1000000);
                    } while (File::where('file_id', $randomId)->exists());

                    // Preparar nuevo nombre y path
                    $extension = $file->getClientOriginalExtension();
                    $newName = $standard_id . '_' . $evidence_id . '_' . $group_id . '-' . $randomId . '.' . $extension;
                    $path_name = 'uploads/' . $evidence_id;

                    // Asegurarse de que el directorio existe
                    if (!Storage::disk('public')->exists($path_name)) {
                        Storage::disk('public')->makeDirectory($path_name);
                    }

                    // Guardar archivo
                    $path = $file->storeAs($path_name, $newName, 'public');

                    if (!$path) {
                        Log::error('Error al guardar archivo:', [
                            'name' => $file->getClientOriginalName()
                        ]);
                        continue;
                    }

                    // Crear registro
                    $newFile = File::create([
                        'file_id' => $randomId,
                        'file_url' => $path,
                        'upload_date' => now(),
                        'evidence_id' => $evidence_id,
                        'file_name' => $file->getClientOriginalName()
                    ]);

                    $savedFiles[] = $newFile;
                } catch (\Exception $e) {
                    Log::error('Error al procesar archivo:', [
                        'error' => $e->getMessage(),
                        'name' => $file->getClientOriginalName()
                    ]);
                }
            }

            if (empty($savedFiles)) {
                return response()->json(['message' => 'No se pudo subir ningún archivo'], 422);
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