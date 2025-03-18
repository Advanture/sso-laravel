<?php

namespace App\Http\Controllers;

use App\Services\TokenService;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    public function __construct(private readonly TokenService $tokenService)
    {}

    public function refresh(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required|string',
        ]);

        $refreshToken = $request->refresh_token;
        $userId = $this->tokenService->validateRefreshToken($refreshToken);

        if (!$userId) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid refresh token',
            ], 401);
        }

        $this->tokenService->deleteRefreshToken($refreshToken);

        $tokens = $this->tokenService->generateTokens($userId);

        return response()->json([
            'success' => true,
            'tokens' => $tokens
        ]);
    }
}
