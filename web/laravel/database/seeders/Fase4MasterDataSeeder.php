<?php

namespace Database\Seeders;

use App\Models\Landfill;
use App\Models\News;
use App\Models\User;
use App\Models\Village;
use App\Models\WasteBank;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
            // Buang artikel berita lama milik seeder ini supaya tidak muncul lagi
            // saat migrate:fresh --seed dijalankan ulang.
            News::whereIn('slug', [
                'pilah-sampah-dari-rumah-langkah-nyata-warga-sumbersari',
                'mengenal-bank-sampah-menabung-sampah-plastik-menjadi-berkah',
                'gerakan-tanggap-sampah-liar-bersama-aplikasi-pap-sampah',
            ])->delete();

            $articles = [
                [
                    'title' => 'Tumpukan Sampah Liar di Sumbersari Berhasil Dibersihkan Petugas',
                    'slug' => 'tumpukan-sampah-liar-di-sumbersari-berhasil-dibersihkan-petugas',
                    'source_image' => '../../sampahTest.jpeg',
                    'thumbnail_storage_key' => 'news-thumbnails/dummy/sampah-test-1.jpg',
                    'content' => "Petugas kebersihan Kelurahan Sumbersari membersihkan titik tumpukan sampah liar yang terdata lewat aplikasi Pap Sampah.\n\nLokasi tersebut sudah ditandai sebagai laporan tuntas, sementara volume sampah langsung dibawa ke TPS-3R Terpadu Sumbersari.\n\nWarga sekitar lokasi diminta menjaga kebersihan jalan dan segera melapor bila menemukan tumpukan baru.",
                    'status' => News::STATUS_PUBLISHED,
                    'published_at' => now()->subDays(2),
                ],
                [
                    'title' => 'Belajar Melaporkan Sampah Liar Lewat Aplikasi Pap Sampah',
                    'slug' => 'belajar-melaporkan-sampah-liar-lewat-aplikasi-pap-sampah',
                    'source_image' => '../../sampahTest2.jpeg',
                    'thumbnail_storage_key' => 'news-thumbnails/dummy/sampah-test-2.jpg',
                    'content' => "Aplikasi Pap Sampah memudahkan warga melaporkan sampah liar cukup dengan foto dan titik lokasi GPS.\n\nLaporan masuk ke admin kelurahan, divalidasi, lalu ditugaskan ke petugas hingga selesai. Semua proses bisa dipantau warga lewat aplikasi.\n\nMari ikut bergerak dari sekarang juga demi Sumbersari yang lebih bersih.",
                    'status' => News::STATUS_DRAFT,
                    'published_at' => null,
                ],
                [
                    'title' => 'Aksi Bersih Sampah Terpilah: Gotong Royong Warga Sumbersari',
                    'slug' => 'aksi-bersih-sampah-terpilah-gotong-royong-warga-sumbersari',
                    'source_image' => '../../BeritaSampah.jpeg',
                    'thumbnail_storage_key' => 'news-thumbnails/dummy/berita-sampah-3.jpg',
                    'content' => "Kegiatan bersih sampah dilakukan bersama warga dan sekolah, dengan sampah langsung dipilah menjadi organik dan anorganik.\n\nSampah anorganik bernilai diserahkan ke bank sampah kelurahan, sementara sampah organik diolah menjadi kompos.\n\nKegiatan ini membuktikan bahwa kekompakan warga adalah kunci menjaga lingkungan Sumbersari.",
                    'status' => News::STATUS_ARCHIVED,
                    'published_at' => now()->subDays(30),
                ],
            ];

            foreach ($articles as $art) {
                // Foto dummy diambil dari root repo (ter-commit di sana), disalin mentah
                // ke public disk supaya tetap tampil setelah migrate:fresh --seed.
                $sourcePath = base_path($art['source_image']);
                if (is_file($sourcePath)) {
                    Storage::disk(config('filesystems.default', 'public'))->put(
                        $art['thumbnail_storage_key'],
                        file_get_contents($sourcePath)
                    );
                }

                unset($art['source_image']);

                News::updateOrCreate(
                    ['slug' => $art['slug']],
                    [
                        'title' => $art['title'],
                        'content' => $art['content'],
                        'thumbnail_storage_key' => $art['thumbnail_storage_key'],
                        'author_id' => $superAdmin->id,
                        'status' => $art['status'],
                        'published_at' => $art['published_at'],
                    ]
                );
            }
        }
    }
}
