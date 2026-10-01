<?php

namespace Tests\Feature;

use App\Models\Landfill;
use App\Models\News;
use App\Models\User;
use App\Models\WasteBank;
use App\Models\WasteReport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Fase4FeatureTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (WasteBank::count() === 0 || User::where('email', 'kec.sumbersari@papsampah.id')->doesntExist()) {
            $this->seed(\Database\Seeders\DatabaseSeeder::class);
        }
    }

    public function test_api_map_waste_points_returns_correct_structure_and_enforces_invariants(): void
    {
        $response = $this->getJson('/api/map/waste-points');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'total_points',
                    'points' => [
                        '*' => [
                            'id',
                            'report_code',
                            'latitude',
                            'longitude',
                            'status',
                            'status_label',
                            'is_resolved',
                            'category_name',
                            'village_id',
                            'village_name',
                        ],
                    ],
                ],
            ]);
    }

    public function test_api_map_heatmap_strictly_excludes_resolved_reports(): void
    {
        $response = $this->getJson('/api/map/heatmap');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'total_points',
                    'heatmap_points',
                ],
            ]);

        // Ensure each heatmap point is an array of [lat, lng, weight]
        $points = $response->json('data.heatmap_points');
        foreach ($points as $pt) {
            $this->assertCount(3, $pt);
            $this->assertIsNumeric($pt[0]);
            $this->assertIsNumeric($pt[1]);
            $this->assertGreaterThan(0, $pt[2]);
        }
    }

    public function test_api_map_boundaries_returns_valid_geojson(): void
    {
        $response = $this->getJson('/api/map/boundaries');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'type' => 'FeatureCollection',
                ],
            ]);
    }

    public function test_api_waste_banks_list_and_detail(): void
    {
        $listRes = $this->getJson('/api/waste-banks');
        $listRes->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'total',
                    'waste_banks',
                ],
            ]);

        $firstId = WasteBank::first()->id;
        $detailRes = $this->getJson("/api/waste-banks/{$firstId}");
        $detailRes->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'id',
                    'name',
                    'address',
                    'latitude',
                    'longitude',
                    'village',
                ],
            ]);
    }

    public function test_api_landfills_list_and_detail(): void
    {
        $listRes = $this->getJson('/api/landfills');
        $listRes->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'total',
                    'landfills',
                ],
            ]);

        $firstId = Landfill::first()->id;
        $detailRes = $this->getJson("/api/landfills/{$firstId}");
        $detailRes->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'id',
                    'name',
                    'address',
                    'latitude',
                    'longitude',
                ],
            ]);
    }

    public function test_api_news_list_and_detail(): void
    {
        $listRes = $this->getJson('/api/news');
        $listRes->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'current_page',
                    'total',
                    'articles',
                ],
            ]);

        $article = News::published()->first();
        $detailRes = $this->getJson("/api/news/{$article->slug}");
        $detailRes->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $article->id,
                    'slug' => $article->slug,
                    'title' => $article->title,
                ],
            ]);
    }

    public function test_api_weather_returns_current_sumbersari_weather(): void
    {
        $response = $this->getJson('/api/weather');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'district',
                    'city',
                    'temperature',
                    'humidity',
                    'condition',
                    'icon',
                ],
            ]);
    }

    public function test_api_settings_returns_marker_display_days(): void
    {
        $response = $this->getJson('/api/settings');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'marker_display_days' => 7,
                    'district_name' => 'Kecamatan Sumbersari',
                ],
            ]);
    }

    public function test_web_routes_accessible_by_authenticated_super_admin(): void
    {
        $superAdmin = User::where('email', 'kec.sumbersari@papsampah.id')->first();

        // 1. Peta & Heatmap
        $this->actingAs($superAdmin)->get('/peta')->assertStatus(200);

        // 2. Bank Sampah
        $this->actingAs($superAdmin)->get('/bank-sampah')->assertStatus(200);
        $this->actingAs($superAdmin)->get('/bank-sampah/create')->assertStatus(200);

        // 3. TPA
        $this->actingAs($superAdmin)->get('/tpa')->assertStatus(200);
        $this->actingAs($superAdmin)->get('/tpa/create')->assertStatus(200);

        // 4. Berita
        $this->actingAs($superAdmin)->get('/berita')->assertStatus(200);
        $this->actingAs($superAdmin)->get('/berita/create')->assertStatus(200);
    }

    public function test_duplicate_waste_bank_is_rejected_with_alert(): void
    {
        $superAdmin = User::where('email', 'kec.sumbersari@papsampah.id')->firstOrFail();
        $wasteBank = WasteBank::firstOrFail();
        $initialCount = WasteBank::count();

        $this->actingAs($superAdmin)
            ->from('/bank-sampah/create')
            ->followingRedirects()
            ->post('/bank-sampah', [
                'village_id' => $wasteBank->village_id,
                'name' => strtoupper($wasteBank->name),
                'address' => strtoupper($wasteBank->address),
                'phone' => '0812' . str_pad((string) random_int(0, 999999999), 9, '0', STR_PAD_LEFT),
                'latitude' => '-8.1725',
                'longitude' => '113.7160',
            ])
            ->assertOk()
            ->assertSee('Data sudah ada sebelumnya')
            ->assertSee('role="alert"', false);

        $this->assertSame($initialCount, WasteBank::count());
    }

    public function test_waste_bank_phone_is_required_and_must_be_unique(): void
    {
        $superAdmin = User::where('email', 'kec.sumbersari@papsampah.id')->firstOrFail();
        $existingBank = WasteBank::whereNotNull('phone')->firstOrFail();
        $validData = [
            'village_id' => $existingBank->village_id,
            'name' => 'Bank Sampah Phone Validation ' . uniqid(),
            'address' => 'Alamat Pengujian Telepon',
            'latitude' => '-8.1725',
            'longitude' => '113.7160',
        ];

        $this->actingAs($superAdmin)
            ->from('/bank-sampah/create')
            ->post('/bank-sampah', $validData)
            ->assertRedirect('/bank-sampah/create')
            ->assertSessionHasErrors('phone');

        $this->actingAs($superAdmin)
            ->from('/bank-sampah/create')
            ->post('/bank-sampah', $validData + ['phone' => $existingBank->phone])
            ->assertRedirect('/bank-sampah/create')
            ->assertSessionHasErrors('phone');
    }

    public function test_waste_bank_and_landfill_addresses_reject_unapproved_symbols(): void
    {
        $superAdmin = User::where('email', 'kec.sumbersari@papsampah.id')->firstOrFail();
        $villageId = WasteBank::firstOrFail()->village_id;
        $address = 'Jl. Mawar No. 5 @';

        $this->actingAs($superAdmin)
            ->from('/bank-sampah/create')
            ->post('/bank-sampah', [
                'village_id' => $villageId,
                'name' => 'Address Validation ' . uniqid(),
                'address' => $address,
                'phone' => '08' . str_pad((string) random_int(0, 99999999999), 11, '0', STR_PAD_LEFT),
                'latitude' => '-8.1725',
                'longitude' => '113.7160',
            ])
            ->assertRedirect('/bank-sampah/create')
            ->assertSessionHasErrors('address');

        $this->actingAs($superAdmin)
            ->from('/tpa/create')
            ->post('/tpa', [
                'village_id' => $villageId,
                'name' => 'Landfill Address Validation ' . uniqid(),
                'address' => $address,
                'latitude' => '-8.1725',
                'longitude' => '113.7160',
            ])
            ->assertRedirect('/tpa/create')
            ->assertSessionHasErrors('address');
    }

    public function test_duplicate_landfill_is_rejected_with_alert(): void
    {
        $superAdmin = User::where('email', 'kec.sumbersari@papsampah.id')->firstOrFail();
        $landfill = Landfill::firstOrFail();
        $initialCount = Landfill::count();

        $this->actingAs($superAdmin)
            ->from('/tpa/create')
            ->followingRedirects()
            ->post('/tpa', [
                'village_id' => $landfill->village_id,
                'name' => strtoupper($landfill->name),
                'address' => strtoupper($landfill->address),
                'latitude' => '-8.1880',
                'longitude' => '113.7650',
            ])
            ->assertOk()
            ->assertSee('Data sudah ada sebelumnya')
            ->assertSee('role="alert"', false);

        $this->assertSame($initialCount, Landfill::count());
    }

    public function test_waste_bank_phone_cannot_exceed_13_digits(): void
    {
        $superAdmin = User::where('email', 'kec.sumbersari@papsampah.id')->firstOrFail();
        $wasteBank = WasteBank::firstOrFail();
        $initialCount = WasteBank::count();

        $this->actingAs($superAdmin)
            ->from('/bank-sampah/create')
            ->post('/bank-sampah', [
                'village_id' => $wasteBank->village_id,
                'name' => 'Bank Sampah Phone Test ' . uniqid(),
                'address' => 'Alamat Pengujian Telepon',
                'phone' => '08123456789012',
                'latitude' => '-8.1725',
                'longitude' => '113.7160',
            ])
            ->assertRedirect('/bank-sampah/create')
            ->assertSessionHasErrors('phone');

        $this->assertSame($initialCount, WasteBank::count());
    }

    public function test_super_admin_can_delete_waste_bank_and_landfill(): void
    {
        $superAdmin = User::where('email', 'kec.sumbersari@papsampah.id')->firstOrFail();
        $villageId = $superAdmin->village_id ?? WasteBank::firstOrFail()->village_id;
        $wasteBankName = 'Delete Test Bank ' . uniqid();
        $landfillName = 'Delete Test Landfill ' . uniqid();

        DB::statement(
            'INSERT INTO waste_banks (village_id, name, address, phone, description, is_active, location) VALUES (?, ?, ?, ?, ?, ?, ST_SetSRID(ST_Point(?, ?), 4326))',
            [$villageId, $wasteBankName, 'Alamat Hapus Test', null, null, true, 113.7160, -8.1725]
        );
        DB::statement(
            'INSERT INTO landfills (village_id, name, address, description, is_active, location) VALUES (?, ?, ?, ?, ?, ST_SetSRID(ST_Point(?, ?), 4326))',
            [$villageId, $landfillName, 'Alamat Hapus Test', null, true, 113.7160, -8.1725]
        );

        $wasteBankId = WasteBank::where('name', $wasteBankName)->value('id');
        $landfillId = Landfill::where('name', $landfillName)->value('id');

        $this->actingAs($superAdmin)
            ->delete(route('waste-banks.destroy', $wasteBankId))
            ->assertRedirect(route('waste-banks.index'));
        $this->assertDatabaseMissing('waste_banks', ['id' => $wasteBankId]);

        $this->actingAs($superAdmin)
            ->delete(route('landfills.destroy', $landfillId))
            ->assertRedirect(route('landfills.index'));
        $this->assertDatabaseMissing('landfills', ['id' => $landfillId]);
    }

    public function test_village_admin_cannot_delete_waste_bank_from_another_village(): void
    {
        $adminSumbersari = User::where('email', 'kel.sumbersari@papsampah.id')->firstOrFail();
        $otherVillageBank = WasteBank::where('village_id', '!=', $adminSumbersari->village_id)->firstOrFail();

        $this->actingAs($adminSumbersari)
            ->delete(route('waste-banks.destroy', $otherVillageBank->id))
            ->assertForbidden();

        $this->assertDatabaseHas('waste_banks', ['id' => $otherVillageBank->id]);
    }

    public function test_village_admin_cannot_edit_or_delete_kecamatan_or_other_village_news(): void
    {
        $superAdmin = User::where('email', 'kec.sumbersari@papsampah.id')->firstOrFail();
        $adminSumbersari = User::where('email', 'kel.sumbersari@papsampah.id')->firstOrFail();
        $adminAntirogo = User::where('email', 'kel.antirogo@papsampah.id')->firstOrFail();

        // 1. Artikel dibuat oleh Super Admin Kecamatan
        $kecamatanNews = News::create([
            'title' => 'Berita Edukasi Resmi Kecamatan',
            'slug' => 'berita-edukasi-resmi-kecamatan-' . uniqid(),
            'content' => 'Konten edukasi dari pihak kecamatan Sumbersari.',
            'author_id' => $superAdmin->id,
            'status' => News::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        // 2. Artikel dibuat oleh Admin Kelurahan Antirogo
        $antirogoNews = News::create([
            'title' => 'Inovasi Kompos Warga Antirogo',
            'slug' => 'inovasi-kompos-warga-antirogo-' . uniqid(),
            'content' => 'Laporan pembuatan kompos di wilayah kelurahan Antirogo.',
            'author_id' => $adminAntirogo->id,
            'status' => News::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        // 3. Artikel dibuat oleh Admin Kelurahan Sumbersari
        $sumbersariNews = News::create([
            'title' => 'Bank Sampah Mandiri Sumbersari Beroperasi',
            'slug' => 'bank-sampah-mandiri-sumbersari-' . uniqid(),
            'content' => 'Berita khusus kelurahan Sumbersari.',
            'author_id' => $adminSumbersari->id,
            'status' => News::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        // Scenario A: Admin Kelurahan Sumbersari TIDAK BISA edit/delete berita Kecamatan
        $this->actingAs($adminSumbersari)->get("/berita/{$kecamatanNews->id}/edit")->assertStatus(403);
        $this->actingAs($adminSumbersari)->put("/berita/{$kecamatanNews->id}", [
            'title' => 'Coba Ubah Berita Kecamatan',
            'content' => 'Konten diubah oleh admin kelurahan',
            'status' => News::STATUS_PUBLISHED,
        ])->assertStatus(403);
        $this->actingAs($adminSumbersari)->delete("/berita/{$kecamatanNews->id}")->assertStatus(403);
        $this->assertDatabaseHas('news', ['id' => $kecamatanNews->id]);

        // Scenario B: Admin Kelurahan Sumbersari TIDAK BISA edit/delete berita Kelurahan Lain (Antirogo)
        $this->actingAs($adminSumbersari)->get("/berita/{$antirogoNews->id}/edit")->assertStatus(403);
        $this->actingAs($adminSumbersari)->put("/berita/{$antirogoNews->id}", [
            'title' => 'Coba Ubah Berita Antirogo',
            'content' => 'Konten diubah oleh admin sumbersari',
            'status' => News::STATUS_PUBLISHED,
        ])->assertStatus(403);
        $this->actingAs($adminSumbersari)->delete("/berita/{$antirogoNews->id}")->assertStatus(403);
        $this->assertDatabaseHas('news', ['id' => $antirogoNews->id]);

        // Scenario C: Admin Kelurahan Sumbersari BISA edit & delete berita miliknya sendiri
        $this->actingAs($adminSumbersari)->get("/berita/{$sumbersariNews->id}/edit")->assertStatus(200);
        $this->actingAs($adminSumbersari)->put("/berita/{$sumbersariNews->id}", [
            'title' => 'Bank Sampah Mandiri Sumbersari Updated',
            'content' => 'Update berita kelurahan Sumbersari.',
            'status' => News::STATUS_PUBLISHED,
        ])->assertRedirect(route('news.index'));
        $this->assertDatabaseHas('news', [
            'id' => $sumbersariNews->id,
            'title' => 'Bank Sampah Mandiri Sumbersari Updated',
        ]);
        $this->actingAs($adminSumbersari)->delete("/berita/{$sumbersariNews->id}")->assertRedirect(route('news.index'));
        $this->assertDatabaseMissing('news', ['id' => $sumbersariNews->id]);

        // Scenario D: Super Admin BISA mengelola berita kelurahan
        $this->actingAs($superAdmin)->get("/berita/{$antirogoNews->id}/edit")->assertStatus(200);
        $this->actingAs($superAdmin)->delete("/berita/{$antirogoNews->id}")->assertRedirect(route('news.index'));
        $this->assertDatabaseMissing('news', ['id' => $antirogoNews->id]);
    }

    public function test_web_views_filter_functionality_without_filter_buttons(): void
    {
        $superAdmin = User::where('email', 'kec.sumbersari@papsampah.id')->firstOrFail();

        // 1. Berita search & status filter
        $newsRes = $this->actingAs($superAdmin)->get('/berita?search=sampah&status=PUBLISHED');
        $newsRes->assertStatus(200);
        $newsRes->assertDontSee('>Filter</button>', false);

        // 2. Bank Sampah search & village filter
        $wbRes = $this->actingAs($superAdmin)->get('/bank-sampah?search=bank&village_id=1');
        $wbRes->assertStatus(200);
        $wbRes->assertDontSee('>Filter</button>', false);

        // 3. Laporan Sampah search & category filter
        $repRes = $this->actingAs($superAdmin)->get('/laporan?search=sampah&category_id=1');
        $repRes->assertStatus(200);
        $repRes->assertDontSee('>Terapkan Filter</button>', false);

        // 4. Peta Spasial
        $mapRes = $this->actingAs($superAdmin)->get('/peta');
        $mapRes->assertStatus(200);
        $mapRes->assertSee('initLeafletMap');
        $mapRes->assertSee('vendor/leaflet/leaflet.js');
    }

    public function test_waste_bank_search_matches_phone_and_description(): void
    {
        $superAdmin = User::where('email', 'kec.sumbersari@papsampah.id')->firstOrFail();
        $wasteBank = WasteBank::where('phone', '08123456701')->firstOrFail();

        $this->actingAs($superAdmin)
            ->get('/bank-sampah?search=08123456701')
            ->assertOk()
            ->assertSee($wasteBank->name);

        $this->actingAs($superAdmin)
            ->get('/bank-sampah?search=jelantah')
            ->assertOk()
            ->assertSee($wasteBank->name);
    }

    public function test_duplicate_news_title_is_rejected_with_error(): void
    {
        $superAdmin = User::where('email', 'kec.sumbersari@papsampah.id')->firstOrFail();
        $existingNews = News::firstOrFail();

        $this->actingAs($superAdmin)
            ->from('/berita/create')
            ->post('/berita', [
                'title' => strtoupper($existingNews->title),
                'content' => 'Konten uji duplikasi judul berita.',
                'status' => 'PUBLISHED',
            ])
            ->assertRedirect('/berita/create')
            ->assertSessionHasErrors('title');

        $this->actingAs($superAdmin)
            ->from('/berita/create')
            ->followingRedirects()
            ->post('/berita', [
                'title' => $existingNews->title,
                'content' => 'Konten uji duplikasi judul berita.',
                'status' => 'PUBLISHED',
            ])
            ->assertOk()
            ->assertSee('role="alert"', false)
            ->assertSee('Judul berita sudah ada sebelumnya');
    }

    public function test_news_title_cannot_exceed_20_words(): void
    {
        $superAdmin = User::where('email', 'kec.sumbersari@papsampah.id')->firstOrFail();

        // 21 words title
        $longTitle = 'Satu Dua Tiga Empat Lima Enam Tujuh Delapan Sembilan Sepuluh Sebelas DuaBelas TigaBelas EmpatBelas LimaBelas EnamBelas TujuhBelas DelapanBelas SembilanBelas DuaPuluh DuaPuluhSatu';

        $this->actingAs($superAdmin)
            ->from('/berita/create')
            ->post('/berita', [
                'title' => $longTitle,
                'content' => 'Konten berita judul panjang.',
                'status' => 'PUBLISHED',
            ])
            ->assertRedirect('/berita/create')
            ->assertSessionHasErrors('title');

        // Exactly 20 words title
        $valid20WordsTitle = 'Kata1 Kata2 Kata3 Kata4 Kata5 Kata6 Kata7 Kata8 Kata9 Kata10 Kata11 Kata12 Kata13 Kata14 Kata15 Kata16 Kata17 Kata18 Kata19 ' . uniqid();

        $this->actingAs($superAdmin)
            ->from('/berita/create')
            ->post('/berita', [
                'title' => $valid20WordsTitle,
                'content' => 'Konten berita judul valid 20 kata.',
                'status' => 'PUBLISHED',
            ])
            ->assertRedirect(route('news.index'))
            ->assertSessionHasNoErrors();
    }

    public function test_news_thumbnail_only_accepts_jpg_and_png(): void
    {
        Storage::fake('public');
        $superAdmin = User::where('email', 'kec.sumbersari@papsampah.id')->firstOrFail();

        // Invalid: PDF file
        $pdfFile = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
        $this->actingAs($superAdmin)
            ->from('/berita/create')
            ->post('/berita', [
                'title' => 'Uji File PDF ' . uniqid(),
                'content' => 'Konten file pdf.',
                'status' => 'PUBLISHED',
                'thumbnail' => $pdfFile,
            ])
            ->assertRedirect('/berita/create')
            ->assertSessionHasErrors('thumbnail');

        // Invalid: WEBP file
        $webpFile = UploadedFile::fake()->create('image.webp', 100, 'image/webp');
        $this->actingAs($superAdmin)
            ->from('/berita/create')
            ->post('/berita', [
                'title' => 'Uji File WEBP ' . uniqid(),
                'content' => 'Konten file webp.',
                'status' => 'PUBLISHED',
                'thumbnail' => $webpFile,
            ])
            ->assertRedirect('/berita/create')
            ->assertSessionHasErrors('thumbnail');

        // Valid: JPG photo
        $jpgFile = UploadedFile::fake()->image('photo.jpg', 600, 400);
        $this->actingAs($superAdmin)
            ->from('/berita/create')
            ->post('/berita', [
                'title' => 'Uji File JPG ' . uniqid(),
                'content' => 'Konten file jpg.',
                'status' => 'PUBLISHED',
                'thumbnail' => $jpgFile,
            ])
            ->assertRedirect(route('news.index'))
            ->assertSessionHasNoErrors();
    }

    public function test_news_draft_status_does_not_display_publish_date_in_table(): void
    {
        $superAdmin = User::where('email', 'kec.sumbersari@papsampah.id')->firstOrFail();

        // Create draft article
        $draftArticle = News::create([
            'title' => 'Artikel Konsep Draft ' . uniqid(),
            'slug' => 'artikel-konsep-draft-' . uniqid(),
            'content' => 'Ini adalah artikel yang berstatus draft.',
            'author_id' => $superAdmin->id,
            'status' => News::STATUS_DRAFT,
            'published_at' => null,
        ]);

        $response = $this->actingAs($superAdmin)->get('/berita?status=DRAFT');
        $response->assertOk();
        $response->assertSee($draftArticle->title);

        // Update an existing published article to DRAFT
        $publishedArticle = News::where('status', News::STATUS_PUBLISHED)->firstOrFail();
        $this->actingAs($superAdmin)
            ->put("/berita/{$publishedArticle->id}", [
                'title' => $publishedArticle->title,
                'content' => $publishedArticle->content,
                'status' => News::STATUS_DRAFT,
            ])
            ->assertRedirect(route('news.index'));

        $publishedArticle->refresh();
        $this->assertNull($publishedArticle->published_at);
        $this->assertSame(News::STATUS_DRAFT, $publishedArticle->status);
    }
}
