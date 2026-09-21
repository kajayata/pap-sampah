<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    protected float $latitude = -8.1724;
    protected float $longitude = 113.7153;

    /**
     * Get current weather for Sumbersari District.
     * Caches response for 30 minutes to reduce rate limits and ensure fast responses.
     *
     * @return array
     */
    public function getCurrentWeather(): array
    {
        return Cache::remember('weather_sumbersari', 1800, function () {
            try {
                $response = Http::timeout(5)->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude' => $this->latitude,
                    'longitude' => $this->longitude,
                    'current' => 'temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m',
                    'timezone' => 'Asia/Jakarta',
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $current = $data['current'] ?? [];

                    $weatherCode = $current['weather_code'] ?? 0;
                    $conditionMeta = $this->interpretWeatherCode($weatherCode);

                    return [
                        'status' => 'success',
                        'district' => 'Sumbersari',
                        'city' => 'Jember',
                        'latitude' => $this->latitude,
                        'longitude' => $this->longitude,
                        'temperature' => $current['temperature_2m'] ?? 28.0,
                        'temperature_unit' => '°C',
                        'humidity' => $current['relative_humidity_2m'] ?? 75,
                        'humidity_unit' => '%',
                        'wind_speed' => $current['wind_speed_10m'] ?? 10.0,
                        'wind_speed_unit' => 'km/h',
                        'weather_code' => $weatherCode,
                        'condition' => $conditionMeta['condition'],
                        'description' => $conditionMeta['description'],
                        'icon' => $conditionMeta['icon'],
                        'fetched_at' => now()->toIso8601String(),
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning('Weather API fetch failed, using fallback weather data: ' . $e->getMessage());
            }

            // Fallback default tropical weather for Sumbersari, Jember if external API is unreachable
            return [
                'status' => 'fallback',
                'district' => 'Sumbersari',
                'city' => 'Jember',
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'temperature' => 28.5,
                'temperature_unit' => '°C',
                'humidity' => 78,
                'humidity_unit' => '%',
                'wind_speed' => 9.5,
                'wind_speed_unit' => 'km/h',
                'weather_code' => 1,
                'condition' => 'Cerah Berawan',
                'description' => 'Cuaca tropis normal untuk Kecamatan Sumbersari',
                'icon' => 'partly_cloudy',
                'fetched_at' => now()->toIso8601String(),
            ];
        });
    }

    /**
     * Map WMO weather code to readable Indonesian description and UI icon.
     */
    protected function interpretWeatherCode(int $code): array
    {
        return match (true) {
            $code === 0 => [
                'condition' => 'Cerah',
                'description' => 'Langit cerah tidak berawan',
                'icon' => 'sunny',
            ],
            in_array($code, [1, 2]) => [
                'condition' => 'Cerah Berawan',
                'description' => 'Sebagian berawan',
                'icon' => 'partly_cloudy',
            ],
            $code === 3 => [
                'condition' => 'Berawan Tebal',
                'description' => 'Langit mendung / tertutup awan',
                'icon' => 'cloudy',
            ],
            in_array($code, [45, 48]) => [
                'condition' => 'Berkabut',
                'description' => 'Jarak pandang terbatas karena kabut',
                'icon' => 'fog',
            ],
            in_array($code, [51, 53, 55]) => [
                'condition' => 'Gerimis',
                'description' => 'Hujan rintik-rintik ringan',
                'icon' => 'drizzle',
            ],
            in_array($code, [61, 63]) => [
                'condition' => 'Hujan Sedang',
                'description' => 'Hujan dengan intensitas sedang',
                'icon' => 'rainy',
            ],
            in_array($code, [65, 80, 81, 82]) => [
                'condition' => 'Hujan Lebat',
                'description' => 'Hujan deras lebat',
                'icon' => 'heavy_rain',
            ],
            in_array($code, [95, 96, 99]) => [
                'condition' => 'Hujan Badai Petir',
                'description' => 'Hujan disertai petir dan angin kencang',
                'icon' => 'thunderstorm',
            ],
            default => [
                'condition' => 'Berawan',
                'description' => 'Kondisi berawan normal',
                'icon' => 'cloudy',
            ],
        };
    }
}
