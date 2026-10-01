<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Tampilkan daftar user (masyarakat/warga saja).
     */
    public function index(Request $request)
    {
        $this->authorizeSuperAdmin();

        // Cari role untuk masyarakat (pastikan nama role sesuai dengan database)
        $userRole = Role::whereIn('name', ['masyarakat', 'user'])->first();

        $query = User::query();

        // Filter hanya menampilkan user dengan role masyarakat
        if ($userRole) {
            $query->where('role_id', $userRole->id);
        } else {
            $query->whereHas('role', function ($q) {
                $q->whereIn('name', ['masyarakat', 'user']);
            });
        }

        // Fitur Pencarian (Search Filter)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%")
                    ->orWhere('phone', 'ilike', "%{$search}%");
            });
        }

        $users = $query->latest('id')->paginate(10)->withQueryString();

        return view('user.index', compact('users'));
    }

    /**
     * Ubah status aktif/nonaktif akun user.
     */
    public function toggleStatus($id)
    {
        $this->authorizeSuperAdmin();

        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        $statusMessage = $user->is_active ? 'Akun berhasil diaktifkan kembali.' : 'Akun berhasil dikunci/dinonaktifkan.';

        return redirect()->back()->with('success', $statusMessage);
    }

    /**
     * Form edit data user.
     */
    public function edit(int $id)
    {
        $this->authorizeSuperAdmin();

        $user = User::findOrFail($id);

        return view('user.edit', compact('user'));
    }

    /**
     * Update data user dengan validasi ketat & mengabaikan unik untuk ID milik sendiri.
     */
    public function update(Request $request, int $id)
    {
        $this->authorizeSuperAdmin();

        $user = User::findOrFail($id);

        $rules = [
            // 1. Nama wajib huruf & spasi (tidak boleh ada angka sama sekali)
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s]+$/'
            ],
            // 2. Email wajib unik (mengabaikan ID user yang sedang diedit)
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id, 'id')
            ],
            // 3. Nomor HP diawali 08, panjang 10-15 digit, unik (mengabaikan ID user yang sedang diedit)
            'phone' => [
                'required',
                'string',
                'regex:/^08[0-9]{8,13}$/',
                Rule::unique('users', 'phone')->ignore($user->id, 'id')
            ],
            'is_active' => ['required', 'boolean'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['string', 'min:8', 'confirmed'];
        }

        $validated = $request->validate($rules, [
            'name.required' => 'Nama pengguna wajib diisi.',
            'name.regex' => 'Nama pengguna hanya boleh berisi huruf dan spasi (tidak boleh mengandung angka).',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh pengguna lain.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.regex' => 'Nomor telepon harus diawali dengan 08 dan memiliki panjang 10 hingga 15 digit angka.',
            'phone.unique' => 'Nomor telepon ini sudah digunakan oleh pengguna lain.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'is_active' => (bool) $validated['is_active'],
        ];

        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('user.index')
            ->with('success', "Data pengguna '{$user->name}' berhasil diperbarui.");
    }

    /**
     * Hapus data user dari sistem (Cek riwayat terlebih dahulu).
     */
    public function destroy(int $id)
    {
        $this->authorizeSuperAdmin();

        $user = User::findOrFail($id);
        $userName = $user->name;

        // 1. Cek apakah user pernah/masih memiliki laporan sampah
        $hasReports = DB::table('waste_reports')
            ->where('reported_by', $user->id)
            ->exists();

        if ($hasReports) {
            return redirect()->route('user.index')
                ->with('error', "Gagal menghapus '{$userName}'. Pengguna tidak dapat dihapus karena memiliki riwayat/laporan sampah dalam sistem.");
        }

        // 2. Cek apakah user terikat sebagai petugas/pekerja
        $hasTask = DB::table('cleanup_task_workers')
            ->where('worker_id', $user->id)
            ->exists();

        if ($hasTask) {
            return redirect()->route('user.index')
                ->with('error', "Gagal menghapus '{$userName}'. Pengguna masih terikat pada tugas kebersihan.");
        }

        // 3. Hapus jika tidak terikat riwayat apapun
        $user->delete();

        return redirect()->route('user.index')
            ->with('success', "Pengguna '{$userName}' berhasil dihapus.");
    }

    /**
     * Otorisasi Super Admin Kecamatan.
     */
    private function authorizeSuperAdmin(): void
    {
        if (! Auth::user()->hasRole('super_admin_kecamatan')) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengelola data masyarakat.');
        }
    }
}