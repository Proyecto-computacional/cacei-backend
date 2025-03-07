<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Academic_management;

class AcademicManagementController extends Controller
{
    public function store(Request $request)
    {


        $request->validate([
            'cv_id' => 'required|exists:cvs,id',
            'job_position' => 'nullable|string',
            'institution' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date'
        ]);

        $Academic_management = Academic_management::updateOrCreate([
            ['cv_id' => $request->cv_id],
            $request->only(['job_position', 'institution', 'start_date', 'end_date'])
        ]);

        return response()->json($Academic_management, 200);
    }
}
