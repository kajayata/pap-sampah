<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\LandfillController;
use App\Http\Controllers\Web\MapWebController;
use App\Http\Controllers\Web\NewsController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\Web\WasteBankController;
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

// Dashboard, Petugas, Laporan, & Master Data (authenticated only)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // Peta Sebaran & Heatmap Spasial
    Route::get('/peta', [MapWebController::class, 'index'])->name('map.index');

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

    // Bank Sampah
    Route::get('/bank-sampah', [WasteBankController::class, 'index'])->name('waste-banks.index');
    Route::get('/bank-sampah/create', [WasteBankController::class, 'create'])->name('waste-banks.create');
    Route::post('/bank-sampah', [WasteBankController::class, 'store'])->name('waste-banks.store');
    Route::get('/bank-sampah/{id}/edit', [WasteBankController::class, 'edit'])->name('waste-banks.edit');
    Route::put('/bank-sampah/{id}', [WasteBankController::class, 'update'])->name('waste-banks.update');
    Route::patch('/bank-sampah/{id}/toggle-status', [WasteBankController::class, 'toggleStatus'])->name('waste-banks.toggle-status');

    // TPA & TPS-3R
    Route::get('/tpa', [LandfillController::class, 'index'])->name('landfills.index');
    Route::get('/tpa/create', [LandfillController::class, 'create'])->name('landfills.create');
    Route::post('/tpa', [LandfillController::class, 'store'])->name('landfills.store');
    Route::get('/tpa/{id}/edit', [LandfillController::class, 'edit'])->name('landfills.edit');
    Route::put('/tpa/{id}', [LandfillController::class, 'update'])->name('landfills.update');
    Route::patch('/tpa/{id}/toggle-status', [LandfillController::class, 'toggleStatus'])->name('landfills.toggle-status');

    // Berita & Edukasi Lingkungan
    Route::get('/berita', [NewsController::class, 'index'])->name('news.index');
    Route::get('/berita/create', [NewsController::class, 'create'])->name('news.create');
    Route::post('/berita', [NewsController::class, 'store'])->name('news.store');
    Route::get('/berita/{id}/edit', [NewsController::class, 'edit'])->name('news.edit');
    Route::put('/berita/{id}', [NewsController::class, 'update'])->name('news.update');
    Route::delete('/berita/{id}', [NewsController::class, 'destroy'])->name('news.destroy');
});
