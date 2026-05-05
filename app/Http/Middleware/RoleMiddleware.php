<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Mengambil payload dari request (asumsi diatur oleh middleware JWT sebelumnya)
        $payload = $request->jwt_payload ?? null;

        if (! $payload) {
            return response()->json([
                'message' => 'Missing token payload',
            ], 401);
        }

        // Mengambil role dari payload
        $userRole = $payload->get('role');

        // Pengecekan apakah role user ada di dalam daftar role yang diizinkan
        if (! in_array($userRole, $roles)) {
            return response()->json([
                'message' => 'Access denied. You do not have the required role.',
            ], 403);
        }

        return $next($request);
    }
}
