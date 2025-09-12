<?php

namespace App\Http\Middleware;

use App\Models\Cv;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class cvBelongsUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $cv = Cv::find($request->route('cv_id'));

        if (!$cv || ($cv->user_rpe !== auth()->user()->user_rpe && auth()->user()->user_role !== 'ADMINISTRADOR')) {
    abort(403, 'No tienes permiso para modificar este CV');
}


        return $next($request);
    }
}
