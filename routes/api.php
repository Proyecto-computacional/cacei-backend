<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProcessController;

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


Route::get('/linked_processes', [ProcessController::class, 'linkedProcesses']);
Route::get('/test_check_user_example', [ProcessController::class, 'checkUser']);