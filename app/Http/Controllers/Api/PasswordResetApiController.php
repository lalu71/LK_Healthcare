<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Mobile-facing password reset trigger.
 *
 * Architecture C: We reuse Laravel's built-in Password broker to issue
 * an emailed reset link. The link itself points to the WEB reset form
 * (APP_URL/reset-password/{token}). Mobile app only triggers the email
 * here — the user completes the reset in their browser.
 *
 * Existing web flow (PasswordResetLinkController / NewPasswordController)
 * is untouched.
 */
class PasswordResetApiController extends Controller
{
    /**
     * POST /api/v1/forgot-password
     *
     * Body:
     *   { "email": "user@example.com" }
     *
     * Response 200:
     *   { "status": true, "message": "Password reset link sent to your email" }
     *
     * Response 422:
     *   { "status": false, "message": "..." , "errors": {...} }
     *
     * Response 429:
     *   { "status": false, "message": "Too many requests. Try again in N seconds." }
     */
    public function sendLink(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'No account is registered with this email address.',
        ]);

        $email = strtolower(trim($request->input('email')));

        // Throttle: max 3 attempts per email per hour.
        $key = 'forgot-password:' . sha1($email);
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'status' => false,
                'message' => "Too many reset attempts. Please try again in {$seconds} seconds.",
            ], 429);
        }
        RateLimiter::hit($key, 3600); // 1 hour decay

        // Delegate to Laravel's built-in broker — emails the link, stores hashed
        // token in `password_reset_tokens` table. Reuses the SAME flow as web.
        $status = Password::sendResetLink(['email' => $email]);

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'status' => true,
                'message' => 'Password reset link sent to your email. Please check your inbox.',
            ]);
        }

        // Any other status (THROTTLED, INVALID_USER, etc.) — surface as 422.
        return response()->json([
            'status' => false,
            'message' => __($status),
        ], 422);
    }
}
