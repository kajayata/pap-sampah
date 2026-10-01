<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class VillageAdminController extends Controller
{
    private function authorizeSuperAdmin(): void
    {
        abort_unless(
            auth()->user()->hasRole('super_admin_kecamatan'),
            403
        );
    }

    private function expectedEmailForVillage(Village $village): string
    {
        $villageName = Str::lower(
            Str::slug($village->name, '')
        );

        return 'kel.' . $villageName . '@papsampah.id';
    }

    public function index()
    {
        $this->authorizeSuperAdmin();

        $admins = User::with(['village', 'role'])
            ->whereHas('role', function ($query) {
                $query->where('name', 'admin_desa');
            })
            ->orderBy('name')
            ->get();

        return view('village_admins.index', compact('admins'));
    }

    public function create()
    {
        $this->authorizeSuperAdmin();

        $villages = Village::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('village_admins.create', compact('villages'));
    }

    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        /*
        |--------------------------------------------------------------------------
        | Cek semua data kosong
        |--------------------------------------------------------------------------
        */
        if (
            !$request->filled('village_id') &&
            !$request->filled('name') &&
            !$request->filled('email') &&
            !$request->filled('phone') &&
            !$request->filled('password') &&
            !$request->filled('password_confirmation')
        ) {
            return back()
                ->withErrors([
                    'form' => 'Silakan lengkapi semua data.',
                ])
                ->withInput();
        }

        $adminRole = Role::where('name', 'admin_desa')->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Validasi data
        |--------------------------------------------------------------------------
        */
        $validated = $request->validate(
            [
                'village_id' => [
                    'required',
                    'integer',
                    Rule::exists('villages', 'id')
                        ->where(fn ($query) => $query->where('is_active', true)),
                ],

                'name' => [
                    'required',
                    'string',
                    'max:30',
                    'regex:/^[\pL ]+$/u',
                ],

                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:30',
                    'unique:users,email',
                ],

                'phone' => [
                    'required',
                    'string',
                    'max:15',
                    'min:12',
                    'regex:/^[0-9]+$/',
                    'unique:users,phone',
                ],

                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'regex:/^[^\s]+$/u',
                ],

                'password_confirmation' => [
                    'required',
                    'same:password',
                ],
            ],
            [
                'village_id.required' => 'Kelurahan wajib dipilih.',
                'village_id.exists' => 'Kelurahan tidak valid atau sudah tidak aktif.',

                'name.required' => 'Nama wajib diisi.',
                'name.max' => 'Nama maksimal 30 karakter.',
                'name.regex' => 'Nama hanya boleh berisi huruf dan spasi.',

                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.max' => 'Email maksimal 30 karakter.',
                'email.unique' => 'Email sudah digunakan.',

                'phone.required' => 'Nomor telepon wajib diisi.',
                'phone.max' => 'Nomor telepon maksimal 15 digit.',
                'phone.min' => 'Nomor telepon minimal 12 digit.',
                'phone.regex' => 'Nomor telepon hanya boleh berisi angka.',
                'phone.unique' => 'Nomor telepon sudah terdaftar.',

                'password.required' => 'Password wajib diisi.',
                'password.min' => 'Password minimal 8 karakter.',
                'password.regex' => 'Password hanya boleh berisi huruf, angka, dan simbol tanpa spasi.',

                'password_confirmation.required' => 'Konfirmasi password wajib diisi.',
                'password_confirmation.same' => 'Konfirmasi password tidak sama.',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Ambil kelurahan yang dipilih
        |--------------------------------------------------------------------------
        */
        $village = Village::where('id', $validated['village_id'])
            ->where('is_active', true)
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Validasi sinkronisasi email dengan kelurahan
        |--------------------------------------------------------------------------
        */
        $expectedEmail = $this->expectedEmailForVillage($village);

        if ($validated['email'] !== $expectedEmail) {
            return back()
                ->withErrors([
                    'email' => 'Nama email tidak sinkron.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Satu kelurahan hanya boleh memiliki satu admin
        |--------------------------------------------------------------------------
        */
        $alreadyExists = User::where('role_id', $adminRole->id)
            ->where('village_id', $validated['village_id'])
            ->exists();

        if ($alreadyExists) {
            return back()
                ->withErrors([
                    'village_id' => 'Kelurahan tersebut sudah memiliki akun admin.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Simpan akun
        |--------------------------------------------------------------------------
        */
        User::create([
            'role_id' => $adminRole->id,
            'village_id' => $validated['village_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'phone' => $validated['phone'],
            'is_active' => true,
        ]);

        return redirect()
            ->route('village-admins.index')
            ->with('success', 'Akun admin kelurahan berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $this->authorizeSuperAdmin();

        abort_unless($user->isVillageAdmin(), 404);

        /*
        |--------------------------------------------------------------------------
        | Akun nonaktif tidak boleh diedit
        |--------------------------------------------------------------------------
        */
        if (!$user->is_active) {
            return redirect()
                ->route('village-admins.index')
                ->withErrors([
                    'form' => 'Akun yang nonaktif tidak dapat diedit.',
                ]);
        }

        $villages = Village::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('village_admins.edit', compact('user', 'villages'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeSuperAdmin();

        abort_unless($user->isVillageAdmin(), 404);

        /*
        |--------------------------------------------------------------------------
        | Akun nonaktif tidak boleh diedit
        |--------------------------------------------------------------------------
        |
        | Pengecekan ini penting agar akun nonaktif tidak bisa diubah
        | walaupun URL update dipanggil secara manual.
        |
        */
        if (!$user->is_active) {
            return redirect()
                ->route('village-admins.index')
                ->withErrors([
                    'form' => 'Akun yang nonaktif tidak dapat diedit.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Cek semua data kosong
        |--------------------------------------------------------------------------
        */
        if (
            !$request->filled('village_id') &&
            !$request->filled('name') &&
            !$request->filled('email') &&
            !$request->filled('phone') &&
            !$request->filled('password') &&
            !$request->filled('password_confirmation')
        ) {
            return back()
                ->withErrors([
                    'form' => 'Silakan lengkapi semua data.',
                ])
                ->withInput();
        }

        $adminRole = Role::where('name', 'admin_desa')->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Validasi data
        |--------------------------------------------------------------------------
        */
        $validated = $request->validate(
            [
                'village_id' => [
                    'required',
                    'integer',
                    Rule::exists('villages', 'id')
                        ->where(fn ($query) => $query->where('is_active', true)),
                ],

                'name' => [
                    'required',
                    'string',
                    'max:30',
                    'regex:/^[\pL ]+$/u',
                ],

                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:30',
                    Rule::unique('users', 'email')->ignore($user->id),
                ],

                'phone' => [
                    'required',
                    'string',
                    'max:15',
                    'regex:/^[0-9]+$/',
                    Rule::unique('users', 'phone')->ignore($user->id),
                ],

                'password' => [
                    'nullable',
                    'string',
                    'min:8',
                    'regex:/^[^\s]+$/u',
                ],

                'password_confirmation' => [
                    'nullable',
                    'required_with:password',
                    'same:password',
                ],
            ],
            [
                'village_id.required' => 'Kelurahan wajib dipilih.',
                'village_id.exists' => 'Kelurahan tidak valid atau sudah tidak aktif.',

                'name.required' => 'Nama wajib diisi.',
                'name.max' => 'Nama maksimal 30 karakter.',
                'name.regex' => 'Nama hanya boleh berisi huruf dan spasi.',

                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.max' => 'Email maksimal 30 karakter.',
                'email.unique' => 'Email sudah digunakan.',

                'phone.required' => 'Nomor telepon wajib diisi.',
                'phone.max' => 'Nomor telepon maksimal 15 digit.',
                'phone.regex' => 'Nomor telepon hanya boleh berisi angka.',
                'phone.unique' => 'Nomor telepon sudah terdaftar.',

                'password.min' => 'Password minimal 8 karakter.',
                'password.regex' => 'Password minimal 8 karakter dan tidak boleh mengandung spasi.',

                'password_confirmation.required_with' => 'Konfirmasi password wajib diisi.',
                'password_confirmation.same' => 'Konfirmasi password tidak sama.',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Ambil kelurahan yang dipilih dan harus aktif
        |--------------------------------------------------------------------------
        */
        $village = Village::where('id', $validated['village_id'])
            ->where('is_active', true)
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Validasi email harus sesuai kelurahan
        |--------------------------------------------------------------------------
        */
        $expectedEmail = $this->expectedEmailForVillage($village);

        if ($validated['email'] !== $expectedEmail) {
            return back()
                ->withErrors([
                    'email' => 'Nama email tidak sinkron.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Satu kelurahan hanya boleh memiliki satu admin
        |--------------------------------------------------------------------------
        */
        $alreadyExists = User::where('role_id', $adminRole->id)
            ->where('village_id', $validated['village_id'])
            ->where('id', '!=', $user->id)
            ->exists();

        if ($alreadyExists) {
            return back()
                ->withErrors([
                    'village_id' => 'Kelurahan tersebut sudah memiliki akun admin.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Update data
        |--------------------------------------------------------------------------
        */
        $user->update([
            'village_id' => $validated['village_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Update password jika diisi
        |--------------------------------------------------------------------------
        */
        if (!empty($validated['password'])) {
            $user->update([
                'password' => $validated['password'],
            ]);
        }

        return redirect()
            ->route('village-admins.index')
            ->with('success', 'Akun admin kelurahan berhasil diperbarui.');
    }

    public function toggleStatus(User $user)
    {
        $this->authorizeSuperAdmin();

        abort_unless($user->isVillageAdmin(), 404);

        $user->update([
            'is_active' => !$user->is_active,
        ]);

        return redirect()
            ->route('village-admins.index')
            ->with(
                'success',
                'Status akun berhasil diubah menjadi ' .
                ($user->is_active ? 'aktif.' : 'nonaktif.')
            );
    }
}
