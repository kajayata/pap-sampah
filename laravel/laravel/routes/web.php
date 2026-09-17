<?php

use App\Http\Controllers\Web\AuthController;
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

// Dashboard & Manajemen Petugas (authenticated only)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // CRUD Petugas Kebersihan Desa
    Route::get('/petugas', [WorkerController::class, 'index'])->name('petugas.index');
    Route::get('/petugas/create', [WorkerController::class, 'create'])->name('petugas.create');
    Route::post('/petugas', [WorkerController::class, 'store'])->name('petugas.store');
    Route::get('/petugas/{id}/edit', [WorkerController::class, 'edit'])->name('petugas.edit');
    Route::put('/petugas/{id}', [WorkerController::class, 'update'])->name('petugas.update');
    Route::patch('/petugas/{id}/toggle-status', [WorkerController::class, 'toggleStatus'])->name('petugas.toggle-status');
});
