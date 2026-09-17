<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MapService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MapApiController extends Controller
{
    public function __construct(
        protected MapService $mapService
    ) {}

    /**
     * Get waste points for mobile / web map.
     * Query params:
     * - village_id: int (optional)
     * - type: 'all' | 'active' | 'resolved' (default: 'all')
     */
    public function wastePoints(Request $request): JsonResponse
    {
        $villageId = $request->filled('village_id') ? (int) $request->input('village_id') : null;
        $type = in_array($request->input('type'), ['all', 'active', 'resolved']) ? $request->input('type') : 'all';

        $points = $this->mapService->getWastePoints($villageId, $type);

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_points' => count($points),
                'points' => $points,
            ],
        ]);
    }

    /**
     * Get heatmap points (latitude, longitude, intensity).
     * Strictly excludes RESOLVED reports (Invariant #7).
     */
    public function heatmap(Request $request): JsonResponse
    {
        $villageId = $request->filled('village_id') ? (int) $request->input('village_id') : null;
        $heatmapData = $this->mapService->getHeatmapData($villageId);

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_points' => count($heatmapData),
                'heatmap_points' => $heatmapData,
            ],
        ]);
    }

    /**
     * Get GeoJSON boundaries for Sumbersari District and its 7 villages.
     */
    public function boundaries(Request $request): JsonResponse
    {
        $villageId = $request->filled('village_id') ? (int) $request->input('village_id') : null;
        $geojson = $this->mapService->getVillagesGeoJson($villageId);

        return response()->json([
            'status' => 'success',
            'data' => $geojson,
        ]);
    }
}
