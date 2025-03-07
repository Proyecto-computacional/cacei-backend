<?php

namespace App\Http\Controllers;

use Brick\Math\BigInteger;
use Illuminate\Http\Request;
use App\Models\Cv;

class CvController extends Controller
{
    public function index()
    {
        $cv = Cv::with(['AcademicManagement'])->get();
        return response()->json($cv, 200);
    }

    public function show($id)
    {
        $cv = Cv::with(['AcademicManagement'])->findOrFail($id);
        if (!$cv) {
            return response()->json(['message' => 'No se encontró el CV del usuario :('] . 404);
        }

        return response()->json($cv, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'professor_number' => 'required|BigInteger',
            'update_date' => 'required|Date',
            'professor_name' => 'required|String',
            'age' => 'required|int',
            'birth_date' => 'required|Date',
            'actual_position' => 'required|String',
            'duration' => 'required|int'
        ]);

        $cv = Cv::create(
            [
                'professor_number' => $request->professor_number,
                'update_date' => $request->update_date,
                'professor_name' => $request->professor_name,
                'age' => $request->age,
                'birth_date' => $request->birth_date,
                'actual_position' => $request->actual_position,
                'duration' => $request->duration
            ]
        );

        return response()->json($cv, 201);
    }

    public function update(Request $request, $id)
    {
        $cv = Cv::find($id);

        if (!$cv) {
            return response()->json(['message' => 'No se encontró el CV del usuario :('] . 404);
        }

        $request->validate([
            'professor_number' => 'required|BigInteger',
            'update_date' => 'required|Date',
            'professor_name' => 'required|String',
            'age' => 'required|int',
            'birth_date' => 'required|Date',
            'actual_position' => 'required|String',
            'duration' => 'required|int'
        ]);

        $cv = Cv::updateOrCreate(
            [
                'professor_number' => $request->professor_number ?? $cv->professor_number,
                'update_date' => $request->update_date ?? $cv->update_date,
                'professor_name' => $request->professor_name ?? $cv->professor_name,
                'age' => $request->age ?? $cv->age,
                'birth_date' => $request->birth_date ?? $cv->birth_date,
                'actual_position' => $request->actual_position ?? $cv->actual_position,
                'duration' => $request->duration ?? $cv->duration
            ]
        );

        return response()->json($cv, 200);
    }

    public function destroy($id)
    {
        $cv = Cv::find($id);

        if (!$cv) {
            return response()->json(['message' => 'No se encontró el CV del usuario :('] . 404);
        }

        $cv->delete();
        return response()->json(['message' => 'Se eliminó el CV del usuario (Nota: esto es de prueba, eliminar después'] . 200);
    }
}
