<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccreditationProcessController;
use App\Http\Controllers\CvController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\TeacherTrainingController;
use App\Http\Controllers\DisciplinaryUpdateController;
use App\Http\Controllers\AcademicManagementController;
use App\Http\Controllers\AcademicProductController;
use App\Http\Controllers\LaboralExperienceController;
use App\Http\Controllers\EngineeringDesignController;
use App\Http\Controllers\ProfessionalAchievementController;
use App\Http\Controllers\ParticipationController;
use App\Http\Controllers\AwardController;
use App\Http\Controllers\ContributionToPEController;

//1. Login
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->post('/logout', [AuthController::class, 'logout']);

//2. Menu prinicipal
Route::middleware('auth:sanctum')->get('/menuPrinicipal', function (Request $request) {
    return response()->json(['message' => 'Bienvenido al menu principal']);
});

//3. Confitguracion personal
Route::middleware([
    'auth:sanctum',
    'role:admin, jefe, coordinador, profesor
profesor_encargado, apoyo, directivo'
])->get('/personalInfo', function () {
    return response()->json(['message' => 'Informacion personal']);
});

// 3.a cv
Route::middleware([
    'auth:sanctum',
    'role:admin, jefe, coordinador, profesor
profesor_encargado'
])->get('/cv', function () {
    return response()->json(['message' => 'Cv de profesor']);
});

//4. Subir evidencia
Route::middleware([
    'auth:sanctum',
    'role:admin, jefe, coordinador, profesor
profesor_encargado, departamento, apoyo'
])->get('/cv', function () {
    return response()->json(['message' => 'Subir evidencia']);
});

//5. Revisar evidencias
Route::middleware([
    'auth:sanctum',
    'role:admin, jefe, coordinador
profesor_encargado'
])->get('/RevisarEvidencia', function () {
    return response()->json(['message' => 'Revisar evidencia']);
});

// 7.Dashboard
Route::middleware(['auth:sanctum'])->get('/Dashboard', function () {
    return response()->json(['message' => 'Dashboard']);
});

// 8.Gestion Evidencias
Route::middleware([
    'auth:sanctum',
    'role:admin, jefe, coordinador
profesor_encargado'
])->get('/GestionEvidencias', function () {
    return response()->json(['message' => 'Gestionar evidencias']);
});

//Exclusivos de administrador 
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    //6. Administracion de usuarios
    Route::get('/GestionUsuarios', function () {
        return response()->json(['message' => 'Administrar usuarios']);
    });
    //9. Gestion de formato
    Route::get('/GestionFormato', function () {
        return response()->json(['message' => 'Gestion de formatos']);
    });
});

// 10.Notificaciones
Route::middleware(['auth:sanctum'])->get('/Notificaciones', function () {
    return response()->json(['message' => 'Notificaciones']);
});

// 11. Procesos relacionados a un usuario
Route::middleware(
    'auth:sanctum'
)->get('/ProcesosUsuario', 
        [AccreditationProcessController::class, 'getProcessesByUser'
]);

// 12.CV de un usuario
Route::apiResource('cvs', CvController::class);

// 13. Información adicional de un CV
Route::prefix('additionalInfo/{cv_id}')->group(function () {

    // Rutas para educaciones
    Route::resource('educations', EducationController::class);
    
    // Rutas para formaciones docentes
    Route::resource('teacher-trainings', TeacherTrainingController::class);

    // Rutas para actualizaciones disciplinarias
    Route::resource('disciplinary-updates', DisciplinaryUpdateController::class);

    // Rutas para gestiones académicas
    Route::resource('academic-managements', AcademicManagementController::class);

    // Rutas para productos académicos
    Route::resource('academic-products', AcademicProductController::class);

    // Rutas para experiencias laborales
    Route::resource('laboral-experiences', LaboralExperienceController::class);

    // Rutas para diseños de ingeniería
    Route::resource('engineering-designs', EngineeringDesignController::class);

    // Rutas para logros profesionales
    Route::resource('professional-achievements', ProfessionalAchievementController::class);

    // Rutas para participaciones
    Route::resource('participations', ParticipationController::class);

    // Rutas para premios
    Route::resource('awards', AwardController::class);

    // Rutas para contribuciones al PE
    Route::resource('contributions-to-pe', ContributionToPEController::class);
});

Route::get('/mensaje', function () {
    return response()->json(['mensaje' => '¡Hola desde Laravel!']);
});

