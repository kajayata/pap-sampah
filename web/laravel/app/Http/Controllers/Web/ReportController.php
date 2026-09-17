<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Models\WasteCategory;
use App\Models\WasteReport;
use App\Services\WasteReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        protected WasteReportService $wasteReportService,
        protected \App\Services\CleanupTaskService $cleanupTaskService
    ) {}

    /**
     * Display a listing of waste reports scoped to the user's administrative jurisdiction.
     */
    public function index(Request $request): View
    {
        $user = $request->user()->loadMissing('role');
        $isSuperAdmin = $user->role?->name === 'super_admin_kecamatan';

        $query = WasteReport::withCoordinates()
            ->with(['category', 'village', 'reporter', 'photos']);

        // Scope boundary: Village admin only sees reports from their village
        if (!$isSuperAdmin) {
            $query->where('village_id', $user->village_id);
        } elseif ($request->filled('village_id')) {
            $query->where('village_id', $request->input('village_id'));
        }

        // Base query for status counts (clean query without spatial column projections)
        $countQuery = WasteReport::query();
        if (!$isSuperAdmin) {
            $countQuery->where('village_id', $user->village_id);
        } elseif ($request->filled('village_id')) {
            $countQuery->where('village_id', $request->input('village_id'));
        }

        // Filter: Status
        $selectedStatus = $request->input('status', 'ALL');
        if ($selectedStatus !== 'ALL') {
            $query->where('status', $selectedStatus);
        }

        // Filter: Category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Search: Code, description, or reporter name
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('report_code', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%")
                  ->orWhereHas('reporter', function ($sub) use ($search) {
                      $sub->where('name', 'ilike', "%{$search}%")
                          ->orWhere('email', 'ilike', "%{$search}%");
                  });
            });
        }

        $reports = $query->orderByDesc('created_at')->paginate(12)->withQueryString();

        // Calculate counts for status filter tabs
        $statusCounts = (clone $countQuery)
            ->selectRaw("
                count(*) as all_count,
                count(*) filter (where status = 'PENDING_VALIDATION') as pending_count,
                count(*) filter (where status = 'VALIDATED') as validated_count,
                count(*) filter (where status in ('ASSIGNED', 'IN_PROGRESS', 'PENDING_VERIFICATION')) as active_count,
                count(*) filter (where status = 'RESOLVED') as resolved_count,
                count(*) filter (where status = 'REJECTED') as rejected_count
            ")
            ->first();

        $categories = WasteCategory::where('is_active', true)->get();
        $villages = $isSuperAdmin ? Village::where('is_active', true)->orderBy('name')->get() : collect();

        return view('reports.index', compact('reports', 'statusCounts', 'categories', 'villages', 'isSuperAdmin', 'selectedStatus'));
    }

    /**
     * Display detailed report with photos, PostGIS map, and history.
     */
    public function show(Request $request, int $id): View
    {
        $user = $request->user()->loadMissing('role');
        $isSuperAdmin = $user->role?->name === 'super_admin_kecamatan';

        $report = WasteReport::withCoordinates()
            ->with([
                'category',
                'village',
                'reporter',
                'validator',
                'photos',
                'statusHistories.user',
                'cleanupTask.workers.worker',
                'cleanupTask.photos.uploader',
            ])
            ->findOrFail($id);

        // Security invariant: Admin Desa cannot access other villages' reports
        if (!$isSuperAdmin && (int) $report->village_id !== (int) $user->village_id) {
            abort(403, 'Anda tidak berwenang mengakses laporan di luar wilayah kelurahan Anda.');
        }

        // Available active cleaning workers in this specific village (Invariant #4)
        $availableWorkers = \App\Models\User::where('village_id', $report->village_id)
            ->where('is_active', true)
            ->whereHas('role', fn($q) => $q->where('name', 'petugas_desa'))
            ->orderBy('name')
            ->get();

        // Fetch village boundary polygon as GeoJSON for Leaflet map display
        $villageGeoJson = DB::table('villages')
            ->where('id', $report->village_id)
            ->selectRaw('ST_AsGeoJSON(boundary) as geojson')
            ->value('geojson');

        return view('reports.show', compact('report', 'villageGeoJson', 'isSuperAdmin', 'availableWorkers'));
    }

    /**
     * Validate and approve a pending waste report.
     */
    public function validateReport(Request $request, int $id): RedirectResponse
    {
        $report = WasteReport::findOrFail($id);

        try {
            $this->wasteReportService->validateReport($report, $request->user());
            return redirect()
                ->route('reports.show', $id)
                ->with('success', "Laporan [{$report->report_code}] berhasil disetujui dan siap ditugaskan.");
        } catch (\Throwable $e) {
            return redirect()
                ->route('reports.show', $id)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Reject a pending waste report with mandatory reason.
     */
    public function rejectReport(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'reason.required' => 'Alasan penolakan laporan wajib diisi.',
            'reason.min' => 'Alasan penolakan minimal 5 karakter.',
            'reason.max' => 'Alasan penolakan maksimal 500 karakter.',
        ]);

        $report = WasteReport::findOrFail($id);

        try {
            $this->wasteReportService->rejectReport($report, $request->user(), $request->input('reason'));
            return redirect()
                ->route('reports.show', $id)
                ->with('success', "Laporan [{$report->report_code}] telah ditolak dengan catatan alasan.");
        } catch (\Throwable $e) {
            return redirect()
                ->route('reports.show', $id)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Assign cleanup task to one or more village cleaning workers.
     */
    public function assignTask(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'worker_ids' => ['required', 'array', 'min:1'],
            'worker_ids.*' => ['required', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'worker_ids.required' => 'Pilih minimal 1 orang petugas kebersihan desa.',
            'worker_ids.min' => 'Pilih minimal 1 orang petugas kebersihan desa.',
            'notes.max' => 'Instruksi dan catatan maksimal 1000 karakter.',
        ]);

        $report = WasteReport::findOrFail($id);

        try {
            $this->cleanupTaskService->assignTask(
                report: $report,
                admin: $request->user(),
                workerIds: $request->input('worker_ids'),
                notes: $request->input('notes')
            );

            $count = count($request->input('worker_ids'));
            return redirect()
                ->route('reports.show', $id)
                ->with('success', "Tugas pembersihan berhasil dibuat dan ditugaskan kepada {$count} petugas kebersihan desa.");
        } catch (\Throwable $e) {
            return redirect()
                ->route('reports.show', $id)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Verify cleanup results and mark report as RESOLVED (Invariant #5 & #6).
     */
    public function verifyReport(Request $request, int $id): RedirectResponse
    {
        $report = WasteReport::with('cleanupTask')->findOrFail($id);

        if (!$report->cleanupTask) {
            return redirect()->route('reports.show', $id)->with('error', 'Tugas pembersihan belum dibuat.');
        }

        try {
            $this->cleanupTaskService->verifyAndResolve($report->cleanupTask, $request->user());

            return redirect()
                ->route('reports.show', $id)
                ->with('success', "Hasil pembersihan telah diverifikasi dan disetujui! Laporan [{$report->report_code}] resmi dinyatakan SELESAI (RESOLVED).");
        } catch (\Throwable $e) {
            return redirect()
                ->route('reports.show', $id)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Reject cleanup verification and request rework from cleaning workers.
     */
    public function rejectVerification(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'notes' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'notes.required' => 'Catatan alasan perbaikan/pembersihan ulang wajib diisi.',
            'notes.min' => 'Catatan minimal 5 karakter.',
            'notes.max' => 'Catatan maksimal 500 karakter.',
        ]);

        $report = WasteReport::with('cleanupTask')->findOrFail($id);

        if (!$report->cleanupTask) {
            return redirect()->route('reports.show', $id)->with('error', 'Tugas pembersihan belum dibuat.');
        }

        try {
            $this->cleanupTaskService->rejectVerification($report->cleanupTask, $request->user(), $request->input('notes'));

            return redirect()
                ->route('reports.show', $id)
                ->with('success', "Pembersihan ulang telah diminta kepada tim petugas kebersihan dengan catatan perbaikan.");
        } catch (\Throwable $e) {
            return redirect()
                ->route('reports.show', $id)
                ->with('error', $e->getMessage());
        }
    }
}
