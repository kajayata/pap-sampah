<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\Village;
use App\Models\WasteReport;
use Illuminate\Support\Facades\DB;

class MapService
{
    /**
     * Get waste points for interactive map (active points + temporarily displayed resolved points).
     * Enforces Invariant #7 & #8.
     *
     * @param int|null $villageId
     * @param string $type 'all' | 'active' | 'resolved'
     * @return array
     */
    public function getWastePoints(?int $villageId = null, string $type = 'all'): array
    {
        $displayDays = (int) AppSetting::getValue('marker_display_days', '7');
        $resolvedCutoff = now()->subDays($displayDays);

        $query = WasteReport::withCoordinates()
            ->with([
                'category:id,name',
                'village:id,name',
                'photos:id,report_id,storage_key',
                'cleanupTask.photos' => function ($q) {
                    $q->where('type', 'AFTER');
                },
            ]);

        if ($villageId) {
            $query->where('village_id', $villageId);
        }

        if ($type === 'active') {
            $query->whereIn('status', [
                WasteReport::STATUS_PENDING_VALIDATION,
                WasteReport::STATUS_VALIDATED,
                WasteReport::STATUS_ASSIGNED,
                WasteReport::STATUS_IN_PROGRESS,
                WasteReport::STATUS_PENDING_VERIFICATION,
            ]);
        } elseif ($type === 'resolved') {
            $query->where('status', WasteReport::STATUS_RESOLVED)
                ->where('resolved_at', '>=', $resolvedCutoff);
        } else {
            // 'all': Active OR Resolved within marker_display_days
            $query->where(function ($q) use ($resolvedCutoff) {
                $q->whereIn('status', [
                    WasteReport::STATUS_PENDING_VALIDATION,
                    WasteReport::STATUS_VALIDATED,
                    WasteReport::STATUS_ASSIGNED,
                    WasteReport::STATUS_IN_PROGRESS,
                    WasteReport::STATUS_PENDING_VERIFICATION,
                ])->orWhere(function ($sub) use ($resolvedCutoff) {
                    $sub->where('status', WasteReport::STATUS_RESOLVED)
                        ->where('resolved_at', '>=', $resolvedCutoff);
                });
            });
        }

        $reports = $query->orderByDesc('created_at')->get();

        return $reports->map(function ($report) {
            $isResolved = $report->status === WasteReport::STATUS_RESOLVED;

            // Before photos from citizen report
            $beforePhotos = $report->photos->map(fn($p) => [
                'id' => $p->id,
                'url' => $p->url,
            ]);

            // After photos from worker cleanup task
            $afterPhotos = $report->cleanupTask ? $report->cleanupTask->photos->map(fn($p) => [
                'id' => $p->id,
                'url' => $p->url,
            ]) : collect();

            return [
                'id' => $report->id,
                'report_code' => $report->report_code,
                'latitude' => $report->latitude,
                'longitude' => $report->longitude,
                'status' => $report->status,
                'status_label' => $report->status_label,
                'is_resolved' => $isResolved,
                'category_name' => $report->category?->name,
                'village_id' => $report->village_id,
                'village_name' => $report->village?->name,
                'description' => $report->description,
                'created_at' => $report->created_at?->toIso8601String(),
                'created_at_formatted' => $report->created_at?->translatedFormat('d M Y H:i'),
                'resolved_at' => $report->resolved_at?->toIso8601String(),
                'resolved_at_formatted' => $report->resolved_at?->translatedFormat('d M Y H:i'),
                'primary_photo_url' => $report->photos->first()?->url,
                'before_photos' => $beforePhotos,
                'after_photos' => $afterPhotos,
            ];
        })->toArray();
    }

    /**
     * Get heatmap coordinates and intensity weight.
     * Enforces Invariant #7: RESOLVED reports are strictly excluded from heatmap.
     *
     * @param int|null $villageId
     * @return array array of [lat, lng, intensity]
     */
    public function getHeatmapData(?int $villageId = null): array
    {
        $query = WasteReport::withCoordinates()
            ->whereIn('status', [
                WasteReport::STATUS_PENDING_VALIDATION,
                WasteReport::STATUS_VALIDATED,
                WasteReport::STATUS_ASSIGNED,
                WasteReport::STATUS_IN_PROGRESS,
                WasteReport::STATUS_PENDING_VERIFICATION,
            ]);

        if ($villageId) {
            $query->where('village_id', $villageId);
        }

        $reports = $query->get();

        return $reports->map(function ($report) {
            // Weight intensity: 1.0 default
            $weight = match ($report->status) {
                WasteReport::STATUS_PENDING_VALIDATION => 1.0,
                WasteReport::STATUS_VALIDATED => 0.9,
                WasteReport::STATUS_ASSIGNED, WasteReport::STATUS_IN_PROGRESS => 0.7,
                WasteReport::STATUS_PENDING_VERIFICATION => 0.5,
                default => 0.5,
            };

            return [
                $report->latitude,
                $report->longitude,
                $weight,
            ];
        })->filter(fn($point) => !is_null($point[0]) && !is_null($point[1]))->values()->toArray();
    }

    /**
     * Get GeoJSON FeatureCollection of villages boundaries in Sumbersari District.
     *
     * @param int|null $villageId
     * @return array
     */
    public function getVillagesGeoJson(?int $villageId = null): array
    {
        $query = DB::table('villages')
            ->select('id', 'name', DB::raw('ST_AsGeoJSON(boundary) as geojson'))
            ->where('is_active', true);

        if ($villageId) {
            $query->where('id', $villageId);
        }

        $villages = $query->get();

        $features = [];
        foreach ($villages as $village) {
            if ($village->geojson) {
                $features[] = [
                    'type' => 'Feature',
                    'properties' => [
                        'id' => $village->id,
                        'name' => $village->name,
                    ],
                    'geometry' => json_decode($village->geojson, true),
                ];
            }
        }

        return [
            'type' => 'FeatureCollection',
            'features' => $features,
        ];
    }
}
