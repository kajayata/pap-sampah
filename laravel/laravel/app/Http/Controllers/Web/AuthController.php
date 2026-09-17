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

        if ($user->hasRole('super_admin_kecamatan')) {
            $reportCounts = \Illuminate\Support\Facades\DB::table('waste_reports')
                ->selectRaw("
                    count(*) as total,
                    count(*) filter (where status not in ('RESOLVED', 'REJECTED')) as active,
                    count(*) filter (where status = 'RESOLVED') as resolved
                ")
                ->first();

            $workersCount = \Illuminate\Support\Facades\DB::table('users')
                ->join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.name', 'petugas_desa')
                ->where('users.is_active', true)
                ->count();

            $stats = [
                'total' => (int) ($reportCounts->total ?? 0),
                'active' => (int) ($reportCounts->active ?? 0),
                'resolved' => (int) ($reportCounts->resolved ?? 0),
                'workers' => $workersCount,
            ];

            $recentReports = \App\Models\WasteReport::with(['category', 'village', 'reporter'])
                ->latest('created_at')
                ->limit(5)
                ->get();
        } else {
            $villageId = $user->village_id;

            $reportCounts = \Illuminate\Support\Facades\DB::table('waste_reports')
                ->where('village_id', $villageId)
                ->selectRaw("
                    count(*) as total,
                    count(*) filter (where status not in ('RESOLVED', 'REJECTED')) as active,
                    count(*) filter (where status = 'RESOLVED') as resolved
                ")
                ->first();

            $workersCount = \Illuminate\Support\Facades\DB::table('users')
                ->join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.name', 'petugas_desa')
                ->where('users.village_id', $villageId)
                ->where('users.is_active', true)
                ->count();

            $stats = [
                'total' => (int) ($reportCounts->total ?? 0),
                'active' => (int) ($reportCounts->active ?? 0),
                'resolved' => (int) ($reportCounts->resolved ?? 0),
                'workers' => $workersCount,
            ];

            $recentReports = \App\Models\WasteReport::with(['category', 'reporter'])
                ->where('village_id', $villageId)
                ->latest('created_at')
                ->limit(5)
                ->get();
        }

        return view('dashboard.index', compact('user', 'stats', 'recentReports'));
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
