<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WasteBank;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WasteBankApiController extends Controller
{
    /**
     * Get active waste banks in Sumbersari District.
     * Query params:
     * - village_id: int (optional)
     * - search: string (optional)
     */
    public function index(Request $request): JsonResponse
    {
        $query = WasteBank::withCoordinates()
            ->with('village:id,name')
            ->where('is_active', true);

        if ($request->filled('village_id')) {
            $query->where('village_id', (int) $request->input('village_id'));
        }

        if ($request->filled('search')) {
            $search = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', $search)
                  ->orWhere('address', 'ilike', $search);
            });
        }

        $wasteBanks = $query->orderBy('name')->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total' => $wasteBanks->count(),
                'waste_banks' => $wasteBanks,
            ],
        ]);
    }

    /**
     * Get detail of a specific waste bank.
     */
    public function show(int $id): JsonResponse
    {
        $wasteBank = WasteBank::withCoordinates()
            ->with('village:id,name')
            ->find($id);

        if (!$wasteBank) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bank sampah tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $wasteBank,
        ]);
    }
}
