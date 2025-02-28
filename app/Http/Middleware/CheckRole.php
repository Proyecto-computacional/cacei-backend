<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Por ahora, simulamos un usuario autenticado con un rol
        //$userRole = $request->user() ? $request->user()->role : 'guest'; // Esto lo ajustarás con la API
        $userRole = 'admin'; // Esto lo ajustarás con la API

        // Verificar si el rol del usuario está en la lista de roles permitidos
        if (!in_array($userRole, $roles)) {
            return response()->json(['error' => 'No tienes permisos para acceder a este recurso'], 403);
        }

        return $next($request);
    }
}