<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Http\Models\AccreditationProcess;

class DashboardController extends Controller
{
    public function showProcesses(Request $request)
    {
        $user = $request->user();
        switch (true) {
            case $user->hasRole('ADMINISTRADOR', 'DIRECTIVO'): // Todo proceso de acreditación activo (completo)
                return response()->json([
                    'name' => $user->name,
                    'email' => $user->email,
                    'allUsers' => \App\Models\User::all(),
                ]);
            case $user->hasRole('JEFE DE AREA'): // Procesos de acreditación activos de su área (completo)
                return response()->json([
                    'name' => $user->name,
                    'email' => $user->email,
                    'courses' => $user->courses,
                ]);
            case $user->hasRole('COORDINADOR DE CARRERA'): // Proceso de acreditación activo de su programa (completo), y participantes (parcial)
                return response()->json([
                    'name' => $user->name,
                    'email' => $user->email,
                    'courses' => $user->courses,
                ]);
            case $user->hasRole('PROFESOR, PROFESOR RESPONSABLE, PERSONAL DE APOYO'): // Proceso de acreditación en que participa (parcial)
                return response()->json([
                    'name' => $user->name,
                    'email' => $user->email,
                    'courses' => $user->courses,
                ]);
        }
    }

    public function displayProcess(Request $request)
    {
        $user = $request->user();
        switch (true) {
            case $user->hasRole('ADMINISTRADOR', 'DIRECTIVO', 'JEFE DE AREA'): // Progreso general del proceso (estatus de todos los criterios)
                return response()->json([
                    'name' => $user->name,
                    'email' => $user->email,
                    'allUsers' => \App\Models\User::all(),
                ]);
            case $user->hasRole('COORDINADOR DE CARRERA'): // Depende si es su carrera o solo participante
                return response()->json([
                    'name' => $user->name,
                    'email' => $user->email,
                    'courses' => $user->courses,
                ]);
            case $user->hasRole('PROFESOR, PROFESOR RESPONSABLE, PERSONAL DE APOYO'): // Progreso individual del proceso (estatus de criterios asignados)
                return response()->json([
                    'name' => $user->name,
                    'email' => $user->email,
                    'courses' => $user->courses,
                ]);
        }
    }
}
