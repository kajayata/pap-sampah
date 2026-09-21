@extends('layouts.app')

@section('title', 'Daftar Petugas Kebersihan — Pap Sampah')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">

    {{-- Breadcrumb & Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-text-muted mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-accent-primary transition-colors">Dashboard</a>
                <span>/</span>
                <span class="text-text-primary">Petugas Kebersihan</span>
            </div>
            <h1 class="font-display text-3xl font-bold text-text-primary">Petugas Kebersihan Desa</h1>
            <p class="text-text-muted text-sm mt-1">
                @if(Auth::user()->hasRole('admin_desa'))
                    Kelola personel tim kebersihan resmi untuk wilayah <strong>{{ Auth::user()->village?->name }}</strong>
                @else
                    Monitoring dan pengelolaan seluruh petugas kebersihan di wilayah Kecamatan Sumbersari
                @endif
            </p>
        </div>

        <div>
            <a href="{{ route('petugas.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-accent-primary text-white font-medium text-sm rounded-xl hover:-translate-y-0.5 shadow-sm hover:shadow transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                <span>Tambah Petugas</span>
            </a>
        </div>
    </div>

    {{-- Filter & Search Card --}}
    <div class="bg-surface rounded-2xl border border-border-default p-4 mb-6">
        <form method="GET" action="{{ route('petugas.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
            {{-- Search input --}}
            <div class="relative flex-1 w-full">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-text-muted/60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau telepon..." class="w-full pl-10 pr-4 py-2 bg-base border border-border-default rounded-xl text-sm text-text-primary placeholder:text-text-muted/60 focus:outline-none focus:ring-2 focus:ring-accent-primary/20 focus:border-accent-primary transition-all">
            </div>

            {{-- Village filter for Super Admin --}}
            @if(Auth::user()->hasRole('super_admin_kecamatan'))
            <div class="w-full sm:w-56">
                <select name="village_id" onchange="this.form.submit()" class="w-full px-3.5 py-2 bg-base border border-border-default rounded-xl text-sm text-text-primary focus:outline-none focus:ring-2 focus:ring-accent-primary/20 focus:border-accent-primary transition-all">
                    <option value="">Semua Kelurahan</option>
                    @foreach($villages as $vil)
                        <option value="{{ $vil->id }}" {{ request('village_id') == $vil->id ? 'selected' : '' }}>{{ $vil->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-base border border-border-default hover:bg-border-default/10 rounded-xl text-sm font-medium text-text-primary transition-colors">
                Cari
            </button>

            @if(request()->hasAny(['search', 'village_id']))
                <a href="{{ route('petugas.index') }}" class="w-full sm:w-auto px-4 py-2 text-sm text-text-muted hover:text-accent-danger transition-colors text-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Table Card --}}
    <div class="bg-surface rounded-2xl border border-border-default overflow-hidden">
        @if($workers->isEmpty())
            <div class="p-12 text-center">
                <div class="w-14 h-14 bg-base rounded-2xl flex items-center justify-center mx-auto mb-4 text-text-muted/40 border border-border-default">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 00-3-3.87"/>
                        <path d="M16 3.13a4 4 0 010 7.75"/>
                    </svg>
                </div>
                <h3 class="font-display text-lg font-bold text-text-primary mb-1">Belum Ada Petugas Terdaftar</h3>
                <p class="text-sm text-text-muted max-w-md mx-auto mb-6">
                    @if(request()->hasAny(['search', 'village_id']))
                        Tidak ada petugas yang cocok dengan kriteria pencarian Anda.
                    @else
                        Daftarkan petugas kebersihan desa agar mereka dapat menerima dan melaksanakan tugas pembersihan sampah melalui aplikasi mobile.
                    @endif
                </p>
                <a href="{{ route('petugas.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-accent-primary text-white text-sm font-medium rounded-xl hover:-translate-y-0.5 transition-all">
                    Tambah Petugas Pertama
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-border-default bg-base/50 text-xs font-semibold text-text-muted uppercase tracking-wider">
                            <th class="py-3.5 px-6">Petugas</th>
                            <th class="py-3.5 px-6">Kontak</th>
                            <th class="py-3.5 px-6">Wilayah Kelurahan</th>
                            <th class="py-3.5 px-6 text-center">Tugas Aktif</th>
                            <th class="py-3.5 px-6 text-center">Status</th>
                            <th class="py-3.5 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-default text-sm">
                        @foreach($workers as $worker)
                        <tr class="hover:bg-base/30 transition-colors">
                            {{-- Petugas name & initial --}}
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 bg-accent-primary/10 text-accent-primary font-bold rounded-xl flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($worker->name, 0, 1)) }}
                                    </span>
                                    <div>
                                        <div class="font-medium text-text-primary">{{ $worker->name }}</div>
                                        <div class="text-xs text-text-muted">ID: #{{ $worker->id }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Kontak --}}
                            <td class="py-4 px-6">
                                <div class="text-text-primary">{{ $worker->email }}</div>
                                <div class="text-xs text-text-muted">{{ $worker->phone ?? 'Belum ada telepon' }}</div>
                            </td>

                            {{-- Wilayah --}}
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-base text-text-primary border border-border-default">
                                    {{ $worker->village?->name ?? '—' }}
                                </span>
                            </td>

                            {{-- Tugas Aktif --}}
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $worker->active_tasks_count > 0 ? 'bg-accent-warning/10 text-accent-warning' : 'bg-base text-text-muted' }}">
                                    {{ $worker->active_tasks_count }} Tugas
                                </span>
                            </td>

                            {{-- Status Aktif --}}
                            <td class="py-4 px-6 text-center">
                                @if($worker->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-accent-primary/10 text-accent-primary">
                                        <span class="w-1.5 h-1.5 rounded-full bg-accent-primary"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-accent-danger/10 text-accent-danger">
                                        <span class="w-1.5 h-1.5 rounded-full bg-accent-danger"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Edit button --}}
                                    <a href="{{ route('petugas.edit', $worker->id) }}" class="p-2 text-text-muted hover:text-accent-primary hover:bg-accent-primary/10 rounded-lg transition-colors" title="Edit Data Petugas">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                    </a>

                                    {{-- Toggle Status button --}}
                                    <form method="POST" action="{{ route('petugas.toggle-status', $worker->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin {{ $worker->is_active ? 'menonaktifkan' : 'mengaktifkan' }} petugas ini?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="p-2 {{ $worker->is_active ? 'text-accent-warning hover:bg-accent-warning/10' : 'text-accent-primary hover:bg-accent-primary/10' }} rounded-lg transition-colors" title="{{ $worker->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                            @if($worker->is_active)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="10"/>
                                                    <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($workers->hasPages())
                <div class="p-4 border-t border-border-default">
                    {{ $workers->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
