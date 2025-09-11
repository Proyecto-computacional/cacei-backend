<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class CheckFileMetadata
{
    public function handle(Request $request, Closure $next): Response
    {
        // Accede a la base de datos para obtener los archivos de una evidencia
        $evidenceId = $request->route('evidence');
        $files = DB::table('files')->where('evidence_id', $evidenceId)->get();

        // Suma en una variable el peso de todos los archivos
        $totalSize = $files->sum('size');
        $remainingSpace = 51200 - $totalSize;

        // Revisa que los tipos de archivo y el espacio restante sean correctos
        $request->validate([
            'files' => 'array',
            'files.*' => 'file|mimes:rar,zip|max:' . $remainingSpace,
        ], [
            'files.*.max' => 'Cada archivo no debe de exceder 50 MB.',
            'files.*.mimes' => 'Los archivos deben ser RAR o ZIP.'
        ]);

        return $next($request);
    }
}
