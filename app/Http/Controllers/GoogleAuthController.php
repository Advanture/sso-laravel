<?php

namespace App\Http\Controllers;

use App\Services\NatsService;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Services\TokenService;
use App\Models\OAuthUser;
use Exception;

class GoogleAuthController extends Controller
{
    public function __construct(private readonly TokenService $tokenService, private readonly NatsService $natsService)
    {}

    public function redirect()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function callback()
    {
//        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => sprintf('GoogleUser%d', $googleUser->getId()),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random()),
                ]);

                OAuthUser::create([
                    'user_id' => $user->id,
                    'provider' => OAuthUser::PROVIDER_GOOGLE,
                    'provider_id' => $googleUser->getId(),
                ]);
            }

            $tokens = $this->tokenService->generateTokens($user->id);

            $this->natsService->publish('user.authenticated', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return response()->json([
                'success' => true,
                'tokens' => $tokens,
            ]);
//        } catch (Exception $e) {
//            return response()->json([
//                'success' => false,
//                'message' => 'Google authentication failed'
//            ], 500);
//        }
    }
}
