<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Services\WeatherService;
use Illuminate\Http\JsonResponse;

class PublicInfoApiController extends Controller
{
    public function __construct(
        protected WeatherService $weatherService
    ) {}

    /**
     * Get current weather for Sumbersari District.
     */
    public function weather(): JsonResponse
    {
        $weatherData = $this->weatherService->getCurrentWeather();

        return response()->json([
            'status' => 'success',
            'data' => $weatherData,
        ]);
    }

    /**
     * Get public application settings.
     */
    public function settings(): JsonResponse
    {
        $settings = AppSetting::all()->mapWithKeys(function ($item) {
            return [$item->key => $item->value];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'marker_display_days' => (int) ($settings['marker_display_days'] ?? 7),
                'district_name' => 'Kecamatan Sumbersari',
                'regency_name' => 'Kabupaten Jember',
                'all_settings' => $settings,
            ],
        ]);
    }
}
