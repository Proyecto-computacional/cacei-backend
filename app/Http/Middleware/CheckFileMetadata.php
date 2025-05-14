<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFileMetadata
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request->validate([
            'files' => 'array',
            'files.*' => 'file|mimes:pdf,rar,zip,doc,docx,xlsx,xls,csv|max:51200', //  docx?
        ], [
            'files.*.max' => 'Cada archivo no debe de exceder 50 MB.',
            'files.*.mimes' => 'Los archivos deben ser PDF, DOC, DOCX, PNG, JPG, JPEG o XLSX.'
        ]);

        return $next($request);
    }
}
