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
            'files' => 'required|array',
            'files.*' => 'file|mimes:pdf,rar,zip,doc,docx,png,jpg,jpeg,xlsx|max:51200', //imagenes y docx?
        ], [
            'files.required' => 'Debes subir al menos un archivo.',
            'files.*.max' => 'Cada archivo no debe de exceder 50 MB.',
            'files.*.mimes' => 'Los archivos deben ser PDF, DOC, DOCX, PNG, JPG, JPEG o XLSX.'
        ]);

        if ($request->hasFile('files')) {
            return $next($request);
        }

        return back()->withErrors(['files' => 'Los archivos no son válidos. Revisa las extensiones o el peso.']);
    }
}
