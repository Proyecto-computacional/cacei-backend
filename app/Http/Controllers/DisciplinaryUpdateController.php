<?php

namespace App\Http\Controllers;

use App\Models\DisciplinaryUpdate;
use Illuminate\Http\Request;

class DisciplinaryUpdateController extends Controller
{
    public function index($cv_id)
    {
        $disciplinaryUpdates = DisciplinaryUpdate::where('cv_id', $cv_id)->get();
        return response()->json($disciplinaryUpdates);
    }

    public function store(Request $request, $cv_id)
    {
        $validated = $request->validate([
            'title_certification' => 'required|string|max:50',
            'year_certification' => 'required|integer',
            'institution_country' => 'required|string|max:50',
            'hours' => 'required|integer',
        ]);

        $disciplinaryUpdate = DisciplinaryUpdate::updateOrCreate(
            [
                'cv_id' => $cv_id,
                'title_certification' => $validated['title_certification']
            ],
            $validated
        );

        return response()->json($disciplinaryUpdate, 201);
    }

    public function show($cv_id, $disciplinary_update_id)
    {
        $disciplinaryUpdate = DisciplinaryUpdate::where('cv_id', $cv_id)->findOrFail($disciplinary_update_id);
        return response()->json($disciplinaryUpdate);
    }

    public function update(Request $request, $cv_id, $disciplinary_update_id)
    {
        $disciplinaryUpdate = DisciplinaryUpdate::where('cv_id', $cv_id)->findOrFail($disciplinary_update_id);
        $disciplinaryUpdate->update($request->all());

        return response()->json($disciplinaryUpdate);
    }

    public function destroy($cv_id, $disciplinary_update_id)
    {
        $disciplinaryUpdate = DisciplinaryUpdate::where('cv_id', $cv_id)->findOrFail($disciplinary_update_id);
        $disciplinaryUpdate->delete();

        return response()->json(null, 204);
    }
}

