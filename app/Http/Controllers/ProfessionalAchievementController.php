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
        $validated = $request->validate([
            'description' => 'required|string|max:500',
        ]);

        // Usar updateOrCreate para evitar duplicados
        $achievement = ProfessionalAchievement::updateOrCreate(
            [
                'cv_id' => $cv_id,
                'professional_achievement_id' => $request->input('professional_achievement_id')
            ],
            $validated
        );

        return response()->json($achievement, 201);
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
