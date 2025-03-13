<?php

namespace App\Http\Controllers;

use App\Models\AcademicManagement;
use Illuminate\Http\Request;

class AcademicManagementController extends Controller
{
    public function index($cv_id)
    {
        $academicManagements = AcademicManagement::where('cv_id', $cv_id)->get();
        return response()->json($academicManagements);
    }

    public function store(Request $request, $cv_id)
    {
        $request->validate([
            'job_position' => 'required|string|max:100',
            'institution' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
        ]);

        $academicManagement = AcademicManagement::create([
            'cv_id' => $cv_id,
            'job_position' => $request->job_position,
            'institution' => $request->institution,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return response()->json($academicManagement, 201);
    }

    public function show($cv_id, $academic_management_id)
    {
        $academicManagement = AcademicManagement::where('cv_id', $cv_id)->findOrFail($academic_management_id);
        return response()->json($academicManagement);
    }

    public function update(Request $request, $cv_id, $academic_management_id)
    {
        $academicManagement = AcademicManagement::where('cv_id', $cv_id)->findOrFail($academic_management_id);
        $academicManagement->update($request->all());

        return response()->json($academicManagement);
    }

    public function destroy($cv_id, $academic_management_id)
    {
        $academicManagement = AcademicManagement::where('cv_id', $cv_id)->findOrFail($academic_management_id);
        $academicManagement->delete();

        return response()->json(null, 204);
    }
}
