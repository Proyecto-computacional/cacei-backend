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
            'evidence_id' => 'required|exists:evidences,evidence_id',
            'file' => 'required|file|mimes:pdf,doc,docx,png,jpg,jpeg|max:2048',
            'justification' => 'nullable|string'
        ]);
        return $next($request);
    }
}
