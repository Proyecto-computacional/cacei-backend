<?php

namespace App\Http\Middleware;

use App\Models\Cv;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class cvBelongsUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $cvId = $request->route('cv_id');
        $user = auth()->user();

        if ($user->cv_id != $cvId && $user->user_role !== 'ADMINISTRADOR') {
          abort(403, 'No tienes permiso para modificar este CV');
}


        return $next($request);
    }
}
