<?php

namespace Database\Seeders;

use App\Models\Landfill;
use App\Models\News;
use App\Models\User;
use App\Models\Village;
use App\Models\WasteBank;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Fase4MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $villages = Village::pluck('id', 'name')->toArray();
        $superAdmin = User::where('email', 'kec.sumbersari@papsampah.id')->first();

        // 1. Seed Waste Banks (1 for each of 7 villages in Sumbersari)
        $wasteBanks = [
            [
                'village_name' => 'Sumbersari',
                'name' => 'Bank Sampah Resik Sumbersari',
                'address' => 'Jl. Danau Toba No. 12, Kelurahan Sumbersari',
                'phone' => '08123456701',
                'description' => 'Menerima tabungan sampah plastik, kertas kardus, logam, dan minyak jelantah. Buka setiap Senin - Sabtu 08.00 - 15.00 WIB.',
                'lat' => -8.1725,
                'lng' => 113.7160,
            ],
            [
                'village_name' => 'Tegalgede',
                'name' => 'Bank Sampah Mandiri Tegalgede',
                'address' => 'Jl. Mastrip Timur No. 45, Kelurahan Tegalgede',
                'phone' => '08123456702',
                'description' => 'Pusat daur ulang sampah anorganik dan pembinaan kompos warga. Buka setiap hari kerja.',
                'lat' => -8.1630,
                'lng' => 113.7220,
            ],
            [
                'village_name' => 'Kebonsari',
                'name' => 'Bank Sampah Berkah Kebonsari',
                'address' => 'Jl. Letjen Panjaitan No. 88, Kelurahan Kebonsari',
                'phone' => '08123456703',
                'description' => 'Kemitraan PKK Kebonsari untuk konversi sampah botol dan kardus menjadi saldo tabungan belanja.',
                'lat' => -8.1810,
                'lng' => 113.7120,
            ],
            [
                'village_name' => 'Antirogo',
                'name' => 'Bank Sampah Hijau Antirogo',
                'address' => 'Jl. Ki Hajar Dewantara No. 19, Kelurahan Antirogo',
                'phone' => '08123456704',
                'description' => 'Unit bank sampah ramah lingkungan berfokus pada pengolahan sampah kering dan limbah pertanian.',
                'lat' => -8.1510,
                'lng' => 113.7380,
            ],
            [
                'village_name' => 'Karangrejo',
                'name' => 'Bank Sampah Sejahtera Karangrejo',
                'address' => 'Jl. Tawang Mangu No. 33, Kelurahan Karangrejo',
                'phone' => '08123456705',
                'description' => 'Melayani penimbangan sampah terpilah warga Karangrejo, bekerjasama dengan pengepul resmi.',
                'lat' => -8.1920,
                'lng' => 113.7250,
            ],
            [
                'village_name' => 'Kranjingan',
                'name' => 'Bank Sampah Asri Kranjingan',
                'address' => 'Jl. Wolter Monginsidi No. 50, Kelurahan Kranjingan',
                'phone' => '08123456706',
                'description' => 'Fasilitas bank sampah komunitas warga Kranjingan untuk lingkungan bersih bebas timbunan liar.',
                'lat' => -8.2010,
                'lng' => 113.7350,
            ],
            [
                'village_name' => 'Wirolegi',
                'name' => 'Bank Sampah Harmoni Wirolegi',
                'address' => 'Jl. MT Haryono No. 76, Kelurahan Wirolegi',
                'phone' => '08123456707',
                'description' => 'Pengolahan kresek, botol kaca, serta pembuatan eco-brick edukatif bagi sekolah dan warga.',
                'lat' => -8.1780,
                'lng' => 113.7420,
            ],
        ];

        foreach ($wasteBanks as $wb) {
            $villageId = $villages[$wb['village_name']] ?? null;
            if (!$villageId) continue;

            $exists = WasteBank::where('name', $wb['name'])->first();
            if (!$exists) {
                DB::statement("
                    INSERT INTO waste_banks (village_id, name, address, phone, description, is_active, location)
                    VALUES (?, ?, ?, ?, ?, true, ST_SetSRID(ST_Point(?, ?), 4326))
                ", [
                    $villageId,
                    $wb['name'],
                    $wb['address'],
                    $wb['phone'],
                    $wb['description'],
                    $wb['lng'],
                    $wb['lat'],
                ]);
            }
        }

        // 2. Seed Landfill (TPA Rujukan Sumbersari)
        $landfills = [
            [
                'village_name' => 'Wirolegi',
                'name' => 'TPA Pakusari (Rujukan Sumbersari)',
                'address' => 'Kecamatan Pakusari, Perbatasan Timur Sumbersari, Jember',
                'description' => 'Tempat Pemrosesan Akhir sampah resmi terpadu Kabupaten Jember yang melayani wilayah Kecamatan Sumbersari.',
                'lat' => -8.1880,
                'lng' => 113.7650,
            ],
            [
                'village_name' => 'Sumbersari',
                'name' => 'TPS-3R Terpadu Sumbersari',
                'address' => 'Kawasan Industri Kreatif, Kelurahan Sumbersari',
                'description' => 'Tempat Pengolahan Sampah Reuse-Reduce-Recycle tingkat kecamatan dengan mesin pencacah plastik organik.',
                'lat' => -8.1710,
                'lng' => 113.7190,
            ],
        ];

        foreach ($landfills as $lf) {
            $villageId = $villages[$lf['village_name']] ?? array_values($villages)[0];
            $exists = Landfill::where('name', $lf['name'])->first();
            if (!$exists) {
                DB::statement("
                    INSERT INTO landfills (village_id, name, address, description, is_active, location)
                    VALUES (?, ?, ?, ?, true, ST_SetSRID(ST_Point(?, ?), 4326))
                ", [
                    $villageId,
                    $lf['name'],
                    $lf['address'],
                    $lf['description'],
                    $lf['lng'],
                    $lf['lat'],
                ]);
            }
        }

        // 3. Seed Environmental News / Educational Articles
        if ($superAdmin) {
            $articles = [
                [
                    'title' => 'Pilah Sampah dari Rumah: Langkah Nyata Warga Sumbersari Cegah Banjir',
                    'slug' => 'pilah-sampah-dari-rumah-langkah-nyata-warga-sumbersari',
                    'content' => "Memilah sampah dari rumah tangga menjadi kunci keberhasilan pengelolaan kebersihan di Kecamatan Sumbersari.\n\nDengan memisahkan antara sampah organik (sisa makanan, daun) dan sampah anorganik (plastik, botol, kardus), kita dapat mengurangi beban tempat pembuangan akhir hingga 60%.\n\nMari aktifkan pemilahan di rumah kita masing-masing demi Jember yang lebih asri dan lestari!",
                    'status' => News::STATUS_PUBLISHED,
                    'published_at' => now()->subDays(2),
                ],
                [
                    'title' => 'Mengenal Bank Sampah: Menabung Sampah Plastik Menjadi Berkah Finansial',
                    'slug' => 'mengenal-bank-sampah-menabung-sampah-plastik-menjadi-berkah',
                    'content' => "Kecamatan Sumbersari kini telah memiliki unit bank sampah aktif di setiap kelurahan.\n\nMelalui program bank sampah, warga dapat membawa sampah bernilai ekonomis seperti botol mineral, kardus bekas, dan kaleng untuk ditimbang dan dicatat menjadi saldo buku tabungan.\n\nSegera hubungi pengurus RT/RW atau kunjungi bank sampah terdekat di kelurahan Anda!",
                    'status' => News::STATUS_PUBLISHED,
                    'published_at' => now()->subDay(),
                ],
                [
                    'title' => 'Gerakan Tanggap Sampah Liar Bersama Aplikasi Pap Sampah',
                    'slug' => 'gerakan-tanggap-sampah-liar-bersama-aplikasi-pap-sampah',
                    'content' => "Aplikasi Pap Sampah resmi diluncurkan untuk mempermudah masyarakat melaporkan tumpukan sampah liar secara cepat dan akurat menggunakan kamera ponsel dan GPS otomatis.\n\nSetiap laporan yang masuk akan langsung divalidasi oleh pihak kelurahan dan ditindaklanjuti oleh petugas kebersihan desa hingga bersih tuntas.\n\nLaporkan sampah liar di lingkungan Anda sekarang juga!",
                    'status' => News::STATUS_PUBLISHED,
                    'published_at' => now(),
                ],
            ];

            foreach ($articles as $art) {
                News::firstOrCreate(
                    ['slug' => $art['slug']],
                    [
                        'title' => $art['title'],
                        'content' => $art['content'],
                        'author_id' => $superAdmin->id,
                        'status' => $art['status'],
                        'published_at' => $art['published_at'],
                    ]
                );
            }
        }
    }
}
