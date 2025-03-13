<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cv;

class CvController extends Controller
{
    public function index() {
        return response()->json(Cv::all(), 200);
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
