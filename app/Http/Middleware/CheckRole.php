<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = auth()->user();
        $userRole = $user->user_role;

        // Verificar si el rol del usuario está en la lista de roles permitidos
        if (!in_array($userRole, $roles)) {
            return response()->json(['error' => 'No tienes permisos para acceder a este recurso'], 403);
        }

        return $next($request);
    }
}