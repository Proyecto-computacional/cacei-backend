<?php

namespace App\Http\Controllers;

use App\Models\ContributionToPe;
use Illuminate\Http\Request;

class ContributionToPeController extends Controller
{
    public function index($cv_id)
    {
        $contributions = ContributionToPe::where('cv_id', $cv_id)->get();
        return response()->json($contributions);
    }

    public function store(Request $request, $cv_id)
    {
        $request->validate([
            'description' => 'required|string|max:255',
        ]);

        $contribution = ContributionToPe::create([
            'cv_id' => $cv_id,
            'description' => $request->description,
        ]);

        return response()->json($contribution, 201);
    }

    public function show($cv_id, $contribution_id)
    {
        $contribution = ContributionToPe::where('cv_id', $cv_id)->findOrFail($contribution_id);
        return response()->json($contribution);
    }

    public function update(Request $request, $cv_id, $contribution_id)
    {
        $contribution = ContributionToPe::where('cv_id', $cv_id)->findOrFail($contribution_id);
        $contribution->update($request->all());

        return response()->json($contribution);
    }

    public function destroy($cv_id, $contribution_id)
    {
        $contribution = ContributionToPe::where('cv_id', $cv_id)->findOrFail($contribution_id);
        $contribution->delete();

        return response()->json(null, 204);
    }
}
