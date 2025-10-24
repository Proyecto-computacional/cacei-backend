<?php

// app/Http/Middleware/RefreshTokenExpiration.php
namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;

class RefreshTokenExpiration
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $user = $request->user();

        if ($user && $request->bearerToken()) {
            $token = $user->currentAccessToken();

            if ($token) {

                // Si expiró, elimínalo
                if ($token->expires_at && $token->expires_at->isPast()) {
                    $token->delete();
                    return response()->json(['message' => 'Token expired'], 401);
                }

                // Renovar expiración sin modificar la respuesta original
                $token->forceFill([
                    'expires_at' => Carbon::now()->addMinutes(20),
                ])->save();
            }
        }

        return $response;
    }
}
