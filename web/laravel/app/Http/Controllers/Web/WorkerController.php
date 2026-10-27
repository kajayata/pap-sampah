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

        // Diubah ke folder 'worker'
        return view('worker.index', compact('workers', 'villages', 'selectedVillage'));
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

        // Diubah ke folder 'worker'
        return view('worker.create', compact('villages'));
    }

    /**
     * Simpan petugas kebersihan baru dengan Auto-Increment Email yang aman.
     */
    public function store(Request $request)
{
    $user = Auth::user();
    $this->authorizeAdmin($user);

    $petugasRole = Role::where('name', 'petugas_desa')->firstOrFail();

    $villageId = $user->hasRole('admin_desa')
        ? $user->village_id
        : $request->village_id;

    $village = Village::findOrFail($villageId);
    $slugVillage = strtolower(str_replace(' ', '', $village->name));

    // Jika email kosong dari form, generate otomatis dengan kombinasi nama + wilayah + random angka
    if (!$request->filled('email')) {
        $nameParts = explode(' ', strtolower(trim($request->name)));
        $firstName = preg_replace('/[^a-z]/', '', $nameParts[0] ?? 'petugas');
        if (empty($firstName)) $firstName = 'petugas';

        do {
            $randomNumber = rand(10, 99);
            $generatedEmail = "{$firstName}.{$slugVillage}{$randomNumber}@papsampah.id";
        } while (User::where('email', $generatedEmail)->exists());

        $request->merge(['email' => $generatedEmail]);
    }

    $validated = $request->validate([
        'name' => [
            'required',
            'string',
            'max:255',
            'regex:/^[a-zA-Z\s]+$/'
        ],
        'email' => [
            'required',
            'string',
            'email',
            'max:255',
            'ends_with:@papsampah.id',
            'unique:users,email'
        ],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
        'phone' => [
            'required',
            'string',
            'numeric',
            'digits_between:10,15',
            'regex:/^08[0-9]+$/',
            'unique:users,phone'
        ],
        'village_id' => $user->hasRole('super_admin_kecamatan')
            ? ['required', 'exists:villages,id']
            : ['nullable'],
    ], [
        'name.regex' => 'Nama petugas tidak boleh diawali dengan angka maupun simbol.',
        'email.unique' => 'Email ini sudah terdaftar dalam sistem.',
        'email.ends_with' => 'Email harus diakhiri dengan @papsampah.id.',
        'phone.required' => 'Nomor telepon wajib diisi.',
        'phone.numeric' => 'Nomor telepon harus berupa angka.',
        'phone.digits_between' => 'Nomor telepon harus berisi antara 10 hingga 15 digit.',
        'phone.regex' => 'Nomor telepon harus diawali dengan 08.',
        'phone.unique' => 'Nomor telepon ini sudah terdaftar dalam sistem.',
        'password.min' => 'Kata sandi minimal 8 karakter.',
        'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
    ]);

    User::create([
        'role_id' => $petugasRole->id,
        'village_id' => $villageId,
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'phone' => $validated['phone'],
        'is_active' => true,
    ]);

    return redirect()->route('worker.index')
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

        // Diubah ke folder 'worker'
        return view('worker.edit', compact('worker', 'villages'));
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
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s][a-zA-Z0-9\s._-]*$/'
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'ends_with:@papsampah.id',
                Rule::unique('users', 'email')->ignore($worker->id)
            ],
            'phone' => [
                'required',
                'string',
                'numeric',
                'digits_between:10,15',
                'regex:/^08[0-9]+$/',
                Rule::unique('users', 'phone')->ignore($worker->id)
            ],
            'is_active' => ['required', 'boolean'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['string', 'min:8', 'confirmed'];
        }

        if ($user->hasRole('super_admin_kecamatan')) {
            $rules['village_id'] = ['required', 'exists:villages,id'];
        }

        $validated = $request->validate($rules, [
            'name.regex' => 'Nama petugas tidak boleh diawali dengan angka maupun simbol.',
            'email.unique' => 'Email ini sudah digunakan oleh pengguna lain.',
            'email.ends_with' => 'Email harus diakhiri dengan @papsampah.id.',
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
            $updateData['password'] = Hash::make($validated['password']);
        }

        if ($user->hasRole('super_admin_kecamatan')) {
            $updateData['village_id'] = $validated['village_id'];
        }

        $worker->update($updateData);

        // Route diarahkan ke worker.index
        return redirect()->route('worker.index')
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
     * Hapus data petugas kebersihan.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $this->authorizeAdmin($user);

        $petugas = User::findOrFail($id);

        if (!$petugas->hasRole('petugas_desa')) {
            // Route diarahkan ke worker.index
            return redirect()->route('worker.index')
                ->with('error', 'Akun tersebut bukan petugas kebersihan.');
        }

        $namaPetugas = $petugas->name;

        try {
            $petugas->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            // Kode error 23503 pada PostgreSQL menandakan adanya pelanggaran Foreign Key Constraint
            if ($e->getCode() == '23503') {
                // Route diarahkan ke worker.index
                return redirect()->route('worker.index')
                    ->with('error', "Petugas '{$namaPetugas}' tidak dapat dihapus karena masih memiliki riwayat aktivitas atau laporan dalam sistem. Sebagai alternatif, silakan nonaktifkan akun ini.");
            }
            
            throw $e;
        }

        // Route diarahkan ke worker.index
        return redirect()->route('worker.index')
            ->with('success', "Petugas kebersihan '{$namaPetugas}' berhasil dihapus.");
    }

    private function authorizeAdmin($user): void
    {
        if (! $user->hasRole('admin_desa') && ! $user->hasRole('super_admin_kecamatan')) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengelola petugas.');
        }
    }

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