<?php

namespace App\Http\Controllers;

use App\Models\Participation;
use Illuminate\Http\Request;

class ParticipationController extends Controller
{
    public function index($cv_id)
    {
        $participations = Participation::where('cv_id', $cv_id)->get();
        return response()->json($participations);
    }

    public function store(Request $request, $cv_id)
    {
        $request->validate([
            'institution' => 'required|string|max:30',
            'period' => 'required|integer',
            'level_participation' => 'required|string|max:20',
        ]);

        $participation = Participation::create([
            'cv_id' => $cv_id,
            'institution' => $request->institution,
            'period' => $request->period,
            'level_participation' => $request->level_participation,
        ]);

        return response()->json($participation, 201);
    }

    public function show($cv_id, $participation_id)
    {
        $participation = Participation::where('cv_id', $cv_id)->findOrFail($participation_id);
        return response()->json($participation);
    }

    public function update(Request $request, $cv_id, $participation_id)
    {
        $participation = Participation::where('cv_id', $cv_id)->findOrFail($participation_id);
        $participation->update($request->all());

        return response()->json($participation);
    }

    public function destroy($cv_id, $participation_id)
    {
        $participation = Participation::where('cv_id', $cv_id)->findOrFail($participation_id);
        $participation->delete();

        return response()->json(null, 204);
    }
}
