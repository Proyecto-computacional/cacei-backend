<?php

namespace App\Http\Controllers;

use App\Models\TeacherTraining;
use Illuminate\Http\Request;

class TeacherTrainingController extends Controller
{
    public function index($cv_id)
    {
        $teacherTrainings = TeacherTraining::where('cv_id', $cv_id)->get();
        return response()->json($teacherTrainings);
    }

    public function store(Request $request, $cv_id)
    {
        $validated=$request->validate([
            'title_certification' => 'required|string|max:50',
            'obtained_year' => 'required|integer',
            'institution_country' => 'required|string|max:50',
            'hours' => 'required|integer',
        ]);

        $teacherTraining = TeacherTraining::updateOrCreate(
            [
                'cv_id' => $cv_id,
                'title_certification' => $validated['title_certification']
            ],
            $validated
        );

        return response()->json($teacherTraining, 201);
    }

    public function show($cv_id, $teacher_training_id)
    {
        $teacherTraining = TeacherTraining::where('cv_id', $cv_id)->findOrFail($teacher_training_id);
        return response()->json($teacherTraining);
    }

    public function update(Request $request, $cv_id, $teacher_training_id)
    {
        $teacherTraining = TeacherTraining::where('cv_id', $cv_id)->findOrFail($teacher_training_id);
        $teacherTraining->update($request->all());

        return response()->json($teacherTraining);
    }

    public function destroy($cv_id, $teacher_training_id)
    {
        $teacherTraining = TeacherTraining::where('cv_id', $cv_id)->findOrFail($teacher_training_id);
        $teacherTraining->delete();

        return response()->json(null, 204);
    }
}
