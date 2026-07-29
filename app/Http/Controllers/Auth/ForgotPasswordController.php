<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    /**
     * Display the password reset request form.
     */
    public function showLinkRequestForm(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle a password reset link request.
     * 
     * Flow:
     * 1. Validate email
     * 2. Generate random token
     * 3. Hash token and store in password_reset_tokens table
     * 4. Send email with reset link
     * 5. Token valid for 60 minutes
     */
    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Send password reset link
        // This will:
        // - Generate token
        // - Store hashed token in password_reset_tokens table
        // - Send email with link: /reset-password?token=xxx&email=xxx
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}
