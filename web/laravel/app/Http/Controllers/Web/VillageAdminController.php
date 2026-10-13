<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class VillageAdminController extends Controller
{
    private const MAX_ACCOUNTS_PER_VILLAGE = 3;

    private const EMAIL_DOMAIN = '@papsampah.id';

    private function authorizeSuperAdmin(): void
    {
        abort_unless(
            auth()->user()?->hasRole('super_admin_kecamatan'),
            403
        );
    }

    /**
     * Membuat bagian awal email berdasarkan nama kelurahan.
     * Contoh: kel.antirogo
     */
    private function emailPrefixForVillage(Village $village): string
    {
        $villageName = Str::lower(
            Str::slug($village->name, '')
        );

        return 'kel.' . $villageName;
    }

    /**
     * Membuat email dengan nomor urutan akun.
     * Contoh: kel.antirogo1@papsampah.id
     */
    private function expectedEmailForVillage(
        Village $village,
        int $sequence
    ): string {
        return $this->emailPrefixForVillage($village)
            . $sequence
            . self::EMAIL_DOMAIN;
    }

    /**
     * Mengambil nomor urutan dari email akun yang sudah ada.
     *
     * Email lama tanpa angka akan dianggap belum memiliki
     * nomor urutan dan ditangani saat akun diedit.
     */
    private function sequenceFromEmail(
        string $email,
        Village $village
    ): ?int {
        $prefix = preg_quote(
            $this->emailPrefixForVillage($village),
            '/'
        );

        $pattern = '/^' . $prefix
            . '([0-9]+)'
            . preg_quote(self::EMAIL_DOMAIN, '/')
            . '$/i';

        if (preg_match($pattern, $email, $matches) === 1) {
            $sequence = (int) $matches[1];

            return $sequence > 0 ? $sequence : null;
        }

        return null;
    }

    /**
     * Menghitung nomor berikutnya berdasarkan jumlah akun
     * dan nomor terbesar yang pernah digunakan.
     *
     * Nomor yang pernah digunakan tidak dipakai ulang
     * hanya karena sebuah akun dihapus.
     */
    private function nextSequenceForVillage(
        Village $village,
        Role $adminRole
    ): int {
        $admins = User::query()
            ->where('role_id', $adminRole->id)
            ->where('village_id', $village->id)
            ->get(['id', 'email']);

        $highestSequence = 0;

        foreach ($admins as $admin) {
            $sequence = $this->sequenceFromEmail(
                $admin->email,
                $village
            );

            if ($sequence !== null) {
                $highestSequence = max(
                    $highestSequence,
                    $sequence
                );
            }
        }

        return max(
            $admins->count(),
            $highestSequence
        ) + 1;
    }

    /**
     * Menghasilkan email yang belum digunakan.
     */
    private function generateAvailableEmail(
        Village $village,
        int $sequence,
        ?int $ignoreUserId = null
    ): string {
        do {
            $email = $this->expectedEmailForVillage(
                $village,
                $sequence
            );

            $exists = User::query()
                ->where('email', $email)
                ->when(
                    $ignoreUserId !== null,
                    fn ($query) => $query->where(
                        'id',
                        '!=',
                        $ignoreUserId
                    )
                )
                ->exists();

            if ($exists) {
                $sequence++;
            }
        } while ($exists);

        return $email;
    }

    /**
     * Mengambil urutan akun lama berdasarkan waktu pembuatan.
     * Berguna untuk mengonversi email lama tanpa nomor.
     */
    private function sequenceForExistingAccount(
        User $user,
        Village $village,
        Role $adminRole
    ): int {
        $existingSequence = $this->sequenceFromEmail(
            $user->email,
            $village
        );

        if ($existingSequence !== null) {
            return $existingSequence;
        }

        $orderedIds = User::query()
            ->where('role_id', $adminRole->id)
            ->where('village_id', $village->id)
            ->orderBy('created_at')
            ->orderBy('id')
            ->pluck('id')
            ->values();

        $position = $orderedIds->search(
            fn ($id) => (int) $id === (int) $user->id
        );

        if ($position !== false) {
            return $position + 1;
        }

        return $this->nextSequenceForVillage(
            $village,
            $adminRole
        );
    }

    /**
     * Menghitung akun kelurahan pada sebuah kelurahan.
     * Akun nonaktif juga dihitung.
     */
    private function countVillageAdmins(
        int $villageId,
        Role $adminRole,
        ?int $ignoreUserId = null
    ): int {
        return User::query()
            ->where('role_id', $adminRole->id)
            ->where('village_id', $villageId)
            ->when(
                $ignoreUserId !== null,
                fn ($query) => $query->where(
                    'id',
                    '!=',
                    $ignoreUserId
                )
            )
            ->count();
    }

    public function index()
    {
        $this->authorizeSuperAdmin();

        $admins = User::with(['village', 'role'])
            ->whereHas('role', function ($query) {
                $query->where('name', 'admin_desa');
            })
            ->orderBy('village_id')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $accountCounts = $admins
            ->groupBy('village_id')
            ->map(fn ($accounts) => $accounts->count());

        return view(
            'village_admins.index',
            compact('admins', 'accountCounts')
        );
    }

    public function create()
    {
        $this->authorizeSuperAdmin();

        $villages = Village::where('is_active', true)
            ->orderBy('name')
            ->get();

        $adminRole = Role::where(
            'name',
            'admin_desa'
        )->firstOrFail();

        $accountCounts = [];
        $emailPreviews = [];

        foreach ($villages as $village) {
            $accountCounts[$village->id] = $this->countVillageAdmins(
                $village->id,
                $adminRole
            );

            $sequence = $this->nextSequenceForVillage(
                $village,
                $adminRole
            );

            $emailPreviews[$village->id] = $this->generateAvailableEmail(
                $village,
                $sequence
            );
        }

        return view(
            'village_admins.create',
            compact(
                'villages',
                'accountCounts',
                'emailPreviews'
            )
        );
    }

    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        $adminRole = Role::where(
            'name',
            'admin_desa'
        )->firstOrFail();

        $validated = $request->validate(
            [
                'village_id' => [
                    'required',
                    'integer',
                    Rule::exists('villages', 'id')
                        ->where(
                            fn ($query) => $query->where(
                                'is_active',
                                true
                            )
                        ),
                ],

                'name' => [
                    'required',
                    'string',
                    'max:100',
                    'regex:/^[\pL ]+$/u',
                ],

                'phone' => [
                    'required',
                    'string',
                    'min:12',
                    'max:15',
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

                'name.required' => 'Nama pengelola wajib diisi.',
                'name.max' => 'Nama pengelola maksimal 100 karakter.',
                'name.regex' => 'Nama pengelola hanya boleh berisi huruf dan spasi.',

                'phone.required' => 'Nomor telepon wajib diisi.',
                'phone.min' => 'Nomor telepon minimal 12 digit.',
                'phone.max' => 'Nomor telepon maksimal 15 digit.',
                'phone.regex' => 'Nomor telepon hanya boleh berisi angka.',
                'phone.unique' => 'Nomor telepon sudah terdaftar.',

                'password.required' => 'Password wajib diisi.',
                'password.min' => 'Password minimal 8 karakter.',
                'password.regex' => 'Password tidak boleh mengandung spasi.',

                'password_confirmation.required' => 'Konfirmasi password wajib diisi.',
                'password_confirmation.same' => 'Konfirmasi password tidak sama.',
            ]
        );

        /*
         * Kunci baris kelurahan ketika membuat akun,
         * agar dua request bersamaan tidak melewati batas 3 akun.
         */
        $createdEmail = DB::transaction(function () use (
            $validated,
            $adminRole
        ) {
            $village = Village::query()
                ->where('id', $validated['village_id'])
                ->where('is_active', true)
                ->lockForUpdate()
                ->firstOrFail();

            $accountCount = $this->countVillageAdmins(
                $village->id,
                $adminRole
            );

            if ($accountCount >= self::MAX_ACCOUNTS_PER_VILLAGE) {
                throw ValidationException::withMessages([
                    'village_id' => 'Kelurahan ini sudah memiliki maksimal 3 akun admin.',
                ]);
            }

            $sequence = $this->nextSequenceForVillage(
                $village,
                $adminRole
            );

            $email = $this->generateAvailableEmail(
                $village,
                $sequence
            );

            User::create([
                'role_id' => $adminRole->id,
                'village_id' => $village->id,
                'name' => $validated['name'],
                'email' => $email,
                'password' => $validated['password'],
                'phone' => $validated['phone'],
                'is_active' => true,
            ]);

            return $email;
        });

        return redirect()
            ->route('village-admins.index')
            ->with(
                'success',
                "Akun admin kelurahan berhasil ditambahkan. Email: {$createdEmail}"
            );
    }

    public function edit(User $user)
    {
        $this->authorizeSuperAdmin();

        abort_unless($user->isVillageAdmin(), 404);

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

        return view(
            'village_admins.edit',
            compact('user', 'villages')
        );
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeSuperAdmin();

        abort_unless($user->isVillageAdmin(), 404);

        if (!$user->is_active) {
            return redirect()
                ->route('village-admins.index')
                ->withErrors([
                    'form' => 'Akun yang nonaktif tidak dapat diedit.',
                ]);
        }

        $validated = $request->validate(
            [
                'village_id' => [
                    'required',
                    'integer',
                    Rule::exists('villages', 'id')
                        ->where(
                            fn ($query) => $query->where(
                                'is_active',
                                true
                            )
                        ),
                ],

                'name' => [
                    'required',
                    'string',
                    'max:100',
                    'regex:/^[\pL ]+$/u',
                ],

                'phone' => [
                    'required',
                    'string',
                    'min:12',
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

                'name.required' => 'Nama pengelola wajib diisi.',
                'name.max' => 'Nama pengelola maksimal 100 karakter.',
                'name.regex' => 'Nama pengelola hanya boleh berisi huruf dan spasi.',

                'phone.required' => 'Nomor telepon wajib diisi.',
                'phone.min' => 'Nomor telepon minimal 12 digit.',
                'phone.max' => 'Nomor telepon maksimal 15 digit.',
                'phone.regex' => 'Nomor telepon hanya boleh berisi angka.',
                'phone.unique' => 'Nomor telepon sudah terdaftar.',

                'password.min' => 'Password minimal 8 karakter.',
                'password.regex' => 'Password tidak boleh mengandung spasi.',

                'password_confirmation.required_with' => 'Konfirmasi password wajib diisi.',
                'password_confirmation.same' => 'Konfirmasi password tidak sama.',
            ]
        );

        $adminRole = Role::where(
            'name',
            'admin_desa'
        )->firstOrFail();

        DB::transaction(function () use (
            $validated,
            $adminRole,
            $user
        ) {
            $village = Village::query()
                ->where('id', $validated['village_id'])
                ->where('is_active', true)
                ->lockForUpdate()
                ->firstOrFail();

            $sameVillage = (int) $user->village_id === (int) $village->id;

            /*
             * Kalau dipindahkan ke kelurahan lain,
             * pastikan kelurahan tujuan masih punya kapasitas.
             */
            if (!$sameVillage) {
                $accountCount = $this->countVillageAdmins(
                    $village->id,
                    $adminRole
                );

                if ($accountCount >= self::MAX_ACCOUNTS_PER_VILLAGE) {
                    throw ValidationException::withMessages([
                        'village_id' => 'Kelurahan tujuan sudah memiliki maksimal 3 akun admin.',
                    ]);
                }

                $sequence = $this->nextSequenceForVillage(
                    $village,
                    $adminRole
                );
            } else {
                /*
                 * Pertahankan nomor akun yang sudah benar.
                 * Akun lama tanpa nomor akan diberi nomor
                 * berdasarkan urutan pembuatan.
                 */
                $sequence = $this->sequenceForExistingAccount(
                    $user,
                    $village,
                    $adminRole
                );
            }

            $email = $this->generateAvailableEmail(
                $village,
                $sequence,
                $user->id
            );

            $data = [
                'village_id' => $village->id,
                'name' => $validated['name'],
                'email' => $email,
                'phone' => $validated['phone'],
            ];

            if (!empty($validated['password'])) {
                $data['password'] = $validated['password'];
            }

            $user->update($data);
        });

        return redirect()
            ->route('village-admins.index')
            ->with(
                'success',
                "Akun admin kelurahan berhasil diperbarui. Email: {$user->fresh()->email}"
            );
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
                'Status akun berhasil diubah menjadi '
                    . ($user->is_active ? 'aktif.' : 'nonaktif.')
            );
    }
}
