<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\District;
use App\Models\Landfill;
use App\Models\Village;
use App\Models\WasteBank;
use App\Models\WasteReport;
use App\Services\MapService;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SampahJemberLandingController extends Controller
{
    public function __construct(
        protected MapService $mapService
    ) {}

    public function index(): View
    {
        $district = District::where('code', '35.09.20')
            ->where('is_active', true)
            ->firstOrFail();

        $villageIds = DB::table('villages')
            ->where('district_id', $district->id)
            ->where('is_active', true)
            ->pluck('id');

        $statusCounts = WasteReport::query()
            ->whereIn('village_id', $villageIds)
            ->selectRaw('count(*) as total')
            ->selectRaw('count(*) filter (where status not in (?, ?)) as active', [
                WasteReport::STATUS_RESOLVED,
                WasteReport::STATUS_REJECTED,
            ])
            ->selectRaw('count(*) filter (where status = ?) as resolved', [WasteReport::STATUS_RESOLVED])
            ->first();

        $villageStats = WasteReport::query()
            ->whereIn('village_id', $villageIds)
            ->select('village_id')
            ->selectRaw('count(*) as total')
            ->selectRaw('count(*) filter (where status not in (?, ?)) as active', [
                WasteReport::STATUS_RESOLVED,
                WasteReport::STATUS_REJECTED,
            ])
            ->selectRaw('count(*) filter (where status = ?) as resolved', [WasteReport::STATUS_RESOLVED])
            ->groupBy('village_id')
            ->get()
            ->keyBy('village_id');

        $villages = DB::table('villages')
            ->whereIn('id', $villageIds)
            ->orderBy('name')
            ->get(['id', 'name']);

        $villageCards = $villages->map(function ($village) use ($villageStats) {
            $villageStat = $villageStats->get($village->id);
            $active = (int) ($villageStat->active ?? 0);

            return [
                'name' => $village->name,
                'status' => $active > 0 ? 'Aktif' : 'Selesai',
                'total' => (int) ($villageStat->total ?? 0),
                'selesai' => (int) ($villageStat->resolved ?? 0),
                'aktif' => $active,
            ];
        })->values()->all();

        $total = (int) ($statusCounts->total ?? 0);
        $resolved = (int) ($statusCounts->resolved ?? 0);

        $stats = [
            'wilayah' => $district->name,
            'total_laporan' => $total,
            'persentase_selesai' => $total > 0 ? round(($resolved / $total) * 100) . '%' : '0%',
            'kelurahan_dipantau' => $villages->count(),
            'laporan_aktif' => (int) ($statusCounts->active ?? 0),
        ];

        $heatmapData = $villageIds
            ->flatMap(fn ($villageId) => $this->mapService->getHeatmapData((int) $villageId))
            ->values()
            ->all();

        $wastePoints = $villageIds
            ->flatMap(fn ($villageId) => $this->mapService->getWastePoints((int) $villageId, 'all'))
            ->values()
            ->all();
        $boundariesGeoJson = $this->mapService->getVillagesGeoJson();
        $villages = Village::whereIn('id', $villageIds)->orderBy('name')->get();
        $wasteBanks = WasteBank::withCoordinates()
            ->with('village:id,name')
            ->whereIn('village_id', $villageIds)
            ->where('is_active', true)
            ->get();
        $landfills = Landfill::withCoordinates()
            ->with('village:id,name')
            ->where('is_active', true)
            ->get();
        $markerDisplayDays = (int) AppSetting::getValue('marker_display_days', '7');

        $guideSteps = [
            ['number' => '01', 'icon' => '📱', 'title' => 'Unduh Aplikasi', 'description' => 'Unduh aplikasi SampahJember secara gratis melalui Google Play Store atau App Store.'],
            ['number' => '02', 'icon' => '👤', 'title' => 'Daftar & Masuk', 'description' => 'Buat akun masyarakat melalui aplikasi mobile, lalu masuk untuk mulai melapor.'],
            ['number' => '03', 'icon' => '📍', 'title' => 'Ambil Lokasi', 'description' => 'Aktifkan GPS dan kirim laporan untuk lokasi di wilayah ' . $district->name . '. Sistem menentukan kelurahan berdasarkan koordinat.'],
            ['number' => '04', 'icon' => '📸', 'title' => 'Kirim Laporan', 'description' => 'Pilih kategori, sertakan foto kondisi sampah, lalu tambahkan deskripsi agar Admin Desa dapat memvalidasi laporan.'],
            ['number' => '05', 'icon' => '🧹', 'title' => 'Pantau Penanganan', 'description' => 'Admin Desa memvalidasi laporan yang masuk. Jika valid, satu pekerjaan pembersihan ditugaskan kepada satu atau beberapa petugas di desa tersebut. Petugas dapat menerima atau menolak tugas.'],
            ['number' => '06', 'icon' => '✅', 'title' => 'Lihat Hasil', 'description' => 'Petugas yang menerima tugas membersihkan lokasi lalu mengirim foto hasil. Admin Desa memverifikasi bukti; setelah valid, laporan berstatus selesai.'],
        ];

        return view('public_landing.index', compact(
            'district',
            'stats',
            'villageCards',
            'heatmapData',
            'wastePoints',
            'boundariesGeoJson',
            'villages',
            'wasteBanks',
            'landfills',
            'markerDisplayDays',
            'guideSteps'
        ));
    }
}
