<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function show(): \Illuminate\View\View
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:60|unique:users,username',
            'first_name' => 'required|string|max:60',
            'last_name' => 'required|string|max:60',
            'email' => 'required|email|max:180|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'vt' => 'nullable|string|max:40',
            'pkg' => 'nullable|integer|exists:packages,id'
        ]);

        $throttleKey = Str::lower($request->input('email')).'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return back()->withErrors(['email' => 'Too many attempts. Try again in '.RateLimiter::availableIn($throttleKey).' seconds.']);
        }

        $fullName = trim($request->first_name.' '.$request->last_name);
        $user = User::create([
            'name' => $fullName,
            'username' => $request->username,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        // Default role applicant if spatie roles table exists
        try { $user->assignRole('applicant'); } catch(\Throwable $e) { /* ignore if role not seeded */ }

        Auth::login($user);
        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        // Optional: auto create draft application if vt (visa_type) provided
        $application = null;
        if($request->filled('vt')) {
            try { $application = \App\Models\Application::firstOrCreate([
                'user_id'=>$user->id,
                'visa_type'=>strtoupper(preg_replace('/[^A-Z0-9_]/i','',$request->vt)),
                'status'=>'draft'
            ],['progress_pct'=>5,'payment_status'=>'unpaid']); } catch(\Throwable $e) {}
        }

        // If package supplied, validate compatibility and attach
        if($application && $request->filled('pkg')) {
            try {
                $pkg = \App\Models\Package::find($request->pkg);
                if($pkg && (!$pkg->visa_type || strtoupper($pkg->visa_type) === $application->visa_type)) {
                    $application->selected_package_id = $pkg->id;
                    $application->save();
                }
            } catch(\Throwable $e) { /* ignore */ }
        }

    return redirect('/dashboard/applicant');
    }
}
