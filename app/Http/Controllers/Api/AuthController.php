<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\PasswordReset;

class AuthController extends Controller
{
       /**
     * Handle user registration with comprehensive error handling
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        try {
            // Rate limiting to prevent spam registrations
            $key = 'register.' . $request->ip();
            
            if (RateLimiter::tooManyAttempts($key, 3)) {
                $seconds = RateLimiter::availableIn($key);
                return $this->errorResponse(
                    'Too many registration attempts. Please try again later.',
                    429,
                    ['retry_after' => $seconds]
                );
            }

            // Input validation with comprehensive rules
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:2|max:255|regex:/^[a-zA-Z\s]+$/',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|max:255|confirmed',
                'password_confirmation' => 'required|string|min:8|max:255',
            ], [
                'name.regex' => 'The name may only contain letters and spaces.',
                'password.confirmed' => 'The password confirmation does not match.',
                'email.unique' => 'An account with this email address already exists.',
            ]);

            if ($validator->fails()) {
                RateLimiter::hit($key, 300); // 5 minutes decay for failed validation
                return $this->errorResponse(
                    'Validation failed',
                    422,
                    ['validation_errors' => $validator->errors()]
                );
            }

            // Additional email format validation
            if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
                return $this->errorResponse(
                    'Invalid email format',
                    422,
                    ['field' => 'email']
                );
            }

            // Check for disposable email domains (optional)
            $disposableDomains = ['10minutemail.com', 'tempmail.org', 'guerrillamail.com'];
            $emailDomain = substr(strrchr($request->email, "@"), 1);
            
            if (in_array(strtolower($emailDomain), $disposableDomains)) {
                return $this->errorResponse(
                    'Disposable email addresses are not allowed',
                    422,
                    ['field' => 'email']
                );
            }

            // Password strength validation
            $password = $request->password;
            $passwordErrors = [];

            if (!preg_match('/[A-Z]/', $password)) {
                $passwordErrors[] = 'Password must contain at least one uppercase letter';
            }
            if (!preg_match('/[a-z]/', $password)) {
                $passwordErrors[] = 'Password must contain at least one lowercase letter';
            }
            if (!preg_match('/[0-9]/', $password)) {
                $passwordErrors[] = 'Password must contain at least one number';
            }
            if (!preg_match('/[^A-Za-z0-9]/', $password)) {
                $passwordErrors[] = 'Password must contain at least one special character';
            }

            if (!empty($passwordErrors)) {
                return $this->errorResponse(
                    'Password does not meet security requirements',
                    422,
                    ['password_requirements' => $passwordErrors]
                );
            }

            // Begin database transaction
            DB::beginTransaction();
            
            try {
                // Create user with additional fields
                

                $user = User::create([
                    'name' => trim($request->name),
                    'email' => strtolower(trim($request->email)),
                    'password' => Hash::make($password),
                    'email_verified_at' => null, // Will be set after email verification
                    'is_active' => true,
                    'registered_at' => now(),
                    'registration_ip' => $request->ip(),
                ]);

                // Debug email sending with detailed logging
                try {
                    logger()->info('Starting email verification process', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'mail_config' => [
                            'MAIL_MAILER' => config('mail.default'),
                            'MAIL_HOST' => config('mail.mailers.smtp.host'),
                            'MAIL_PORT' => config('mail.mailers.smtp.port'),
                            'MAIL_USERNAME' => config('mail.mailers.smtp.username'),
                            'MAIL_ENCRYPTION' => config('mail.mailers.smtp.encryption'),
                            'MAIL_FROM_ADDRESS' => config('mail.from.address'),
                            'MAIL_FROM_NAME' => config('mail.from.name'),
                        ]
                    ]);

                    

                    // Send verification email using SMTP
                    Mail::send('emails.verify', ['user' => $user], function($message) use ($user) {
                        $message->to($user->email)
                               ->subject('Verify Your Email Address');
                    });
                    
                    logger()->info('Verification email sent successfully');
                } catch (\Exception $e) {
                    logger()->error('Failed to send verification email', [
                        'error' => $e->getMessage(),
                        'error_code' => $e->getCode(),
                        'error_file' => $e->getFile(),
                        'error_line' => $e->getLine(),
                        'trace' => $e->getTraceAsString(),
                        'user_id' => $user->id,
                        'email' => $user->email
                    ]);

                    // Don't throw the error, just log it and continue
                    // This way the user can still register even if email fails
                }
                
                // Generate auth token
                $tokenName = 'auth_token_' . Str::random(10);
                $token = $user->createToken($tokenName)->plainTextToken;

                // Commit transaction
                DB::commit();

                // Clear rate limiting on successful registration
                RateLimiter::clear($key);

                // Log successful registration
                logger()->info('User registered successfully', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'ip' => $request->ip(),
                ]);

                // Prepare user data (exclude sensitive information)
                $userData = $user->only([
                    'id', 'name', 'email', 'email_verified_at', 'is_active', 'created_at'
                ]);

                return $this->successResponse(
                    'Registration successful',
                    [
                        'user' => $userData,
                        'token' => $token,
                        'token_type' => 'Bearer',
                        'requires_email_verification' => true,
                        'message' => 'Please check your email to verify your account.',
                    ],
                    201
                );

            } catch (Exception $e) {
                // Rollback transaction on database error
                DB::rollback();
                throw $e;
            }

        } catch (Exception $e) {
            // Log the error for debugging
            logger()->error('Registration error: ' . $e->getMessage(), [
                'email' => $request->email ?? 'unknown',
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->errorResponse(
                'An unexpected error occurred during registration. Please try again later.',
                500
            );
        }
    }

    public function login(Request $request): JsonResponse
    {
        try {
            // Rate limiting to prevent brute force attacks
            $key = 'login.' . $request->ip();
            
            if (RateLimiter::tooManyAttempts($key, 5)) {
                $seconds = RateLimiter::availableIn($key);
                return $this->errorResponse(
                    'Too many login attempts. Please try again later.',
                    429,
                    ['retry_after' => $seconds]
                );
            }

            // Input validation
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255',
                'password' => 'required|string|min:6|max:255',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(
                    'Validation failed',
                    422,
                    ['validation_errors' => $validator->errors()]
                );
            }

            $credentials = $request->only('email', 'password');

            // Check if user exists first
            $user = User::where('email', $credentials['email'])->first();
            
            if (!$user) {
                RateLimiter::hit($key, 300); // 5 minutes decay
                return $this->errorResponse(
                    'Invalid credentials',
                    401,
                    ['field' => 'email']
                );
            }

            // Check if user account is active (assuming you have an is_active field)
            if (isset($user->is_active) && !$user->is_active) {
                return $this->errorResponse(
                    'Account is deactivated. Please contact support.',
                    403
                );
            }

            // Check if email is verified (assuming you have email verification)
            if ($user->email_verified_at==null) {
                return $this->errorResponse(
                    'Email address is not verified. Please verify your email first.',
                    403,
                    ['requires_verification' => true]
                );
            }

            // Attempt authentication
            if (!Hash::check($credentials['password'], $user->password)) {
                RateLimiter::hit($key, 300); // 5 minutes decay
                return $this->errorResponse(
                    'Invalid credentials',
                    401,
                    ['field' => 'password']
                );
            }

            // Clear rate limiting on successful login
            RateLimiter::clear($key);

            // Generate token
            $tokenName = 'auth_token_' . Str::random(10);
            $token = $user->createToken($tokenName)->plainTextToken;

            // Update last login timestamp (optional)
            $user->update(['last_login_at' => now()]);

            // Prepare user data (exclude sensitive information)
            $userData = $user->only([
                'id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at'
            ]);
            $expiresAt = now()->addHours(2160);
            return $this->successResponse(
                'Login successful',
                [
                    'user' => $userData,
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_at' => $expiresAt, // Set if you have token expiration
                ]
            );

        } catch (Exception $e) {
            // Log the error for debugging
            logger()->error('Login error: ' . $e->getMessage(), [
                'email' => $request->email ?? 'unknown',
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return $this->errorResponse(
                'An unexpected error occurred. Please try again later.',
                500
            );
        }
    }

    /**
     * Handle user logout
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return $this->errorResponse(
                    'No authenticated user found',
                    401
                );
            }

            // Revoke all tokens for the user (optional - uncomment if you want to revoke all tokens)
            // $user->tokens()->delete();
            
            // Revoke only the current token
            $user->currentAccessToken()->delete();

            // Log the logout action
            logger()->info('User logged out successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
            ]);

            return $this->successResponse(
                'Successfully logged out',
                [
                    'user_id' => $user->id,
                    'logged_out_at' => now()->toISOString(),
                ]
            );

        } catch (\Exception $e) {
            logger()->error('Logout error: ' . $e->getMessage(), [
                'user_id' => $request->user()?->id ?? 'unknown',
                'ip' => $request->ip(),
            ]);

            return $this->errorResponse(
                'An error occurred during logout',
                500
            );
        }
    }

    public function googleRedirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function googleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::updateOrCreate(
                ['email' => $googleUser->email],
                [
                    'name' => $googleUser->name,
                    'google_id' => $googleUser->id,
                    'password' => Hash::make(Str::random(24)),
                ]
            );

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Google login successful',
                'user' => $user,
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Google login failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function appSignIn(Request $request)
    {
        $request->validate([
            'device_id' => 'required|string',
            'device_name' => 'required|string',
        ]);

        $user = User::firstOrCreate(
            ['device_id' => $request->device_id],
            [
                'name' => 'Guest User',
                'email' => "guest_{$request->device_id}@pelevo.app",
                'password' => Hash::make(Str::random(24)),
                'device_name' => $request->device_name,
            ]
        );

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'App sign in successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

      /**
     * Return a success JSON response
     *
     * @param string $message
     * @param array $data
     * @param int $status
     * @return JsonResponse
     */
    private function successResponse(string $message, array $data = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toISOString(),
        ], $status);
    }

    /**
     * Return an error JSON response
     *
     * @param string $message
     * @param int $status
     * @param array $errors
     * @return JsonResponse
     */
    private function errorResponse(string $message, int $status = 400, array $errors = []): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => now()->toISOString(),
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    /**
     * Send password reset link
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        try {
            // Rate limiting
            $key = 'forgot_password.' . $request->ip();
            
            if (RateLimiter::tooManyAttempts($key, 3)) {
                $seconds = RateLimiter::availableIn($key);
                return $this->errorResponse(
                    'Too many attempts. Please try again later.',
                    429,
                    ['retry_after' => $seconds]
                );
            }

            // Validate input
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255',
            ]);

            if ($validator->fails()) {
                RateLimiter::hit($key, 300);
                return $this->errorResponse(
                    'Validation failed',
                    422,
                    ['validation_errors' => $validator->errors()]
                );
            }

            // Find user
            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                // Return success even if user not found for security
                return $this->successResponse(
                    'If your email is registered, you will receive a password reset link.'
                );
            }

            // Generate token
            $token = Str::random(64);
            $expiresAt = now()->addHours(24);

            // Store token
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token' => Hash::make($token),
                    'created_at' => now(),
                    'expires_at' => $expiresAt
                ]
            );

            // Generate reset URL using the named route with token parameter
            $resetUrl = route('password.reset', ['token' => $token]) . '?email=' . urlencode($user->email);

            // Send email using the template
            Mail::send('emails.reset-password', [
                'user' => $user,
                'resetUrl' => $resetUrl
            ], function($message) use ($user) {
                $message->to($user->email)
                    ->subject('Reset Your Password - ' . config('app.name'))
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });

            // Clear rate limiting on success
            RateLimiter::clear($key);

            return $this->successResponse(
                'If your email is registered, you will receive a password reset link.'
            );

        } catch (Exception $e) {
            logger()->error('Forgot password error: ' . $e->getMessage(), [
                'email' => $request->email ?? 'unknown',
                'ip' => $request->ip(),
            ]);

            return $this->errorResponse(
                'An unexpected error occurred. Please try again later.',
                500
            );
        }
    }

    /**
     * Show the password reset form.
     */
    public function showResetForm(Request $request, $token)
    {
        $email = $request->query('email');
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email
        ]);
    }

    /**
     * Reset the user's password.
     */
    public function resetPassword(Request $request)
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'email' => 'required|email',
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
                ],
            ], [
                'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            ]);

            if ($validator->fails()) {
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors($validator);
            }

            // Find the password reset token
            $resetToken = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->first();

            if (!$resetToken || !Hash::check($request->token, $resetToken->token)) {
                return back()
                    ->withInput($request->only('email'))
                    ->with('error', 'Invalid or expired password reset token.');
            }

            // Check if token is expired
            if (now()->isAfter($resetToken->expires_at)) {
                DB::table('password_reset_tokens')->where('email', $request->email)->delete();
                return back()
                    ->withInput($request->only('email'))
                    ->with('error', 'Password reset token has expired. Please request a new one.');
            }

            // Find user
            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                return back()
                    ->withInput($request->only('email'))
                    ->with('error', 'User not found.');
            }

            // Update password
            $user->password = Hash::make($request->password);
            $user->save();

            // Delete used token
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            // Log the password change
            Log::info('Password reset successful', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);

            return back()
                ->with('success', 'Your password has been reset successfully. Please login with your new password.');

        } catch (\Exception $e) {
            Log::error('Password reset error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput($request->only('email'))
                ->with('error', 'An error occurred while resetting your password. Please try again.');
        }
    }
}
 