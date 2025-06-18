<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse; // Added
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function store(LoginRequest $request) // : RedirectResponse - Assuming store also returns RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->route('dashboard'); // Redirect to dashboard
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse // Update return type hint
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/'); // Changed line
    }
}
