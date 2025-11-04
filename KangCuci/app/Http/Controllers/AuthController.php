<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        return view('dashboard.auth.login');
    }

    public function loginAttempt(LoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();
        return to_route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return to_route('login');
    }

    public function register()
    {
        return view('dashboard.auth.register');
    }

    public function registerAttempt(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('pelanggan'); // jika pakai spatie/laravel-permission

        Auth::login($user);
        $request->session()->regenerate();

        return to_route('pelanggan.dashboard');
    }
}
