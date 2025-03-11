<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;

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

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//1.Inicio de sesion
Route::middleware(['role:guest'])->get('/admin', function () {
    Route::post('/login', [AuthController::class, 'login']);
});*/

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
])->get('/info_personal', function () {
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



// 10. Notificaciones
Route::middleware(['auth:sanctum'])->group(function () {
    // Listar notificaciones
    Route::get('/Notificaciones', [NotificationController::class, 'index']);

    // Marcar/Desmarcar favorito
    Route::put('/Notificaciones/{id}/favorite', [NotificationController::class, 'toggleFavorite']);

    // Marcar/Desmarcar fijada
    Route::put('/Notificaciones/{id}/pinned', [NotificationController::class, 'togglePinned']);

    // Eliminar notificación
    Route::delete('/Notificaciones/{id}', [NotificationController::class, 'destroy']);
    Route::post('Notificaciones/Enviar', [NotificationController::class, 'sendNotification']);
});



Route::get('/mensaje', function () {
    return response()->json(['mensaje' => '¡Hola desde Laravel!']);
});

