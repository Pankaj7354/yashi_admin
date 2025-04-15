<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Models\{User, Otp};
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    // Show Register Form & Handle Register
    public function register(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('admin_access.layouts.register');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'otp' => 'required|string',
            'password' => 'required|string|confirmed|min:6',
            'role' => 'user', // or 'admin', 'super_admin'

        ]);

        // Optional: Match OTP stored in session or database
        $otpRecord = Otp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now()) // Make sure it's still valid
            ->latest()
            ->first();

        if (!$otpRecord) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $otpRecord->delete(); // Optional

        return redirect()->route('auth.login')->with('success', 'Registration successful. Please login.');
    }
    // Show Login Form & Handle Login
    public function login(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('admin_access.layouts.login'); // Return view for login
        }

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/index'); // Redirect to dashboard or home page
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    // Handle Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('auth.login');
    }

    // Show Forgot Password Form
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    // Handle Forgot Password (Send Reset Link)
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    // Show Reset Password Form
    public function showResetForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    // Handle Reset Password Submission
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('auth.login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    // Dashboard (Authenticated users only)
    public function dashboard()
    {
        if (Auth::check()) {
            return view('admin_access.dashboard'); // Return view for dashboard
        }

        return redirect()->route('auth.login')->withErrors(['msg' => 'You must be logged in to access the dashboard.']);
    }
}
