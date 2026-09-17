<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class WorkerController extends Controller
{
    /**
     * Tampilkan daftar petugas kebersihan.
     * Admin Desa hanya melihat petugas di desanya.
     * Super Admin Kecamatan dapat melihat seluruh petugas atau memfilter per desa.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $this->authorizeAdmin($user);

        $petugasRole = Role::where('name', 'petugas_desa')->firstOrFail();

        $query = User::with('village')
            ->where('role_id', $petugasRole->id)
            ->withCount([
                'cleanupTaskWorkers as active_tasks_count' => function ($q) {
                    $q->whereIn('status', ['PENDING', 'ACCEPTED']);
                },
                'cleanupTaskWorkers as completed_tasks_count' => function ($q) {
                    $q->where('status', 'COMPLETED');
                },
            ]);

        if ($user->hasRole('admin_desa')) {
            $query->where('village_id', $user->village_id);
            $selectedVillage = $user->village;
            $villages = collect([$selectedVillage]);
        } else {
            // Super Admin Kecamatan
            if ($request->filled('village_id')) {
                $query->where('village_id', $request->village_id);
            }
            $villages = Village::where('is_active', true)->orderBy('name')->get();
            $selectedVillage = $request->filled('village_id')
                ? $villages->firstWhere('id', $request->village_id)
                : null;
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%")
                    ->orWhere('phone', 'ilike', "%{$search}%");
            });
        }

        $workers = $query->latest('id')->paginate(10)->withQueryString();

        return view('petugas.index', compact('workers', 'villages', 'selectedVillage'));
    }

    /**
     * Form tambah petugas kebersihan.
     */
    public function create()
    {
        $user = Auth::user();
        $this->authorizeAdmin($user);

        $villages = $user->hasRole('admin_desa')
            ? collect([$user->village])
            : Village::where('is_active', true)->orderBy('name')->get();

        return view('petugas.create', compact('villages'));
    }

    /**
     * Simpan petugas kebersihan baru.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $this->authorizeAdmin($user);

        $petugasRole = Role::where('name', 'petugas_desa')->firstOrFail();

        $villageId = $user->hasRole('admin_desa')
            ? $user->village_id
            : $request->village_id;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'village_id' => $user->hasRole('super_admin_kecamatan')
                ? ['required', 'exists:villages,id']
                : ['nullable'],
        ], [
            'email.unique' => 'Email ini sudah terdaftar dalam sistem.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        User::create([
            'role_id' => $petugasRole->id,
            'village_id' => $villageId,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('petugas.index')
            ->with('success', "Petugas kebersihan '{$validated['name']}' berhasil ditambahkan.");
    }

    /**
     * Form edit data petugas kebersihan.
     */
    public function edit(int $id)
    {
        $user = Auth::user();
        $this->authorizeAdmin($user);

        $worker = User::with('village')->findOrFail($id);
        $this->validateWorkerScope($user, $worker);

        $villages = $user->hasRole('admin_desa')
            ? collect([$user->village])
            : Village::where('is_active', true)->orderBy('name')->get();

        return view('petugas.edit', compact('worker', 'villages'));
    }

    /**
     * Update data petugas kebersihan.
     */
    public function update(Request $request, int $id)
    {
        $user = Auth::user();
        $this->authorizeAdmin($user);

        $worker = User::findOrFail($id);
        $this->validateWorkerScope($user, $worker);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($worker->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['required', 'boolean'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['string', 'min:8', 'confirmed'];
        }

        if ($user->hasRole('super_admin_kecamatan')) {
            $rules['village_id'] = ['required', 'exists:villages,id'];
        }

        $validated = $request->validate($rules, [
            'email.unique' => 'Email ini sudah digunakan oleh pengguna lain.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => (bool) $validated['is_active'],
        ];

        if ($request->filled('password')) {
            $updateData['password'] = $validated['password'];
        }

        if ($user->hasRole('super_admin_kecamatan')) {
            $updateData['village_id'] = $validated['village_id'];
        }

        $worker->update($updateData);

        return redirect()->route('petugas.index')
            ->with('success', "Data petugas '{$worker->name}' berhasil diperbarui.");
    }

    /**
     * Toggle status aktif / nonaktif petugas kebersihan.
     */
    public function toggleStatus(int $id)
    {
        $user = Auth::user();
        $this->authorizeAdmin($user);

        $worker = User::findOrFail($id);
        $this->validateWorkerScope($user, $worker);

        $worker->is_active = ! $worker->is_active;
        $worker->save();

        $statusText = $worker->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "Akun petugas '{$worker->name}' berhasil {$statusText}.");
    }

    /**
     * Pastikan pengguna adalah Admin Desa atau Super Admin.
     */
    private function authorizeAdmin($user): void
    {
        if (! $user->hasRole('admin_desa') && ! $user->hasRole('super_admin_kecamatan')) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengelola petugas.');
        }
    }

    /**
     * Validasi scope wilayah (Invariant #4 & #10): Admin Desa hanya boleh mengelola petugas desanya sendiri.
     */
    private function validateWorkerScope($user, User $worker): void
    {
        if (! $worker->hasRole('petugas_desa')) {
            abort(404, 'Data petugas tidak ditemukan.');
        }

        if ($user->hasRole('admin_desa') && $worker->village_id !== $user->village_id) {
            abort(403, 'Anda tidak memiliki wewenang untuk mengelola petugas di luar wilayah desa Anda.');
        }
    }
}
