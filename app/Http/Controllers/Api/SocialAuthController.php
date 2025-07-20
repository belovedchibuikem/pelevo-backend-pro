<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::updateOrCreate(
                ['email' => $googleUser->email],
                [
                    'name' => $googleUser->name,
                    'google_id' => $googleUser->id,
                    'password' => Hash::make(Str::random(24)),
                    'email_verified_at' => now(),
                ]
            );

            $token = $user->createToken('google-auth')->plainTextToken;

            return response()->json([
                'token' => $token,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            Log::error('Google authentication error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to authenticate with Google'], 500);
        }
    }

    public function redirectToApple()
    {
        return Socialite::driver('apple')->redirect();
    }

    public function handleAppleCallback()
    {
        try {
            $appleUser = Socialite::driver('apple')->user();
            
            $user = User::updateOrCreate(
                ['email' => $appleUser->email],
                [
                    'name' => $appleUser->name ?? explode('@', $appleUser->email)[0],
                    'apple_id' => $appleUser->id,
                    'password' => Hash::make(Str::random(24)),
                    'email_verified_at' => now(),
                ]
            );

            $token = $user->createToken('apple-auth')->plainTextToken;

            return response()->json([
                'token' => $token,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            Log::error('Apple authentication error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to authenticate with Apple'], 500);
        }
    }

    public function redirectToSpotify()
    {
        return Socialite::driver('spotify')->redirect();
    }

    public function handleSpotifyCallback()
    {
        try {
            $spotifyUser = Socialite::driver('spotify')->user();
            
            $user = User::updateOrCreate(
                ['email' => $spotifyUser->email],
                [
                    'name' => $spotifyUser->name,
                    'spotify_id' => $spotifyUser->id,
                    'password' => Hash::make(Str::random(24)),
                    'email_verified_at' => now(),
                ]
            );

            $token = $user->createToken('spotify-auth')->plainTextToken;

            return response()->json([
                'token' => $token,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            Log::error('Spotify authentication error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to authenticate with Spotify'], 500);
        }
    }
} 