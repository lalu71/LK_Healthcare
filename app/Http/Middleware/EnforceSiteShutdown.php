<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceSiteShutdown
{
    /**
     * Path patterns that must always work even during shutdown:
     * authentication flow, password ops, email verification, language switch.
     * Path matching is used (not route names) because Breeze's POST endpoints
     * for /login, /register, /confirm-password are unnamed.
     */
    private const ALLOWED_PATHS = [
        'login',
        'logout',
        'register',
        'forgot-password',
        'reset-password',
        'reset-password/*',
        'confirm-password',
        'verify-email',
        'verify-email/*',
        'email/verification-notification',
        'password',
        'language/*',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! Setting::isShutdown()) {
            return $next($request);
        }

        // Admin can do anything (so they can flip the site back on).
        if ($request->user() && $request->user()->hasRole('admin')) {
            return $next($request);
        }

        // Always allow auth-related paths so users can sign in / out / reset password.
        foreach (self::ALLOWED_PATHS as $path) {
            if ($request->is($path)) {
                return $next($request);
            }
        }

        // Read-only views are fine: GET / HEAD / OPTIONS pass through with a banner.
        if ($request->isMethodSafe()) {
            return $next($request);
        }

        // Block any write action (POST / PATCH / PUT / DELETE) with a friendly message.
        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('Site is shut down. Actions are disabled.'),
            ], 503);
        }

        return back()->with('error', __('Site is shut down. Actions are disabled.'));
    }
}
