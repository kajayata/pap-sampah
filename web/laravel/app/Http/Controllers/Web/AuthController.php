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

    $user = Auth::user()->load('role', 'village');

    if (!$user->is_active) {
        Auth::logout();

        return back()->withErrors([
            'email' => 'Akun tidak aktif.',
        ])->onlyInput('email');
    }

    if ($user->hasRole('admin_desa')) {
        if (!$user->village) {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Akun admin kelurahan belum memiliki kelurahan.',
            ])->onlyInput('email');
        }

        if (!$user->village->is_active) {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Kelurahan tempat akun Anda terdaftar sedang nonaktif.',
            ])->onlyInput('email');
        }
    }

    $request->session()->regenerate();

    return $this->redirectByRole($user);
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
        $isSuperAdmin = $user->hasRole('super_admin_kecamatan');
        $villageId = $user->village_id;

        // Base query ber-scope wilayah: super admin = semua, admin kelurahan = wilayahnya.
        $scoped = fn () => \App\Models\WasteReport::query()
            ->when(!$isSuperAdmin, fn ($q) => $q->where('village_id', $villageId));

        // Ringkasan status (semua status dalam satu query).
        $statusCounts = $scoped()->selectRaw("
            count(*) as total,
            count(*) filter (where status = 'PENDING_VALIDATION') as pending,
            count(*) filter (where status = 'VALIDATED') as validated,
            count(*) filter (where status = 'ASSIGNED') as assigned,
            count(*) filter (where status = 'IN_PROGRESS') as in_progress,
            count(*) filter (where status = 'PENDING_VERIFICATION') as pending_verification,
            count(*) filter (where status = 'RESOLVED') as resolved,
            count(*) filter (where status = 'REJECTED') as rejected
        ")->first();

        $total = (int) ($statusCounts->total ?? 0);
        $resolved = (int) ($statusCounts->resolved ?? 0);
        $rejected = (int) ($statusCounts->rejected ?? 0);
        $pending = (int) ($statusCounts->pending ?? 0);
        $pendingVerification = (int) ($statusCounts->pending_verification ?? 0);
        $inProgress = (int) ($statusCounts->assigned ?? 0) + (int) ($statusCounts->in_progress ?? 0);

        $statusBreakdown = [
            ['label' => 'Menunggu Validasi', 'count' => $pending, 'color' => '#FF9D23'],
            ['label' => 'Tervalidasi', 'count' => (int) ($statusCounts->validated ?? 0), 'color' => '#FFD65A'],
            ['label' => 'Sedang Ditangani', 'count' => $inProgress, 'color' => '#5B7E3C'],
            ['label' => 'Menunggu Verifikasi', 'count' => $pendingVerification, 'color' => '#8B5CF6'],
            ['label' => 'Selesai', 'count' => $resolved, 'color' => '#2E7D32'],
            ['label' => 'Ditolak', 'count' => $rejected, 'color' => '#EA5252'],
        ];

        $stats = [
            'total' => $total,
            'active' => $total - $resolved - $rejected,
            'resolved' => $resolved,
            'workers' => \Illuminate\Support\Facades\DB::table('users')
                ->join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.name', 'petugas_desa')
                ->where('users.is_active', true)
                ->when(!$isSuperAdmin, fn ($q) => $q->where('users.village_id', $villageId))
                ->count(),
            'pending_validation' => $pending,
            'pending_verification' => $pendingVerification,
            'in_progress' => $inProgress,
            'completion_rate' => $total > 0 ? (int) round($resolved / $total * 100) : 0,
        ];

        // Tren laporan 7 hari terakhir + perbandingan 7 hari sebelumnya.
        $prevStart = now()->subDays(13)->startOfDay();
        $prevEnd = now()->subDays(7)->endOfDay();

        $trendRows = $scoped()
            ->where('created_at', '>=', $prevStart)
            ->selectRaw("to_char(created_at, 'YYYY-MM-DD') as d, count(*) as c")
            ->groupBy('d')
            ->pluck('c', 'd');

        $dayLabels = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $trend[] = [
                'label' => $dayLabels[$date->dayOfWeek],
                'date' => $date->format('d/m'),
                'count' => (int) ($trendRows[$date->toDateString()] ?? 0),
            ];
        }

        $recent7 = array_sum(array_column($trend, 'count'));
        $prev7 = (int) $scoped()->whereBetween('created_at', [$prevStart, $prevEnd])->count();
        $trendDelta = $prev7 > 0
            ? (int) round(($recent7 - $prev7) / $prev7 * 100)
            : ($recent7 > 0 ? 100 : 0);

        // Kategori terbanyak.
        $topCategories = $scoped()
            ->join('waste_categories', 'waste_reports.category_id', '=', 'waste_categories.id')
            ->selectRaw('waste_categories.name as name, count(*) as total')
            ->groupBy('waste_categories.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Kelurahan dengan laporan aktif terbanyak + rincian status (khusus super admin).
        $topVillages = $isSuperAdmin
            ? \App\Models\WasteReport::query()
                ->join('villages', 'waste_reports.village_id', '=', 'villages.id')
                ->whereNotIn('waste_reports.status', ['RESOLVED', 'REJECTED'])
                ->selectRaw("
                    villages.name as name,
                    count(*) as total,
                    count(*) filter (where waste_reports.status = 'PENDING_VALIDATION') as pending,
                    count(*) filter (where waste_reports.status in ('ASSIGNED', 'IN_PROGRESS')) as in_progress,
                    count(*) filter (where waste_reports.status = 'PENDING_VERIFICATION') as pending_verification
                ")
                ->groupBy('villages.name')
                ->orderByDesc('total')
                ->limit(5)
                ->get()
            : collect();

        // Petugas teraktif — di-reset tiap bulan (tugas selesai sejak awal bulan).
        $topWorkers = \Illuminate\Support\Facades\DB::table('cleanup_task_workers as ctw')
            ->join('users as u', 'ctw.worker_id', '=', 'u.id')
            ->where('ctw.status', 'COMPLETED')
            ->where('ctw.completed_at', '>=', now()->startOfMonth())
            ->when(!$isSuperAdmin, fn ($q) => $q->where('u.village_id', $villageId))
            ->selectRaw('u.name as name, count(*) as total')
            ->groupBy('u.id', 'u.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $recentReports = \App\Models\WasteReport::with(['category', 'village', 'reporter'])
            ->when(!$isSuperAdmin, fn ($q) => $q->where('village_id', $villageId))
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'user',
            'stats',
            'recentReports',
            'statusBreakdown',
            'trend',
            'trendDelta',
            'topCategories',
            'topVillages',
            'topWorkers'
        ));
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
