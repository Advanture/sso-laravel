<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class TokenService
{
    private const ACCESS_TOKEN_EXPIRY = 3600 * 12;
    private const REFRESH_TOKEN_EXPIRY = 86400 * 30;

    private string $jwtSecret;

    public function __construct()
    {
        $this->jwtSecret = config('app.jwt_secret');
    }

    public function generateTokens(int $userId, array $roles = []): array
    {
        $accessToken = $this->generateAccessToken($userId, $roles);
        $refreshToken = $this->generateRefreshToken($userId);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => self::ACCESS_TOKEN_EXPIRY,
        ];
    }

    private function generateAccessToken(int $userId, array $roles = []): string
    {
        $payload = [
            'sub' => $userId,
            'roles' => $roles,
            'iat' => time(),
            'exp' => time() + self::ACCESS_TOKEN_EXPIRY,
        ];

        return JWT::encode($payload, $this->jwtSecret, 'HS256');
    }

    private function generateRefreshToken(int $userId): string
    {
        $refreshToken = Str::uuid()->toString();

        Redis::setex("refresh_tokens:$refreshToken", self::REFRESH_TOKEN_EXPIRY, $userId);

        return $refreshToken;
    }

    public function validateAccessToken(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getUserIdByRefreshToken(string $refreshToken): ?int
    {
        return Redis::get("refresh_tokens:$refreshToken");
    }

    public function deleteRefreshToken(string $refreshToken): ?int
    {
        return Redis::del("refresh_tokens:$refreshToken");
    }
}
