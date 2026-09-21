<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Village;
use App\Models\WasteCategory;
use App\Models\WasteReport;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SampleWasteDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Kategori Sampah
        $categories = [
            ['name' => 'Organik', 'description' => 'Sisa makanan, daun, ranting, dan sampah mudah terurai'],
            ['name' => 'Anorganik', 'description' => 'Plastik, kardus, botol kaca, kaleng, dan bahan daur ulang'],
            ['name' => 'B3', 'description' => 'Bahan berbahaya dan beracun, baterai, limbah elektronik'],
            ['name' => 'Liar / Campuran', 'description' => 'Timbunan sampah liar campuran di pinggir jalan atau lahan kosong'],
        ];

        foreach ($categories as $cat) {
            WasteCategory::updateOrCreate(['name' => $cat['name']], [
                'description' => $cat['description'],
                'is_active' => true,
            ]);
        }

        $petugasRole = Role::where('name', 'petugas_desa')->firstOrFail();
        $masyarakatRole = Role::where('name', 'masyarakat')->firstOrFail();

        $sumbersari = Village::where('name', 'Sumbersari')->first();
        $antirogo = Village::where('name', 'Antirogo')->first();
        $kebonsari = Village::where('name', 'Kebonsari')->first();

        // 2. Sample Petugas Kebersihan
        if ($sumbersari) {
            User::updateOrCreate(['email' => 'petugas1.sumbersari@papsampah.id'], [
                'role_id' => $petugasRole->id,
                'village_id' => $sumbersari->id,
                'name' => 'Budi Santoso (Petugas Sumbersari)',
                'password' => Hash::make('password123'),
                'phone' => '082111222333',
                'is_active' => true,
            ]);

            User::updateOrCreate(['email' => 'petugas2.sumbersari@papsampah.id'], [
                'role_id' => $petugasRole->id,
                'village_id' => $sumbersari->id,
                'name' => 'Agus Wicaksono (Petugas Sumbersari)',
                'password' => Hash::make('password123'),
                'phone' => '082111222334',
                'is_active' => true,
            ]);
        }

        if ($antirogo) {
            User::updateOrCreate(['email' => 'petugas1.antirogo@papsampah.id'], [
                'role_id' => $petugasRole->id,
                'village_id' => $antirogo->id,
                'name' => 'Slamet Riyadi (Petugas Antirogo)',
                'password' => Hash::make('password123'),
                'phone' => '082111222335',
                'is_active' => true,
            ]);
        }

        // 3. Sample User Masyarakat Pelapor
        $reporter = User::updateOrCreate(['email' => 'warga.sumbersari@example.com'], [
            'role_id' => $masyarakatRole->id,
            'village_id' => $sumbersari?->id,
            'name' => 'Ahmad Warga',
            'password' => Hash::make('password123'),
            'phone' => '081399887766',
            'is_active' => true,
        ]);

        // 4. Sample Laporan Sampah jika belum ada
        if ($sumbersari && WasteReport::count() === 0) {
            $catLiar = WasteCategory::where('name', 'Liar / Campuran')->first();

            $reports = [
                [
                    'code' => 'REP-202609-0001',
                    'village_id' => $sumbersari->id,
                    'desc' => 'Tumpukan sampah plastik dan daun di dekat jembatan Mastrip',
                    'status' => WasteReport::STATUS_PENDING_VALIDATION,
                    'lon' => 113.7188,
                    'lat' => -8.1725,
                ],
                [
                    'code' => 'REP-202609-0002',
                    'village_id' => $sumbersari->id,
                    'desc' => 'Sampah liar di lahan kosong jalan Karimata',
                    'status' => WasteReport::STATUS_ASSIGNED,
                    'lon' => 113.7150,
                    'lat' => -8.1710,
                ],
                [
                    'code' => 'REP-202609-0003',
                    'village_id' => $sumbersari->id,
                    'desc' => 'Limbah rumah tangga menumpuk di gang Danau Toba',
                    'status' => WasteReport::STATUS_RESOLVED,
                    'lon' => 113.7220,
                    'lat' => -8.1740,
                ],
            ];

            foreach ($reports as $r) {
                DB::table('waste_reports')->insert([
                    'report_code' => $r['code'],
                    'reported_by' => $reporter->id,
                    'village_id' => $r['village_id'],
                    'category_id' => $catLiar->id,
                    'description' => $r['desc'],
                    'status' => $r['status'],
                    'location' => DB::raw("ST_SetSRID(ST_MakePoint({$r['lon']}, {$r['lat']}), 4326)"),
                    'created_at' => now()->subHours(rand(1, 24)),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
