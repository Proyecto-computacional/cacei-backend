<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Cv;

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
}
