<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class VerificationController extends Controller
{
    /**
     * Handle the email verification process
     */
    public function verify(Request $request)
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
                
                return redirect()->route('verification.error');
            }

            if ($user->hasVerifiedEmail()) {
                Log::info('Email already verified', ['user_id' => $user->id]);
                return redirect()->route('verification.success');
            }

            if ($user->markEmailAsVerified()) {
                event(new Verified($user));
                Log::info('Email verified successfully', ['user_id' => $user->id]);
            }

            return redirect()->route('verification.success');

        } catch (\Exception $e) {
            Log::error('Verification error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('verification.error');
        }
    }

    /**
     * Show the success page
     */
    public function success()
    {
        return view('verification.success');
    }

    /**
     * Show the error page
     */
    public function error()
    {
        return view('verification.error');
    }
} 