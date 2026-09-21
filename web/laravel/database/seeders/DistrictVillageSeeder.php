<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictVillageSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat atau update Kecamatan Sumbersari
        $districtBoundaryWkt = 'MULTIPOLYGON(((113.695 -8.140, 113.765 -8.140, 113.765 -8.220, 113.695 -8.220, 113.695 -8.140)))';

        $district = DB::table('districts')->where('code', '35.09.20')->first();
        if (! $district) {
            $districtId = DB::table('districts')->insertGetId([
                'name' => 'Kecamatan Sumbersari',
                'code' => '35.09.20',
                'boundary' => DB::raw("ST_Multi(ST_GeomFromText('{$districtBoundaryWkt}', 4326))"),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $districtId = $district->id;
            DB::table('districts')->where('id', $districtId)->update([
                'name' => 'Kecamatan Sumbersari',
                'boundary' => DB::raw("ST_Multi(ST_GeomFromText('{$districtBoundaryWkt}', 4326))"),
                'is_active' => true,
                'updated_at' => now(),
            ]);
        }

        // 2. Data 7 Kelurahan di Kecamatan Sumbersari
        $villages = [
            [
                'name' => 'Antirogo',
                'code' => '35.09.20.1001',
                'wkt' => 'MULTIPOLYGON(((113.730 -8.140, 113.765 -8.140, 113.765 -8.165, 113.735 -8.165, 113.730 -8.155, 113.730 -8.140)))',
            ],
            [
                'name' => 'Tegalgede',
                'code' => '35.09.20.1002',
                'wkt' => 'MULTIPOLYGON(((113.705 -8.140, 113.730 -8.140, 113.730 -8.155, 113.730 -8.165, 113.705 -8.165, 113.705 -8.140)))',
            ],
            [
                'name' => 'Sumbersari',
                'code' => '35.09.20.1003',
                'wkt' => 'MULTIPOLYGON(((113.705 -8.165, 113.735 -8.165, 113.735 -8.180, 113.718 -8.180, 113.705 -8.175, 113.705 -8.165)))',
            ],
            [
                'name' => 'Wirolegi',
                'code' => '35.09.20.1004',
                'wkt' => 'MULTIPOLYGON(((113.735 -8.165, 113.765 -8.165, 113.765 -8.195, 113.735 -8.195, 113.735 -8.165)))',
            ],
            [
                'name' => 'Kebonsari',
                'code' => '35.09.20.1005',
                'wkt' => 'MULTIPOLYGON(((113.695 -8.175, 113.718 -8.180, 113.715 -8.200, 113.695 -8.195, 113.695 -8.175)))',
            ],
            [
                'name' => 'Karangrejo',
                'code' => '35.09.20.1006',
                'wkt' => 'MULTIPOLYGON(((113.718 -8.180, 113.735 -8.180, 113.735 -8.195, 113.735 -8.205, 113.715 -8.205, 113.715 -8.200, 113.718 -8.180)))',
            ],
            [
                'name' => 'Kranjingan',
                'code' => '35.09.20.1007',
                'wkt' => 'MULTIPOLYGON(((113.710 -8.200, 113.735 -8.205, 113.750 -8.205, 113.750 -8.220, 113.710 -8.220, 113.710 -8.200)))',
            ],
        ];

        foreach ($villages as $v) {
            $existing = DB::table('villages')->where('code', $v['code'])->first();
            if (! $existing) {
                DB::table('villages')->insert([
                    'district_id' => $districtId,
                    'name' => $v['name'],
                    'code' => $v['code'],
                    'boundary' => DB::raw("ST_Multi(ST_GeomFromText('{$v['wkt']}', 4326))"),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('villages')->where('id', $existing->id)->update([
                    'name' => $v['name'],
                    'district_id' => $districtId,
                    'boundary' => DB::raw("ST_Multi(ST_GeomFromText('{$v['wkt']}', 4326))"),
                    'is_active' => true,
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
