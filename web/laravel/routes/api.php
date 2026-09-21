<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\LandfillApiController;
use App\Http\Controllers\Api\MapApiController;
use App\Http\Controllers\Api\NewsApiController;
use App\Http\Controllers\Api\PublicInfoApiController;
use App\Http\Controllers\Api\WasteBankApiController;
use App\Http\Controllers\Api\WasteReportController;
use App\Http\Controllers\Api\WorkerTaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Public Master Data & Spatial API (Mobile App & Web)
|--------------------------------------------------------------------------
*/
Route::get('/categories', [CategoryController::class, 'index']);

// Spatial Map & Heatmap
Route::get('/map/waste-points', [MapApiController::class, 'wastePoints']);
Route::get('/map/heatmap', [MapApiController::class, 'heatmap']);
Route::get('/map/boundaries', [MapApiController::class, 'boundaries']);

// Waste Banks (Bank Sampah)
Route::get('/waste-banks', [WasteBankApiController::class, 'index']);
Route::get('/waste-banks/{id}', [WasteBankApiController::class, 'show']);

// Landfills (TPA)
Route::get('/landfills', [LandfillApiController::class, 'index']);
Route::get('/landfills/{id}', [LandfillApiController::class, 'show']);

// News & Education
Route::get('/news', [NewsApiController::class, 'index']);
Route::get('/news/{slug}', [NewsApiController::class, 'show']);

// Public Info (Weather & Settings)
Route::get('/weather', [PublicInfoApiController::class, 'weather']);
Route::get('/settings', [PublicInfoApiController::class, 'settings']);

/*
|--------------------------------------------------------------------------
| Protected Mobile API Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Waste Reports (Citizens & Workers)
    Route::get('/reports', [WasteReportController::class, 'index']);
    Route::post('/reports', [WasteReportController::class, 'store']);
    Route::get('/reports/{id}', [WasteReportController::class, 'show']);

    // Cleanup Tasks (Cleaning Workers)
    Route::get('/tasks', [WorkerTaskController::class, 'index']);
    Route::get('/tasks/{id}', [WorkerTaskController::class, 'show']);
    Route::post('/tasks/{id}/accept', [WorkerTaskController::class, 'accept']);
    Route::post('/tasks/{id}/reject', [WorkerTaskController::class, 'reject']);
    Route::post('/tasks/{id}/photos', [WorkerTaskController::class, 'uploadPhoto']);
    Route::post('/tasks/{id}/complete', [WorkerTaskController::class, 'complete']);
});
