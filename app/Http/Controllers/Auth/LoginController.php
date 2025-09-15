<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function show(): \Illuminate\View\View
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string'
        ]);

        $throttleKey = Str::lower($request->input('login')).'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return back()->withErrors(['login' => 'Too many attempts. Try again in '.RateLimiter::availableIn($throttleKey).' seconds.']);
        }

        $loginField = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [
            $loginField => $request->input('login'),
            'password' => $request->input('password')
        ];

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors(['login' => 'Invalid credentials'])->withInput();
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        $user = Auth::user();
        // Role-based redirect priority
        if ($user->hasRole('admin')) {
            return redirect()->intended('/admin');
        }
        if ($user->hasRole('case_manager')) {
            return redirect()->intended('/dashboard/case-manager');
        }
        if ($user->hasRole('attorney')) {
            return redirect()->intended('/attorney');
        }
        if ($user->hasRole('printing')) {
            return redirect()->intended('/dashboard/printing');
        }
        return redirect()->intended('/dashboard/applicant');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
