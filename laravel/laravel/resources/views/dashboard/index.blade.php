@extends('layouts.app')

@section('title', 'Dashboard — Pap Sampah')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="font-display text-3xl font-bold text-text-primary">Dashboard</h1>
        <p class="text-text-muted mt-1">Selamat datang kembali, <span class="font-medium text-text-primary">{{ $user->name }}</span></p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        {{-- Total Laporan --}}
        <div class="bg-surface rounded-2xl border border-border-default p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-medium text-text-muted">Total Laporan</span>
                <span class="w-9 h-9 bg-accent-primary/10 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-accent-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                </span>
            </div>
            <div class="font-display text-3xl font-black text-text-primary">0</div>
            <div class="text-xs text-text-muted mt-1">Semua status</div>
        </div>

        {{-- Aktif --}}
        <div class="bg-surface rounded-2xl border border-border-default p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-medium text-text-muted">Aktif</span>
                <span class="w-9 h-9 bg-accent-warning/10 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-accent-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </span>
            </div>
            <div class="font-display text-3xl font-black text-accent-warning">0</div>
            <div class="text-xs text-text-muted mt-1">Perlu penanganan</div>
        </div>

        {{-- Selesai --}}
        <div class="bg-surface rounded-2xl border border-border-default p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-medium text-text-muted">Selesai</span>
                <span class="w-9 h-9 bg-accent-primary/10 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-accent-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </span>
            </div>
            <div class="font-display text-3xl font-black text-accent-primary">0</div>
            <div class="text-xs text-text-muted mt-1">Telah ditangani</div>
        </div>

        {{-- Petugas --}}
        <div class="bg-surface rounded-2xl border border-border-default p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-medium text-text-muted">Petugas</span>
                <span class="w-9 h-9 bg-accent-secondary/20 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 00-3-3.87"/>
                        <path d="M16 3.13a4 4 0 010 7.75"/>
                    </svg>
                </span>
            </div>
            <div class="font-display text-3xl font-black text-text-primary">0</div>
            <div class="text-xs text-text-muted mt-1">Tim kebersihan</div>
        </div>
    </div>

    {{-- Role-specific content --}}
    @if($user->hasRole('super_admin_kecamatan'))
        {{-- Super Admin: Monitoring --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Map / Overview --}}
            <div class="lg:col-span-2 bg-surface rounded-2xl border border-border-default p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-display text-xl font-bold text-text-primary">Peta Kecamatan</h2>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-accent-primary/10 text-accent-primary">
                        Live
                    </span>
                </div>
                <div class="bg-base rounded-2xl h-64 flex items-center justify-center border border-border-default">
                    <div class="text-center">
                        <svg class="w-12 h-12 text-text-muted/30 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        <p class="text-sm text-text-muted">Peta heatmap akan ditampilkan di sini</p>
                    </div>
                </div>
            </div>

            {{-- Sidebar info --}}
            <div class="space-y-4">
                {{-- Info Akun --}}
                <div class="bg-surface rounded-2xl border border-border-default p-5">
                    <h3 class="font-display text-lg font-bold text-text-primary mb-4">Informasi Akun</h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 bg-accent-secondary/20 rounded-full flex items-center justify-center text-sm font-bold text-text-primary">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                            <div>
                                <div class="text-sm font-medium text-text-primary">{{ $user->name }}</div>
                                <div class="text-xs text-text-muted">{{ $user->email }}</div>
                            </div>
                        </div>
                        <div class="pt-3 border-t border-border-default">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-text-muted">Role</span>
                                <span class="font-medium text-accent-primary">Super Admin Kecamatan</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Aksi Cepat --}}
                <div class="bg-surface rounded-2xl border border-border-default p-5">
                    <h3 class="font-display text-lg font-bold text-text-primary mb-4">Aksi Cepat</h3>
                    <div class="space-y-2">
                        <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-accent-primary/5 border border-transparent hover:border-accent-primary/15 transition-all duration-200 group">
                            <span class="w-9 h-9 bg-accent-primary/10 rounded-xl flex items-center justify-center group-hover:bg-accent-primary/20 transition-colors">
                                <svg class="w-5 h-5 text-accent-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                            </span>
                            <span class="text-sm font-medium text-text-primary">Lihat Peta</span>
                        </a>
                        <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-accent-primary/5 border border-transparent hover:border-accent-primary/15 transition-all duration-200 group">
                            <span class="w-9 h-9 bg-accent-warning/10 rounded-xl flex items-center justify-center group-hover:bg-accent-warning/20 transition-colors">
                                <svg class="w-5 h-5 text-accent-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                    <line x1="12" y1="9" x2="12" y2="13"/>
                                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                                </svg>
                            </span>
                            <span class="text-sm font-medium text-text-primary">Laporan Aktif</span>
                        </a>
                        <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-accent-primary/5 border border-transparent hover:border-accent-primary/15 transition-all duration-200 group">
                            <span class="w-9 h-9 bg-accent-primary/10 rounded-xl flex items-center justify-center group-hover:bg-accent-primary/20 transition-colors">
                                <svg class="w-5 h-5 text-accent-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="20" x2="18" y2="10"/>
                                    <line x1="12" y1="20" x2="12" y2="4"/>
                                    <line x1="6" y1="20" x2="6" y2="14"/>
                                </svg>
                            </span>
                            <span class="text-sm font-medium text-text-primary">Statistik</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($user->hasRole('admin_desa'))
        {{-- Admin Desa --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Recent Reports --}}
            <div class="lg:col-span-2 bg-surface rounded-2xl border border-border-default p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-display text-xl font-bold text-text-primary">Laporan Terbaru</h2>
                    <a href="#" class="text-sm font-medium text-accent-primary hover:text-emphasis/80 transition-colors">Lihat Semua</a>
                </div>

                {{-- Empty state --}}
                <div class="bg-base rounded-2xl border border-border-default p-8 text-center">
                    <svg class="w-12 h-12 text-text-muted/30 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                    <p class="text-sm text-text-muted">Belum ada laporan masuk</p>
                    <p class="text-xs text-text-muted/60 mt-1">Laporan dari masyarakat akan muncul di sini</p>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-4">
                {{-- Info Desa --}}
                <div class="bg-surface rounded-2xl border border-border-default p-5">
                    <h3 class="font-display text-lg font-bold text-text-primary mb-4">Informasi Desa</h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 bg-accent-secondary/20 rounded-full flex items-center justify-center text-sm font-bold text-text-primary">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                            <div>
                                <div class="text-sm font-medium text-text-primary">{{ $user->name }}</div>
                                <div class="text-xs text-text-muted">{{ $user->email }}</div>
                            </div>
                        </div>
                        <div class="pt-3 border-t border-border-default space-y-2">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-text-muted">Role</span>
                                <span class="font-medium text-accent-primary">Admin Desa</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-text-muted">Desa</span>
                                <span class="font-medium text-text-primary">{{ $user->village?->name ?? '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Aksi Cepat --}}
                <div class="bg-surface rounded-2xl border border-border-default p-5">
                    <h3 class="font-display text-lg font-bold text-text-primary mb-4">Aksi Cepat</h3>
                    <div class="space-y-2">
                        <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-accent-primary/5 border border-transparent hover:border-accent-primary/15 transition-all duration-200 group">
                            <span class="w-9 h-9 bg-accent-warning/10 rounded-xl flex items-center justify-center group-hover:bg-accent-warning/20 transition-colors">
                                <svg class="w-5 h-5 text-accent-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                    <line x1="12" y1="9" x2="12" y2="13"/>
                                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                                </svg>
                            </span>
                            <span class="text-sm font-medium text-text-primary">Validasi Laporan</span>
                        </a>
                        <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-accent-primary/5 border border-transparent hover:border-accent-primary/15 transition-all duration-200 group">
                            <span class="w-9 h-9 bg-accent-primary/10 rounded-xl flex items-center justify-center group-hover:bg-accent-primary/20 transition-colors">
                                <svg class="w-5 h-5 text-accent-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <line x1="19" y1="8" x2="19" y2="14"/>
                                    <line x1="22" y1="11" x2="16" y2="11"/>
                                </svg>
                            </span>
                            <span class="text-sm font-medium text-text-primary">Atur Petugas</span>
                        </a>
                        <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-accent-primary/5 border border-transparent hover:border-accent-primary/15 transition-all duration-200 group">
                            <span class="w-9 h-9 bg-accent-primary/10 rounded-xl flex items-center justify-center group-hover:bg-accent-primary/20 transition-colors">
                                <svg class="w-5 h-5 text-accent-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                            </span>
                            <span class="text-sm font-medium text-text-primary">Verifikasi Hasil</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Recent Activity (shared) --}}
    <div class="mt-8 bg-surface rounded-2xl border border-border-default p-6">
        <h2 class="font-display text-xl font-bold text-text-primary mb-4">Aktivitas Terbaru</h2>
        <div class="space-y-3">
            <div class="flex items-center gap-4 p-3 rounded-xl bg-base">
                <span class="w-9 h-9 bg-accent-primary/10 rounded-full flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-accent-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="16"/>
                        <line x1="8" y1="12" x2="16" y2="12"/>
                    </svg>
                </span>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-text-primary">Selamat datang di dashboard Pap Sampah</p>
                    <p class="text-xs text-text-muted mt-0.5">Sistem siap digunakan</p>
                </div>
                <span class="text-xs text-text-muted shrink-0">Baru saja</span>
            </div>
        </div>
    </div>

</div>
@endsection
