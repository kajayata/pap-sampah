<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
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
| Master Data (Public / Mobile App)
|--------------------------------------------------------------------------
*/
Route::get('/categories', [CategoryController::class, 'index']);

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
