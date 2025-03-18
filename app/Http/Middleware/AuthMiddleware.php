<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\TokenService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    public function __construct(private TokenService $tokenService)
    {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token required'
            ], 401);
        }

        $userData = $this->tokenService->getAccessTokenPayload($token);

        if (!$userData) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token'
            ], 401);
        }

        $request->merge(['user' => User::find($userData['sub'])]);

        return $next($request);
    }
}
