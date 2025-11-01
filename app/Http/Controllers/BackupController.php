<?php

namespace App\Http\Controllers;
use App\Console\Commands\BackupApprovedEvidence;
use Illuminate\Support\Facades\Artisan;
class BackupController extends Controller
{
    public function backup(){
        // Ejecuta el comando por su nombre
        Artisan::call('backup:approved-evidence');

        // Puedes obtener la salida del comando
        $output = Artisan::output();

        return response()->json([
            'message' => 'Backup ejecutado correctamente',
            'output' => $output
        ]);
    }
}