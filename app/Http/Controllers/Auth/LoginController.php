<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UserPortalAccessNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Display the login view.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectUserByRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return $this->redirectUserByRole(Auth::user());
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Authenticate user via signed portal access link.
     */
    public function accessPortalViaLink(Request $request, User $user): RedirectResponse
    {
        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectUserByRole($user)->with('success', 'You have successfully authenticated via your portal link.');
    }

    /**
     * Resend access link to user on demand.
     */
    public function resendAccessLink(Request $request, User $user): RedirectResponse|JsonResponse
    {
        try {
            $user->notify(new UserPortalAccessNotification());
            session()->flash('email_sent', true);
        } catch (\Throwable $e) {
            \Log::error('Failed sending resend access notification: ' . $e->getMessage());
            session()->flash('email_error', $e->getMessage());
        }

        $accessUrl = URL::temporarySignedRoute(
            'portal.access',
            now()->addHours(48),
            ['user' => $user->id]
        );

        session()->flash('access_url', $accessUrl);
        session()->flash('user_name', $user->name);
        session()->flash('user_email', $user->email);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Portal access link generated for {$user->email}.",
                'access_url' => $accessUrl,
            ]);
        }

        return back()->with('success', "Portal access link generated for {$user->email}.");
    }

    /**
     * Show the self-service "Request New Portal Access Link" form.
     */
    public function showRequestLinkForm(): View
    {
        return view('auth.request-portal-link');
    }

    /**
     * Handle the portal link request.
     *
     * Security: We always return the same success message whether the
     * email exists or not — this prevents email enumeration attacks.
     * Only student/teacher accounts get a link (admins use password).
     */
    public function requestPortalLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)
            ->whereIn('role', ['student', 'teacher'])
            ->first();

        if ($user) {
            try {
                $user->notify(new UserPortalAccessNotification());
            } catch (\Throwable $e) {
                \Log::error('Failed sending self-service portal link: ' . $e->getMessage());
            }
        }

        // Always redirect with success — never reveal if email exists
        return back()->with(
            'success',
            'If that email is registered, a new portal access link has been sent. Please check your inbox (and spam folder).'
        );
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Redirect users to their respective dashboard based on role.
     */
    protected function redirectUserByRole($user): RedirectResponse
    {
        return match ($user->role) {
            'admin' => redirect()->intended(route('admin.dashboard')),
            'teacher' => redirect()->intended(route('teacher.dashboard')),
            'student' => redirect()->intended(route('student.dashboard')),
            default => redirect()->route('login'),
        };
    }
}
