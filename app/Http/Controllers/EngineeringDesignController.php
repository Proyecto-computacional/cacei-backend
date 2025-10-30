<?php

namespace App\Http\Controllers;

use App\Models\EngineeringDesign;
use Illuminate\Http\Request;

class EngineeringDesignController extends Controller
{
    public function index($cv_id)
    {
        $engineeringDesigns = EngineeringDesign::where('cv_id', $cv_id)->get();
        return response()->json($engineeringDesigns);
    }

    public function store(Request $request, $cv_id)
    {
        $validated = $request->validate([
            'institution' => 'required|string|max:70',
            'period' => 'required|integer',
            'level_experience' => 'required|string|max:20',
        ]);

        $engineeringDesign = EngineeringDesign::updateOrCreate(
            [
                'cv_id' => $cv_id,
                'engineering_design_id' => $request->input('engineering_design_id')
            ],
            $validated
        );

        return response()->json($engineeringDesign, 201);
    }

    public function show($cv_id, $engineering_design_id)
    {
        $engineeringDesign = EngineeringDesign::where('cv_id', $cv_id)->findOrFail($engineering_design_id);
        return response()->json($engineeringDesign);
    }

    public function update(Request $request, $cv_id, $engineering_design_id)
    {
        $engineeringDesign = EngineeringDesign::where('cv_id', $cv_id)->findOrFail($engineering_design_id);
        $engineeringDesign->update($request->all());

        return response()->json($engineeringDesign);
    }

    public function destroy($cv_id, $engineering_design_id)
    {
        $engineeringDesign = EngineeringDesign::where('cv_id', $cv_id)->findOrFail($engineering_design_id);
        $engineeringDesign->delete();

        return response()->json(null, 204);
    }
}
