<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Cari role 'masyarakat'
        $userRole = Role::whereIn('name', ['masyarakat', 'user'])->first();

        if (!$userRole) {
            $userRole = Role::create(['name' => 'masyarakat']);
        }

        $dummyUsers = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@gmail.com',
                'phone' => '081234567890',
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siti.aminah@yahoo.com',
                'phone' => '085712345678',
            ],
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad.fauzi@outlook.com',
                'phone' => '088198765432',
            ],
        ];

        foreach ($dummyUsers as $userData) {
            // Cegah seeder jika email mengandung domain internal petugas/admin (@papsampah.id)
            if (str_ends_with($userData['email'], '@papsampah.id')) {
                continue; // Lewati data ini jika emailnya milik petugas/admin
            }

            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'phone' => $userData['phone'],
                    'password' => Hash::make('password123'),
                    'role_id' => $userRole->id, // Hanya diset sebagai role masyarakat
                    'is_active' => true,
                ]
            );
        }
    }
}