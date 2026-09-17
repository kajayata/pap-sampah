<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->load('role'));
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->onlyInput('email');
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Akun tidak aktif.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return $this->redirectByRole($user->load('role'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function dashboard()
    {
        $user = Auth::user()->load('role', 'village');

        return view('dashboard.index', compact('user'));
    }

    private function redirectByRole($user)
    {
        if ($user->hasRole('super_admin_kecamatan')) {
            return redirect()->route('dashboard');
        }

        if ($user->hasRole('admin_desa')) {
            return redirect()->route('dashboard');
        }

        return redirect('/');
    }
}
