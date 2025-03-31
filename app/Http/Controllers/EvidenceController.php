<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evidence;

class EvidenceController extends Controller
{
    public function index(Request $request)
    {
        $evidences = Evidence::where("standard_id", $request->standard_id)->get();
        return response()->json($evidences);
    }

    public function show(Request $request)
    {
        $evidence = Evidence::find($request->file_id);
        if (!$evidence) {
            return response()->json(['message' => 'Evidencia no encontrada'], 404);
        }
        return response()->json($evidence);
    }

    public function store(Request $request)
    {
        $request->validate([
            '' => '',
        ]);
    }
}
