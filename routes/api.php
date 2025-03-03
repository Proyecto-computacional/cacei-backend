<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/mensaje', function () {
    return response()->json(['mensaje' => '¡Hola desde Laravel!']);
});

Route::get('/linked_processes_example', function () {
    return response()->json([
        ['frame_id' => 2025, 'process_id' => 1, 'career_id' => 1],
        ['frame_id' => 2025, 'process_id' => 2, 'career_id' => 3]
    ]);
});

Route::get('/linked_processes', function (Request $request) {
    $user = $request->user();

    $processes = $user->processes;

    return response()->json([
        'user_id' => $user->id,
        'processes' => $processes
    ]);
});

Route::get('/test_check_user_example', function () {


    return response()->json([
        'message' => 'Checando procesos vinculados...',
        'user_data' => "Usuario tal",
        route('linked_processes')
    ]);
});

Route::post('/test_check_user', function (Request $request) {
    // Get the authenticated user
    $user = $request->user();

    // Retrieve the processes associated with the user
    $processes = $user->processes;

    // Return a response with the user data and processes
    return response()->json([
        'message' => 'Checando procesos vinculados...',
        'user_data' => $user->id,
        'processes' => $processes
    ]);
});