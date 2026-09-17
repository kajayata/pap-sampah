<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Landfill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LandfillApiController extends Controller
{
    /**
     * Get active landfills (TPA) for Sumbersari District.
     */
    public function index(Request $request): JsonResponse
    {
        $landfills = Landfill::withCoordinates()
            ->with('village:id,name')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total' => $landfills->count(),
                'landfills' => $landfills,
            ],
        ]);
    }

    /**
     * Get detail of a specific landfill (TPA).
     */
    public function show(int $id): JsonResponse
    {
        $landfill = Landfill::withCoordinates()
            ->with('village:id,name')
            ->find($id);

        if (!$landfill) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tempat Pembuangan Akhir (TPA) tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $landfill,
        ]);
    }
}
