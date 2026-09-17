<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

/**
 * Wajib dijalankan sebelum fitur register bisa dipakai — AuthController::register
 * bergantung pada role "masyarakat" yang sudah ada.
 * Jalankan: php artisan db:seed --class=RoleSeeder
 */
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'super_admin_kecamatan',
            'admin_desa',
            'petugas_desa',
            'masyarakat',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}
