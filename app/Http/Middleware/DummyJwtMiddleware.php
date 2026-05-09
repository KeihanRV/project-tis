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
            // Memparsing token dan mendapatkan payload-nya
            $payload = JWTAuth::parseToken()->getPayload();

            // Menyisipkan payload ke dalam request agar bisa diakses di controller
            $request->merge(['jwt_payload' => $payload]);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Token invalid or expired',
                'error' => $e->getMessage(),
            ], 401);
        }

        return $next($request);
    }
}
