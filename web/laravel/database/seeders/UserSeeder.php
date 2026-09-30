<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan role 'user' ada
        $userRole = Role::firstOrCreate(['name' => 'user']);

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

        foreach ($dummyUsers as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'role_id' => $userRole->id,
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'password' => Hash::make('password123'),
                    'is_active' => true,
                ]
            );
        }
    }
}