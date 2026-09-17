<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'super_admin_kecamatan')->first();
        $adminDesaRole = Role::where('name', 'admin_desa')->first();

        // Super Admin Kecamatan
        User::updateOrCreate(
            ['email' => 'superadmin@papsampah.id'],
            [
                'role_id' => $superAdminRole->id,
                'name' => 'Super Admin Kecamatan',
                'password' => Hash::make('password123'),
                'phone' => '081234567890',
                'is_active' => true,
            ]
        );

        // Admin Desa (contoh: Desa Sukodono)
        User::updateOrCreate(
            ['email' => 'admin@sukodono.id'],
            [
                'role_id' => $adminDesaRole->id,
                'name' => 'Admin Desa Sukodono',
                'password' => Hash::make('password123'),
                'phone' => '081234567891',
                'is_active' => true,
            ]
        );

        // Admin Desa kedua (contoh: Desa Kaliwates)
        User::updateOrCreate(
            ['email' => 'admin@kaliwates.id'],
            [
                'role_id' => $adminDesaRole->id,
                'name' => 'Admin Desa Kaliwates',
                'password' => Hash::make('password123'),
                'phone' => '081234567892',
                'is_active' => true,
            ]
        );
    }
}
