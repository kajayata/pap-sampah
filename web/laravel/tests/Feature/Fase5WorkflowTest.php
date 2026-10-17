<?php

namespace Tests\Feature;

use App\Models\CleanupTask;
use App\Models\Role;
use App\Models\User;
use App\Models\WasteCategory;
use App\Models\WasteReport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class Fase5WorkflowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (User::where('email', 'kec.sumbersari@papsampah.id')->doesntExist()) {
            $this->seed(\Database\Seeders\DatabaseSeeder::class);
        }
    }

    /**
     * Titik dijamin berada di dalam boundary kelurahan (Invariant #2).
     *
     * @return array{0: float, 1: float} [latitude, longitude]
     */
    private function pointInsideVillage(int $villageId): array
    {
        $point = DB::selectOne(
            'select ST_Y(ST_PointOnSurface(boundary)) as lat, ST_X(ST_PointOnSurface(boundary)) as lng from villages where id = ?',
            [$villageId]
        );

        return [(float) $point->lat, (float) $point->lng];
    }

    public function test_full_report_lifecycle_from_mobile_api_to_resolution(): void
    {
        Storage::fake('public');

        $villageId = 3; // Kelurahan Sumbersari
        $admin = User::where('email', 'kel.sumbersari@papsampah.id')->firstOrFail();
        $worker = User::where('email', 'petugas1.sumbersari@papsampah.id')->firstOrFail();

        $masyarakatRole = Role::where('name', 'masyarakat')->firstOrFail();
        $citizen = User::firstOrCreate(
            ['email' => 'warga.e2e@papsampah.id'],
            [
                'role_id' => $masyarakatRole->id,
                'name' => 'Warga E2E',
                'password' => 'password123',
                'is_active' => true,
            ]
        );

        [$lat, $lng] = $this->pointInsideVillage($villageId);

        // 1. Masyarakat mengirim laporan via REST API (multipart foto, seperti mobile).
        Sanctum::actingAs($citizen);
        $submit = $this->post('/api/reports', [
            'category_id' => WasteCategory::first()->id,
            'latitude' => $lat,
            'longitude' => $lng,
            'description' => 'Laporan uji e2e alur penuh mobile ke verifikasi.',
            'photos' => [UploadedFile::fake()->image('sampah.jpg', 800, 600)],
        ]);

        $submit->assertStatus(201)->assertJsonPath('status', 'success');
        $reportId = $submit->json('data.id');
        $this->assertNotNull($reportId, 'Report ID harus dikembalikan oleh API.');
        $this->assertEquals('PENDING_VALIDATION', WasteReport::find($reportId)->status);

        // 2. Admin Kelurahan memvalidasi laporan lalu menugaskan petugas.
        $this->actingAs($admin)
            ->post("/laporan/{$reportId}/validate")
            ->assertRedirect(route('reports.show', $reportId));
        $this->assertEquals('VALIDATED', WasteReport::find($reportId)->status);

        $this->actingAs($admin)
            ->post("/laporan/{$reportId}/assign", [
                'worker_ids' => [$worker->id],
                'notes' => 'Bawa karung, cangkul, dan sarung tangan.',
            ])
            ->assertRedirect(route('reports.show', $reportId));

        $task = CleanupTask::where('report_id', $reportId)->firstOrFail();

        // 3. Petugas: terima tugas, unggah bukti AFTER, tandai selesai.
        Sanctum::actingAs($worker);
        $this->post("/api/tasks/{$task->id}/accept")->assertStatus(200);
        $this->post("/api/tasks/{$task->id}/photos", [
            'type' => 'AFTER',
            'photo' => UploadedFile::fake()->image('after.jpg', 800, 600),
        ])->assertStatus(201);
        $this->post("/api/tasks/{$task->id}/complete")->assertStatus(200);

        // Invariant #3: seluruh petugas ACCEPTED selesai -> menunggu verifikasi.
        $this->assertEquals('PENDING_VERIFICATION', WasteReport::find($reportId)->status);

        // 4. Admin memverifikasi hasil -> RESOLVED (Invariant #5 & #6).
        $this->actingAs($admin)
            ->post("/laporan/{$reportId}/verify")
            ->assertRedirect(route('reports.show', $reportId));
        $this->assertEquals('RESOLVED', WasteReport::find($reportId)->fresh()->status);
    }
}
