<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controller as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class VerificationController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Mark the authenticated user's email address as verified.
     */
    public function verify(Request $request): JsonResponse
    {
        try {
            Log::info('Verification attempt', [
                'id' => $request->route('id'),
                'hash' => $request->route('hash')
            ]);

            $user = User::findOrFail($request->route('id'));

            if (!hash_equals(
                (string) $request->route('hash'),
                sha1($user->getEmailForVerification())
            )) {
                Log::warning('Invalid verification hash', [
                    'user_id' => $user->id,
                    'provided_hash' => $request->route('hash'),
                    'expected_hash' => sha1($user->getEmailForVerification())
                ]);
                
                return response()->json([
                    'message' => 'Invalid verification link.',
                ], 400);
            }

            if ($user->hasVerifiedEmail()) {
                Log::info('Email already verified', ['user_id' => $user->id]);
                return response()->json([
                    'message' => 'Email already verified.',
                ], 200);
            }

            if ($user->markEmailAsVerified()) {
                event(new Verified($user));
                Log::info('Email verified successfully', ['user_id' => $user->id]);
            }

            return response()->json([
                'message' => 'Email has been verified successfully.',
                'verified' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Verification error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'An error occurred during verification.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resend the email verification notification.
     */
    public function resend(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified.',
            ], 200);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Verification link has been resent.',
        ]);
    }
} 