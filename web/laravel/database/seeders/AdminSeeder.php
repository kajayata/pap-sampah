<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Village;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'super_admin_kecamatan')->firstOrFail();
        $adminDesaRole = Role::where('name', 'admin_desa')->firstOrFail();

        // 1. Super Admin Kecamatan Sumbersari
        User::updateOrCreate(
            ['email' => 'kec.sumbersari@papsampah.id'],
            [
                'role_id' => $superAdminRole->id,
                'name' => 'Super Admin Kecamatan Sumbersari',
                'password' => Hash::make('password123'),
                'phone' => '081234567890',
                'is_active' => true,
            ]
        );

        // Hapus akun lama yang tidak digunakan
        User::whereIn('email', [
            'superadmin@papsampah.id',
            'admin@sukodono.id',
            'admin@kaliwates.id',
        ])->delete();

        // 2. Admin 7 Kelurahan di Kecamatan Sumbersari
        $kelurahans = [
            ['name' => 'Antirogo', 'email' => 'kel.antirogo@papsampah.id', 'phone' => '081234567801'],
            ['name' => 'Karangrejo', 'email' => 'kel.karangrejo@papsampah.id', 'phone' => '081234567802'],
            ['name' => 'Kebonsari', 'email' => 'kel.kebonsari@papsampah.id', 'phone' => '081234567803'],
            ['name' => 'Kranjingan', 'email' => 'kel.kranjingan@papsampah.id', 'phone' => '081234567804'],
            ['name' => 'Tegalgede', 'email' => 'kel.tegalgede@papsampah.id', 'phone' => '081234567805'],
            ['name' => 'Wirolegi', 'email' => 'kel.wirolegi@papsampah.id', 'phone' => '081234567806'],
            ['name' => 'Sumbersari', 'email' => 'kel.sumbersari@papsampah.id', 'phone' => '081234567807'],
        ];

        foreach ($kelurahans as $item) {
            $village = Village::where('name', $item['name'])->first();

            User::updateOrCreate(
                ['email' => $item['email']],
                [
                    'role_id' => $adminDesaRole->id,
                    'village_id' => $village?->id,
                    'name' => 'Admin Kelurahan ' . $item['name'],
                    'password' => Hash::make('password123'),
                    'phone' => $item['phone'],
                    'is_active' => true,
                ]
            );
        }
    }
}
