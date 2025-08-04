<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = $request->user(); // usuario autenticado

        if (!$user || !$user->hasPermission($permission)) {
            return response()->json(['message' => 'No tienes permiso para realizar está acción.'], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
