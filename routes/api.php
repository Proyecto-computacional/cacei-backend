<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
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
use App\Http\Controllers\RevisionEvidenciasController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ReviserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\StandardController;
use App\Http\Controllers\EvidenceController;
use App\Http\Controllers\FrameOfReferenceController;
use App\Http\Middleware\CorsMiddleware;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/*
Route::middleware('auth:sanctum')->get('/test_check_user_example', function (Request $request) {
    $user = $request->user();

    $linkedProcesses = [
        ['frame_id' => 2025, 'process_id' => 1, 'career_id' => 1],
        ['frame_id' => 2025, 'process_id' => 2, 'career_id' => 3]
    ];

    return response()->json([
        'message' => 'Checando procesos vinculados...',
        'user_data' => $user,
        'linked_processes' => $linkedProcesses
    ]);
});
*/


Route::get('/linked_processes', [ProcessController::class, 'linkedProcesses']);
Route::post('/test_check_user_example', [ProcessController::class, 'checkUser']);

//1. Login
Route::post('/login', [AuthController::class, 'login']);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    Route::post('/userToken', [AuthController::class, 'getUserToken']);
    Route::post('/allTokens', [AuthController::class, 'getAllTokens']);
});

//2. Menu prinicipal
Route::middleware('auth:sanctum')->get('/menuPrinicipal', function (Request $request) {
    return response()->json(['message' => 'Bienvenido al menu principal']);
});

//3. Confitguracion personal
Route::middleware([
    'auth:sanctum',
    'role:ADMINISTRADOR, JEFE DE AREA, COORDINADOR DE CARRERA, PROFESOR, PROFESOR RESPONSABLE, PERSONAL DE APOYO, DIRECTIVO'
])->get('/personalInfo', function () {
    return response()->json(['message' => 'Informacion personal']);
});

// 3.a cv
Route::middleware([
    'auth:sanctum',
    'role:ADMINISTRADOR, JEFE DE AREA, COORDINADOR DE CARRERA, PROFESOR, PROFESOR RESPONSABLE',
    'token.expired'
])->get('/cv', function () {
    return response()->json(['message' => 'Cv de profesor']);
});

//4. Subir evidencia
//REPETIDA CON CV
Route::middleware([
    'auth:sanctum',
    'role:ADMINISTRADOR, JEFE DE AREA, COORDINADOR DE CARRERA, PROFESOR, PROFESOR RESPONSABLE, DEPARTAMENTO UNIVERSITARIO, PERSONAL DE APOYO'
])->get('/cv', function () {
    return response()->json(['message' => 'Subir evidencia']);
});

//5. Revisar evidencias
Route::middleware([
    'auth:sanctum',
    'role:ADMINISTRADOR,JEFE DE AREA,COORDINADOR DE CARRERA,PROFESOR'
])->get('/ReviewEvidence', [EvidenceController::class, 'allEvidence']);

// 5.a. Revisar archivos
Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/files/{evidence_id}', [FileController::class, 'index']);
    Route::get('/file/{file_id}', [FileController::class, 'show']);
    Route::middleware(['file.correct'])->group(function () {
        Route::post('/file', [FileController::class, 'store']);
        Route::put('/file/{file_id}', [FileController::class, 'update']);
    });
    Route::delete('/file/{file_id}', [FileController::class, 'destroy']);
});

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/RevisionEvidencias/aprobar', [RevisionEvidenciasController::class, 'aprobarEvidencia']);

    Route::post('/RevisionEvidencias/desaprobar', [RevisionEvidenciasController::class, 'desaprobarEvidencia']);

    Route::post('/RevisionEvidencias/pendiente', [RevisionEvidenciasController::class, 'marcarPendiente']);

});

// 7.Dashboard
Route::middleware(['auth:sanctum'])->get('/Dashboard', function () {
    return response()->json(['message' => 'Dashboard']);
});

// 8.Gestion Evidencias
Route::middleware([
    'auth:sanctum',
    'role:ADMINISTRADOR, JEFE DE AREA, COORDINADOR DE CARRERA, PROFESOR RESPONSABLE'
])->get('/GestionEvidencias', function () {
    return response()->json(['message' => 'Gestionar evidencias']);
});

//Exclusivos de administrador 
Route::middleware(['auth:sanctum', 'role:ADMINISTRADOR'])->group(function () {
    //6. Administracion de usuarios
    Route::get('/usersadmin', [UserController::class, 'index'])->name('usuarios.index');
    Route::post('/usersadmin/actualizar-rol', [UserController::class, 'actualizarRol'])
        ->name('usuarios.actualizarRol');
    //9. Gestion de formato
    Route::get('/GestionFormato', function () {
        return response()->json(['message' => 'Gestion de formatos']);
    });
});
Route::get('/procesos/{id}/descargar-evidencias', [AccreditationProcessController::class, 'downloadProcess']);




// 10. Notificaciones
Route::middleware(['auth:sanctum'])->group(function () {
    // Listar notificaciones
    Route::get('/Notificaciones', [NotificationController::class, 'index']);
    Route::post('/Notificaciones', [NotificationController::class, 'index']);

    // Marcar/Desmarcar favorito
    Route::put('/Notificaciones/favorite', [NotificationController::class, 'toggleFavorite']);

    // Marcar/Desmarcar fijada
    Route::put('/Notificaciones/pinned', [NotificationController::class, 'togglePinned']);

    // Marcar/Desmarcar eliminada (seen)
    Route::put('/Notificaciones/deleted', [NotificationController::class, 'toggleDeleted']);

    // Eliminar notificación
    Route::delete('/Notificaciones', [NotificationController::class, 'destroy']);
    Route::post('/Notificaciones/Enviar', [NotificationController::class, 'sendNotification']);
});


// 11. Procesos relacionados a un usuario
Route::middleware(
    'auth:sanctum'
)->get(
        '/ProcesosUsuario',
        [
            AccreditationProcessController::class,
            'getProcessesByUser'
        ]
    );

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
//Rutas hechas en la rama de asignarTareas
Route::get('/revisers', [ReviserController::class, 'index']);
Route::post('/reviser', [ReviserController::class, 'store']);
Route::post('/categories', [CategoryController::class, 'getByFrame']);
Route::post('/category',[CategoryController::class, 'store']);
Route::put('/category-update',[CategoryController::class, 'update']);
Route::post('/sections', [SectionController::class, 'getByCategory']);
Route::post('/section', [SectionController::class, 'store']);
Route::put('/section-update', [SectionController::class, 'update']);
Route::post('/standards', [StandardController::class, 'getBySection']);
Route::post('/standard', [StandardController::class, 'store']);
Route::put('/standard-update', [StandardController::class, 'update']);
Route::post('/evidences', [EvidenceController::class, 'getByStandard']);
Route::get('/frames-of-references', [FrameOfReferenceController::class, 'index']);
Route::post('/frames-of-reference', [FrameOfReferenceController::class, 'store']);
Route::put('/frames-of-reference-update', [FrameOfReferenceController::class, 'update']);