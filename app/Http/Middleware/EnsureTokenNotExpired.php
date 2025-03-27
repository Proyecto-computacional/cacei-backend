<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class EnsureTokenNotExpired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->user()->currentAccessToken();

        if ($token && $token->expires_at && Carbon::now()->greaterThan($token->expires_at)) {
            $token->delete();
            return response()->json(['message' => 'La sesión ha expirado.'], 401);
        }

        return $next($request);
    }
}
