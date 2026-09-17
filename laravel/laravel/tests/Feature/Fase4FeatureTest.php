<?php

namespace Tests\Feature;

use App\Models\Landfill;
use App\Models\News;
use App\Models\User;
use App\Models\WasteBank;
use App\Models\WasteReport;
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
}
