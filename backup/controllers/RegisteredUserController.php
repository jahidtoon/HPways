<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => ['required','string','min:3','max:40','unique:users,username'],
            'first_name' => ['required','string','max:60'],
            'last_name' => ['required','string','max:60'],
            'email' => ['required','string','email','max:255','unique:users,email'],
            'password' => ['required','string','min:8','confirmed'],
        ]);

        $user = User::create([
            'name' => $data['first_name'].' '.$data['last_name'],
            'username' => $data['username'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Assign default role
        try {
            if (Role::where('name','applicant')->exists()) {
                $user->assignRole('applicant');
            }
        } catch (\Throwable $e) {
            // swallow role assignment errors so registration still works
        }

        event(new Registered($user));
        Auth::login($user);

    return redirect()->intended('/dashboard/applicant');
    }
}
