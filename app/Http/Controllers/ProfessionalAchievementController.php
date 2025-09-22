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

        // Buscar por descripción (evita duplicados)
        $achievement = ProfessionalAchievement::firstOrNew([
            'cv_id' => $cv_id,
            'description' => $validated['description']
        ]);

        if (!$achievement->exists) {
            $achievement->achievement_id = rand(1000, 999999);
            $achievement->save();
        }

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
