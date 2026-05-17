<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class DummyJwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Memvalidasi JWT token dan mendapatkan user dari token
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json([
                    'message' => 'User not found',
                ], 401);
            }

            // Menyimpan user dan role ke request untuk diakses di controller/middleware lainnya
            $request->merge([
                'authenticated_user' => $user,
                'user_role' => $user->role,
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Token invalid or expired',
                'error' => $e->getMessage(),
            ], 401);
        }

        return $next($request);
    }
}
