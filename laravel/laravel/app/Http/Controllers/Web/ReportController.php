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
        protected WasteReportService $wasteReportService
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
                'cleanupTask.photos',
            ])
            ->findOrFail($id);

        // Security invariant: Admin Desa cannot access other villages' reports
        if (!$isSuperAdmin && (int) $report->village_id !== (int) $user->village_id) {
            abort(403, 'Anda tidak berwenang mengakses laporan di luar wilayah kelurahan Anda.');
        }

        // Fetch village boundary polygon as GeoJSON for Leaflet map display
        $villageGeoJson = DB::table('villages')
            ->where('id', $report->village_id)
            ->selectRaw('ST_AsGeoJSON(boundary) as geojson')
            ->value('geojson');

        return view('reports.show', compact('report', 'villageGeoJson', 'isSuperAdmin'));
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
}
