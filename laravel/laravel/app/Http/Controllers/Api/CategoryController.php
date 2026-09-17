<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WasteCategory;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * Get active waste categories for reporting options in mobile app.
     */
    public function index(): JsonResponse
    {
        $categories = WasteCategory::select('id', 'name', 'description')->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories,
        ]);
    }
}
