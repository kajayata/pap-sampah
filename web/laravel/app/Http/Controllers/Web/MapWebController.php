<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Landfill;
use App\Models\Village;
use App\Models\WasteBank;
use App\Services\MapService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MapWebController extends Controller
{
    public function __construct(
        protected MapService $mapService
    ) {}

    /**
     * Display interactive spatial map and heatmap for Sumbersari District.
     */
    public function index(Request $request): View
    {
        $user = $request->user()->loadMissing('role');
        $isSuperAdmin = $user->role?->name === 'super_admin_kecamatan';

        // Admin desa default to their own village if desired, or all Sumbersari
        $selectedVillageId = $request->filled('village_id') ? (int) $request->input('village_id') : null;

        // If village admin, optionally scope to their village or allow viewing full district
        $villages = Village::where('is_active', true)->orderBy('name')->get();

        // 1. Waste Points (Active & Resolved H+7)
        $wastePoints = $this->mapService->getWastePoints($selectedVillageId, 'all');

        // 2. Heatmap Points (Active only, Invariant #7)
        $heatmapData = $this->mapService->getHeatmapData($selectedVillageId);

        // 3. Boundaries GeoJSON
        $boundariesGeoJson = $this->mapService->getVillagesGeoJson($selectedVillageId);

        // 4. Waste Banks & Landfills
        $wasteBanksQuery = WasteBank::withCoordinates()->with('village:id,name')->where('is_active', true);
        if ($selectedVillageId) {
            $wasteBanksQuery->where('village_id', $selectedVillageId);
        }
        $wasteBanks = $wasteBanksQuery->get();

        $landfills = Landfill::withCoordinates()->with('village:id,name')->where('is_active', true)->get();

        $markerDisplayDays = (int) AppSetting::getValue('marker_display_days', '7');

        return view('map.index', compact(
            'wastePoints',
            'heatmapData',
            'boundariesGeoJson',
            'wasteBanks',
            'landfills',
            'villages',
            'selectedVillageId',
            'isSuperAdmin',
            'markerDisplayDays'
        ));
    }
}
