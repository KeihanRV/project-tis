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
        // Mengambil role dari request yang disimpan oleh JWT middleware
        $userRole = $request->user_role ?? null;

        if (! $userRole) {
            return response()->json([
                'message' => 'Missing user role information',
            ], 401);
        }

        // Pengecekan apakah role user ada di dalam daftar role yang diizinkan
        if (! in_array($userRole, $roles)) {
            return response()->json([
                'message' => 'Access denied. You do not have the required role.',
            ], 403);
        }

        return $next($request);
    }
}
