<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWasteReportRequest;
use App\Models\WasteReport;
use App\Services\WasteReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class WasteReportController extends Controller
{
    public function __construct(
        protected WasteReportService $wasteReportService
    ) {}

    /**
     * Get report history of the authenticated citizen user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $reports = WasteReport::withCoordinates()
            ->with([
                'category:id,name',
                'village:id,name',
                'photos',
            ])
            ->where('reported_by', $user->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $reports->items(),
            'meta' => [
                'current_page' => $reports->currentPage(),
                'last_page' => $reports->lastPage(),
                'per_page' => $reports->perPage(),
                'total' => $reports->total(),
            ],
        ]);
    }

    /**
     * Submit a new waste report with photos and GPS location.
     */
    public function store(StoreWasteReportRequest $request): JsonResponse
    {
        try {
            $report = $this->wasteReportService->createReport(
                reporter: $request->user(),
                data: $request->validated(),
                photoFiles: $request->file('photos')
            );

            // Re-fetch with coordinates
            $report = WasteReport::withCoordinates()
                ->with(['category', 'village', 'photos', 'statusHistories'])
                ->find($report->id);

            return response()->json([
                'status' => 'success',
                'message' => 'Laporan sampah berhasil dikirim dan menunggu validasi.',
                'data' => $report,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan laporan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get detail of a specific report.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $report = WasteReport::withCoordinates()
            ->with([
                'category',
                'village',
                'photos',
                'statusHistories.user:id,name',
                'validator:id,name',
                'cleanupTask.workers.worker:id,name,phone',
                'cleanupTask.photos',
            ])
            ->find($id);

        if (!$report) {
            return response()->json([
                'status' => 'error',
                'message' => 'Laporan sampah tidak ditemukan.',
            ], 404);
        }

        // Check ownership or role access
        $roleName = $user->role?->name;
        if ($roleName === 'masyarakat' && $report->reported_by !== $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses ke laporan ini.',
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $report,
        ]);
    }
}
