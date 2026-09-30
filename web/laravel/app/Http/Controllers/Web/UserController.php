<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Tampilkan daftar user (masyarakat/warga).
     */
    public function index(Request $request)
    {
        $this->authorizeSuperAdmin();

        // Cari role user biasa / warga
        $userRole = Role::where('name', 'user')->first();

        $query = User::query();

        // Jika ada role 'user', filter hanya menampilkan user biasa
        if ($userRole) {
            $query->where('role_id', $userRole->id);
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

    public function toggleStatus($id)
        {
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
     * Update data user dengan validasi ketat.
     */
    public function update(Request $request, int $id)
    {
        $this->authorizeSuperAdmin();

        $user = User::findOrFail($id);

        $rules = [
            // 1. Nama wajib string & tidak boleh diawali angka
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s][a-zA-Z0-9\s._-]*$/'
            ],
            // 2. Email wajib berakhiran @papsampah.id dan unik (mengabaikan ID user ini)
            'email' => [
                'required',
                'string',
                'email:rfc,dns', // Memastikan domain email valid dan aktif
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id)
            ],
            // 3. Phone wajib diawali 08, berupa angka, dan unik
            'phone' => [
                'required',
                'string',
                'numeric',
                'digits_between:10,15',
                'regex:/^08[0-9]+$/',
                Rule::unique('users', 'phone')->ignore($user->id)
            ],
            'is_active' => ['required', 'boolean'],
        ];

        // Jika password diisi, lakukan validasi ganti password
        if ($request->filled('password')) {
            $rules['password'] = ['string', 'min:8', 'confirmed'];
        }

        $validated = $request->validate($rules, [
            'name.regex' => 'Nama pengguna tidak boleh diawali dengan angka.',
            'email.ends_with' => 'Email harus menggunakan domain @papsampah.id.',
            'email.unique' => 'Email ini sudah digunakan oleh pengguna lain.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.numeric' => 'Nomor telepon harus berupa angka.',
            'phone.digits_between' => 'Nomor telepon harus berisi antara 10 hingga 15 digit.',
            'phone.regex' => 'Nomor telepon harus diawali dengan 08.',
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
            $updateData['password'] = $validated['password'];
        }

        $user->update($updateData);

        return redirect()->route('user.index')
            ->with('success', "Data pengguna '{$user->name}' berhasil diperbarui.");
    }

    /**
     * Hapus data user dari sistem.
     */
    public function destroy(int $id)
    {
        $this->authorizeSuperAdmin();

        $user = User::findOrFail($id);
        $userName = $user->name;

        $user->delete();

        return redirect()->route('user.index')
            ->with('success', "Pengguna '{$userName}' berhasil dihapus dari sistem.");
    }

    /**
     * Pastikan hanya Super Admin Kecamatan yang dapat mengakses controller ini.
     */
    private function authorizeSuperAdmin(): void
    {
        if (! Auth::user()->hasRole('super_admin_kecamatan')) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengelola data masyarakat.');
        }
    }
}