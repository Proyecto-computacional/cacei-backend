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
            'file' => 'required|file|mimes:pdf,doc,docx,png,jpg,jpeg|max:2048',
        ], [
            'file.max' => 'El archivo no debe de exceder 50 MB',
            'file.mimes' => 'El archivo debe ser PDF, DOC, DOCX, PNG, JPG, JPEG'
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->storeAs('uploads', 'public');

            return $next($request);
        }

        return back()->withErrors(['file' => 'El archivo no es valido, revisa la extensión o el peso']);
    }
}
