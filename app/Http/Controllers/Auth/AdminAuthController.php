<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.admin_login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->role !== 'admin') {
                Auth::logout();
                return redirect()->route('admin.login')->withErrors(['email' => 'You are not authorized to access this area.']);
            }

            $user->update(['last_login_at' => now()]);
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('admin.login')->withErrors(['email' => 'Invalid credentials']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login')->with('success', 'Logged out successfully');
    }


}