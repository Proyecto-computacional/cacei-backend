<?php

namespace App\Http\Controllers;

use App\Models\Education;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    public function index($cv_id)
    {
        $educations = Education::where('cv_id', $cv_id)->get();
        return response()->json($educations);
    }

    public function store(Request $request, $cv_id)
    {
        $validated = $request->validate([
            'institution' => 'required|string|max:30',
            'degree_obtained' => 'required|string|max:1',
            'obtained_year' => 'required|integer',
            'professional_license' => 'nullable|string|max:8',
            'degree_name' => 'required|string|max:50',
        ]);
        
        $education = Education::updateOrCreate(
            [
                'cv_id' => $cv_id,
                'degree_name' => $validated['degree_name']
            ],
            $validated
        );

        return response()->json($education, 201);
    }

    public function show($cv_id, $education_id)
    {
        $education = Education::where('cv_id', $cv_id)->findOrFail($education_id);
        return response()->json($education);
    }

    public function update(Request $request, $cv_id, $education_id)
    {
        $education = Education::where('cv_id', $cv_id)->findOrFail($education_id);
        $education->update($request->all());

        return response()->json($education);
    }

    public function destroy($cv_id, $education_id)
    {
        $education = Education::where('cv_id', $cv_id)->findOrFail($education_id);
        $education->delete();

        return response()->json(null, 204);
    }
}
