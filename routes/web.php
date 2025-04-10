<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PruebaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RevisionEvidenciasController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/api.php';
Route::get('/prueba', [PruebaController::class, 'index'])->name('prueba_valida_usuario_v1');
Route::post('/prueba', [PruebaController::class, 'index'])->name('prueba_valida_usuario_v1');
Route::post('/guardar', [PruebaController::class, 'guardar'])->name('guardar');
Route::get('/index', [UserController::class, 'index'])->name('usuarios.index');
Route::post('/usuarios/actualizar-rol', [UserController::class, 'actualizarRol']);
Route::get('/Notificaciones', [NotificationController::class, 'index']);
Route::post('/Notificaciones/Enviar', [NotificationController::class, 'sendNotification']);
Route::post('/RevisionEvidencias/aprobar', [RevisionEvidenciasController::class, 'aprobarEvidencia']);
Route::post('/RevisionEvidencias/desaprobar', [RevisionEvidenciasController::class, 'desaprobarEvidencia']);
Route::post('/RevisionEvidencias/pendiente', [RevisionEvidenciasController::class, 'marcarPendiente']);
