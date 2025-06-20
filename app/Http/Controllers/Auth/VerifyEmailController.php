<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User; // Added
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request; // Changed from EmailVerificationRequest
use Illuminate\Http\Response; // Changed from RedirectResponse
use Illuminate\Support\Facades\Auth; // Added for potential use, though we're avoiding session user

class VerifyEmailController extends Controller
{
    /**
     * Mark the user's email address as verified.
     */
    public function __invoke(Request $request, $id, $hash) // Removed return type hint
    {
        // Manually find the user by ID from the route
        $user = User::find($id);

        if (! $user) {
            // Or handle as appropriate, perhaps a generic error for API, or redirect for web
            return abort(404, 'User not found.');
        }

        // Authorization logic from EmailVerificationRequest (approximated for unauthenticated context)
        if (! hash_equals((string) $id, (string) $user->getKey())) {
            // This check is somewhat redundant if we fetch user by $id, but mirrors original
            return abort(403, 'Invalid user ID.');
        }

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return abort(403, 'Invalid or expired verification link (hash mismatch).');
        }
        // End of authorization logic

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(
                config('app.frontend_url', route('dashboard')).'/dashboard?verified=1' // Added fallback for frontend_url
            );
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // If the request wants JSON (which getJson() does), return JSON, otherwise redirect.
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Email verified successfully.']);
        }

        return redirect()->intended(
            config('app.frontend_url', route('dashboard')).'/dashboard?verified=1'
        );
    }
}
