<?php

namespace App\Http\Controllers;

use App\Models\Award;
use Illuminate\Http\Request;

class AwardController extends Controller
{
    public function index($cv_id)
    {
        $awards = Award::where('cv_id', $cv_id)->get();
        return response()->json($awards);
    }

    public function store(Request $request, $cv_id)
    {
        $request->validate([
            'description' => 'required|string|max:255',
        ]);

        $award = Award::create([
            'cv_id' => $cv_id,
            'description' => $request->description,
        ]);

        return response()->json($award, 201);
    }

    public function show($cv_id, $award_id)
    {
        $award = Award::where('cv_id', $cv_id)->findOrFail($award_id);
        return response()->json($award);
    }

    public function update(Request $request, $cv_id, $award_id)
    {
        $award = Award::where('cv_id', $cv_id)->findOrFail($award_id);
        $award->update($request->all());

        return response()->json($award);
    }

    public function destroy($cv_id, $award_id)
    {
        $award = Award::where('cv_id', $cv_id)->findOrFail($award_id);
        $award->delete();

        return response()->json(null, 204);
    }
}
