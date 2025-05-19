<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Cv;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class CvController extends Controller
{
    public function index(Request $request) {
        $user = User::where('user_rpe', $request->user_rpe)->first();
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        $cv = Cv::where('cv_id', $user->cv_id)->first();
        return response()->json($cv, 200);
    }

    public function show($id) {
        $cv = Cv::find($id);
        if (!$cv) return response()->json(['message' => 'No encontrado'], 404);
        return response()->json($cv, 200);
    }

    public function store(Request $request) {
        $cv = Cv::create($request->all());
        return response()->json($cv, 201);
    }

    public function update(Request $request, $id) {
        $cv = Cv::find($id);
        if (!$cv) return response()->json(['message' => 'No encontrado'], 404);
        $cv->update($request->all());
        return response()->json($cv, 200);
    }

    public function destroy($id) {
        $cv = Cv::find($id);
        if (!$cv) return response()->json(['message' => 'No encontrado'], 404);
        $cv->delete();
        return response()->json(['message' => 'Eliminado correctamente'], 200);
    }

    public function generateWord($user_rpe)
    {
        $user = User::where('user_rpe', $user_rpe)->first();
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $cv = Cv::where('cv_id', $user->cv_id)->first();
        if (!$cv) {
            return response()->json(['message' => 'CV no encontrado'], 404);
        }

        // Cargar plantilla
        $templatePath = storage_path('app/templates/Cedula_CV.docx');
        if (!file_exists($templatePath)) {
            return response()->json(['message' => 'Plantilla no encontrada'], 500);
        }

        $template = new TemplateProcessor($templatePath);


        // Insertar valores
        $nombres = explode(' ', $cv->professor_name);
        $template->setValue('paterno', $nombres[0] ?? ''); 
        $template->setValue('materno', $nombres[1] ?? '');
        $template->setValue('nombre', implode(' ', array_slice($nombres, 2)) ?? '');
        $template->setValue('edad', $cv->age);
        $template->setValue('fecha', $cv->birth_date);

        // Guardar el documento generado temporalmente
        $filename = 'CV_' . $cv->cv_id . '.docx';
        $outputPath = storage_path('app/public/' . $filename);
        $template->saveAs($outputPath);

        // Descargar el archivo
        return response()->download($outputPath)->deleteFileAfterSend(true);
    }
}
