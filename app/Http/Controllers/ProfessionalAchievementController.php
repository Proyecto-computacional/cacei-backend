<?php

namespace App\Http\Controllers;

use App\Models\ProfessionalAchievement;
use Illuminate\Http\Request;

class ProfessionalAchievementController extends Controller
{
    public function index($cv_id)
    {
        $professionalAchievements = ProfessionalAchievement::where('cv_id', $cv_id)->get();
        return response()->json($professionalAchievements);
    }

    public function store(Request $request, $cv_id)
    {
        $request->validate([
            'description' => 'required|string|max:255',
        ]);

        $professionalAchievement = ProfessionalAchievement::create([
            'cv_id' => $cv_id,
            'description' => $request->description,
        ]);

        return response()->json($professionalAchievement, 201);
    }

    public function show($cv_id, $achievement_id)
    {
        $professionalAchievement = ProfessionalAchievement::where('cv_id', $cv_id)->findOrFail($achievement_id);
        return response()->json($professionalAchievement);
    }

    public function update(Request $request, $cv_id, $achievement_id)
    {
        $professionalAchievement = ProfessionalAchievement::where('cv_id', $cv_id)->findOrFail($achievement_id);
        $professionalAchievement->update($request->all());

        return response()->json($professionalAchievement);
    }

    public function destroy($cv_id, $achievement_id)
    {
        $professionalAchievement = ProfessionalAchievement::where('cv_id', $cv_id)->findOrFail($achievement_id);
        $professionalAchievement->delete();

        return response()->json(null, 204);
    }
}
