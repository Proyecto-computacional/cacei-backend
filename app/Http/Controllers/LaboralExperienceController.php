<?php

namespace App\Http\Controllers;

use App\Models\LaboralExperience;
use Illuminate\Http\Request;

class LaboralExperienceController extends Controller
{
    public function index($cv_id)
    {
        $laboralExperiences = LaboralExperience::where('cv_id', $cv_id)->get();
        return response()->json($laboralExperiences);
    }

    public function store(Request $request, $cv_id)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:50',
            'position' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
        ]);

        $laboralExperience = LaboralExperience::updateOrCreate(
            [
                'cv_id' => $cv_id,
                'position' => $validated['position']
            ],
            $validated
        );

        return response()->json($laboralExperience, 201);
    }

    public function show($cv_id, $laboral_experience_id)
    {
        $laboralExperience = LaboralExperience::where('cv_id', $cv_id)->findOrFail($laboral_experience_id);
        return response()->json($laboralExperience);
    }

    public function update(Request $request, $cv_id, $laboral_experience_id)
    {
        $laboralExperience = LaboralExperience::where('cv_id', $cv_id)->findOrFail($laboral_experience_id);
        $laboralExperience->update($request->all());

        return response()->json($laboralExperience);
    }

    public function destroy($cv_id, $laboral_experience_id)
    {
        $laboralExperience = LaboralExperience::where('cv_id', $cv_id)->findOrFail($laboral_experience_id);
        $laboralExperience->delete();

        return response()->json(null, 204);
    }
}
