<?php

namespace App\Services;

use App\Models\User;
use App\Models\Village;
use App\Models\WasteReport;
use App\Models\WasteReportPhoto;
use App\Models\WasteReportStatusHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class WasteReportService
{
    public function __construct(
        protected ImageStorageService $imageStorageService
    ) {}

    /**
     * Find the village whose boundary contains the given latitude and longitude.
     */
    public function findVillageByCoordinates(float $latitude, float $longitude): ?Village
    {
        return Village::whereRaw(
            'ST_Contains(boundary, ST_SetSRID(ST_MakePoint(?, ?), 4326))',
            [$longitude, $latitude]
        )->where('is_active', true)->first();
    }

    /**
     * Create a new waste report from mobile app with photos, coordinates, and audit trail.
     *
     * @param User $reporter
     * @param array $data ['latitude' => float, 'longitude' => float, 'category_id' => int, 'description' => ?string]
     * @param UploadedFile[] $photoFiles
     * @return WasteReport
     * @throws ValidationException
     */
    public function createReport(User $reporter, array $data, array $photoFiles): WasteReport
    {
        $lat = (float) $data['latitude'];
        $lon = (float) $data['longitude'];

        // Invariant #2: Verify coordinates fall inside operational district villages
        $village = $this->findVillageByCoordinates($lat, $lon);
        if (!$village) {
            throw ValidationException::withMessages([
                'location' => ['Lokasi laporan berada di luar wilayah operasional Kecamatan Sumbersari.'],
            ]);
        }

        if (empty($photoFiles)) {
            throw ValidationException::withMessages([
                'photos' => ['Minimal sertakan 1 foto bukti kondisi sampah.'],
            ]);
        }

        return DB::transaction(function () use ($reporter, $village, $data, $photoFiles, $lat, $lon) {
            // Generate unique report code: REP-YYYYMMDD-XXXXX
            $datePrefix = date('Ymd');
            $randomCode = strtoupper(Str::random(5));
            $reportCode = "REP-{$datePrefix}-{$randomCode}";

            // Ensure unique code in case of collision
            while (WasteReport::where('report_code', $reportCode)->exists()) {
                $randomCode = strtoupper(Str::random(5));
                $reportCode = "REP-{$datePrefix}-{$randomCode}";
            }

            // Insert waste_report using raw PostGIS point
            $reportId = DB::table('waste_reports')->insertGetId([
                'report_code' => $reportCode,
                'reported_by' => $reporter->id,
                'village_id' => $village->id,
                'category_id' => $data['category_id'],
                'description' => $data['description'] ?? null,
                'status' => WasteReport::STATUS_PENDING_VALIDATION,
                'location' => DB::raw("ST_SetSRID(ST_MakePoint({$lon}, {$lat}), 4326)"),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $report = WasteReport::findOrFail($reportId);

            // Process, compress, and record photos
            foreach ($photoFiles as $file) {
                $meta = $this->imageStorageService->processAndStore($file, 'reports');

                DB::table('waste_report_photos')->insert([
                    'report_id' => $report->id,
                    'storage_key' => $meta['storage_key'],
                    'mime_type' => $meta['mime_type'],
                    'file_size' => $meta['file_size'],
                    'width' => $meta['width'],
                    'height' => $meta['height'],
                    'captured_at' => now(),
                    'uploaded_by' => $reporter->id,
                    'location' => DB::raw("ST_SetSRID(ST_MakePoint({$lon}, {$lat}), 4326)"),
                    'created_at' => now(),
                ]);
            }

            // Insert initial audit trail
            WasteReportStatusHistory::create([
                'report_id' => $report->id,
                'status' => WasteReport::STATUS_PENDING_VALIDATION,
                'changed_by' => $reporter->id,
                'note' => 'Laporan baru diajukan oleh masyarakat.',
            ]);

            return $report->load(['category', 'village', 'photos', 'statusHistories']);
        });
    }

    /**
     * Validate an existing report (Admin Desa / Super Admin).
     */
    public function validateReport(WasteReport $report, User $admin): WasteReport
    {
        $this->authorizeAdminAction($report, $admin);

        if (!$report->isPendingValidation()) {
            throw new \DomainException("Hanya laporan dengan status 'Menunggu Validasi' yang dapat disetujui.");
        }

        return DB::transaction(function () use ($report, $admin) {
            $report->update([
                'status' => WasteReport::STATUS_VALIDATED,
                'validated_by' => $admin->id,
                'validated_at' => now(),
            ]);

            WasteReportStatusHistory::create([
                'report_id' => $report->id,
                'status' => WasteReport::STATUS_VALIDATED,
                'changed_by' => $admin->id,
                'note' => 'Laporan telah divalidasi dan disetujui untuk ditangani.',
            ]);

            return $report->fresh(['category', 'village', 'photos', 'statusHistories', 'validator']);
        });
    }

    /**
     * Reject a report with mandatory reason note (Admin Desa / Super Admin).
     */
    public function rejectReport(WasteReport $report, User $admin, string $reason): WasteReport
    {
        $this->authorizeAdminAction($report, $admin);

        if (!$report->isPendingValidation()) {
            throw new \DomainException("Hanya laporan dengan status 'Menunggu Validasi' yang dapat ditolak.");
        }

        if (trim($reason) === '') {
            throw new \InvalidArgumentException('Alasan penolakan laporan wajib diisi.');
        }

        return DB::transaction(function () use ($report, $admin, $reason) {
            $report->update([
                'status' => WasteReport::STATUS_REJECTED,
                'validated_by' => $admin->id,
                'validated_at' => now(),
            ]);

            WasteReportStatusHistory::create([
                'report_id' => $report->id,
                'status' => WasteReport::STATUS_REJECTED,
                'changed_by' => $admin->id,
                'note' => $reason,
            ]);

            return $report->fresh(['category', 'village', 'photos', 'statusHistories', 'validator']);
        });
    }

    /**
     * Enforce village scope boundaries for admin actions.
     */
    protected function authorizeAdminAction(WasteReport $report, User $admin): void
    {
        $roleName = $admin->role?->name;

        if ($roleName === 'super_admin_kecamatan') {
            return;
        }

        if ($roleName === 'admin_desa') {
            if ((int) $admin->village_id !== (int) $report->village_id) {
                throw new AccessDeniedHttpException('Anda tidak berwenang mengelola laporan di luar desa Anda.');
            }
            return;
        }

        throw new AccessDeniedHttpException('Role Anda tidak memiliki izin untuk memvalidasi laporan.');
    }
}
