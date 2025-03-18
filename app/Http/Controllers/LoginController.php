<?php

namespace App\Http\Controllers;

use App\Jobs\SuccessAuthMessage;
use App\Services\NatsService;
use App\Services\TokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    public function __construct(private readonly TokenService $tokenService, private readonly NatsService $natsService)
    {}

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized'
            ], 401);
        }

        $tokens = $this->tokenService->generateTokens($user->id, $user->roles ?? []);

        $this->natsService->publish('user.authenticated', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return response()->json([
            'success' => true,
            'tokens' => $tokens,
        ]);
    }
}
