<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\Web\WorkerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Auth routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard, Petugas, & Laporan (authenticated only)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // CRUD Petugas Kebersihan Desa
    Route::get('/petugas', [WorkerController::class, 'index'])->name('petugas.index');
    Route::get('/petugas/create', [WorkerController::class, 'create'])->name('petugas.create');
    Route::post('/petugas', [WorkerController::class, 'store'])->name('petugas.store');
    Route::get('/petugas/{id}/edit', [WorkerController::class, 'edit'])->name('petugas.edit');
    Route::put('/petugas/{id}', [WorkerController::class, 'update'])->name('petugas.update');
    Route::patch('/petugas/{id}/toggle-status', [WorkerController::class, 'toggleStatus'])->name('petugas.toggle-status');

    // Manajemen & Validasi Laporan Sampah
    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/laporan/{id}', [ReportController::class, 'show'])->name('reports.show');
    Route::post('/laporan/{id}/validate', [ReportController::class, 'validateReport'])->name('reports.validate');
    Route::post('/laporan/{id}/reject', [ReportController::class, 'rejectReport'])->name('reports.reject');
    Route::post('/laporan/{id}/assign', [ReportController::class, 'assignTask'])->name('reports.assign');
    Route::post('/laporan/{id}/verify', [ReportController::class, 'verifyReport'])->name('reports.verify');
    Route::post('/laporan/{id}/reject-verification', [ReportController::class, 'rejectVerification'])->name('reports.reject-verification');
});
