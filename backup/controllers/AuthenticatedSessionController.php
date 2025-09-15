<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'login' => ['required','string'],
            'password' => ['required','string'],
        ]);

        $login = $request->input('login');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [
            $field => $login,
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();
            // Role-based redirect: admins to admin dashboard, applicants to applicant dashboard
            if($user && $user->hasAnyRole(['admin','super-admin'])){
                return redirect()->intended(route('admin.dashboard'));
            }
            return redirect()->intended(route('dashboard.applicant'));
        }

        return back()->withErrors([
            'login' => 'Invalid credentials provided.',
        ])->onlyInput('login');
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
