<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show the login page.
     */
    public function showLogin()
    {
        // Redirect if already logged in
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Show the register page.
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    /**
     * Handle register form submission.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:191',
            'user_name' => 'required|string|max:191|unique:users,user_name',
            'gmail'     => 'required|email|max:191|unique:users,gmail',
            'phone_no'  => 'nullable|string|max:20',
            'password'  => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name'      => $request->name,
            'user_name' => $request->user_name,
            'gmail'     => $request->gmail,
            'phone_no'  => $request->phone_no,
            'password'  => $request->password, // auto-hashed by model cast
            'user_type' => 'user',
            'status'    => 'active',
        ]);

        Auth::login($user);
        return redirect()->route('dashboard');
    }

    /**
     * Handle login form submission.
     */
    public function login(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string',
            'password'  => 'required|string',
        ]);

        $credentials = [
            'user_name' => $request->user_name,
            'password'  => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'user_name' => 'Invalid username or password.',
        ])->withInput($request->only('user_name'));
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
