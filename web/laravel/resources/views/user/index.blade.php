@extends('layouts.app')

@section('title', 'Daftar Masyarakat — Pap Sampah')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">

    {{-- Breadcrumb & Header --}}
    <div class="mb-8">
        <h1 class="font-display text-3xl font-bold text-text-primary">Data Masyarakat</h1>
        <p class="text-text-muted text-sm mt-1">
            Monitoring dan pengelolaan status akun masyarakat / warga terdaftar
        </p>
    </div>

    {{-- Alert Error / Peringatan (Menggunakan Warna Merah/Rose Agar Sesuai Fungsi Warning) --}}
    @if (session('error'))
        <div class="mb-4 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Filter & Search Card --}}
    <div class="bg-surface rounded-2xl border border-border-default p-4 mb-6">
        <form method="GET" action="{{ route('user.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
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

            {{-- Status Filter --}}
            <div class="w-full sm:w-48">
                <select name="status" onchange="this.form.submit()" class="w-full px-3.5 py-2 bg-base border border-border-default rounded-xl text-sm text-text-primary focus:outline-none focus:ring-2 focus:ring-accent-primary/20 focus:border-accent-primary transition-all">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Terkunci / Nonaktif</option>
                </select>
            </div>

            <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-base border border-border-default hover:bg-border-default/10 rounded-xl text-sm font-medium text-text-primary transition-colors">
                Cari
            </button>

            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('user.index') }}" class="w-full sm:w-auto px-4 py-2 text-sm text-text-muted hover:text-accent-danger transition-colors text-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Table Card --}}
    <div class="bg-surface rounded-2xl border border-border-default overflow-hidden">
        @if($users->isEmpty())
            <div class="p-12 text-center">
                <div class="w-14 h-14 bg-base rounded-2xl flex items-center justify-center mx-auto mb-4 text-text-muted/40 border border-border-default">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 00-3-3.87"/>
                        <path d="M16 3.13a4 4 0 010 7.75"/>
                    </svg>
                </div>
                <h3 class="font-display text-lg font-bold text-text-primary mb-1">Tidak Ada Data Masyarakat</h3>
                <p class="text-sm text-text-muted max-w-md mx-auto">
                    @if(request()->hasAny(['search', 'status']))
                        Tidak ada warga yang cocok dengan pencarian/filter Anda.
                    @else
                        Belum ada akun masyarakat terdaftar di dalam sistem.
                    @endif
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-[700px] w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-border-default bg-base/50 text-xs font-semibold text-text-muted uppercase tracking-wider">
                            <th class="py-3.5 px-6">Nama Warga</th>
                            <th class="py-3.5 px-6">Kontak</th>
                            <th class="py-3.5 px-6 text-center">Status Akun</th>
                            <th class="py-3.5 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-default text-sm">
                        @foreach($users as $user)
                        <tr class="hover:bg-base/30 transition-colors">
                            {{-- User name & initial --}}
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 bg-accent-primary/10 text-accent-primary font-bold rounded-xl flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </span>
                                    <div>
                                        <div class="font-medium text-text-primary">{{ $user->name }}</div>
                                        <div class="text-xs text-text-muted">ID: #{{ $user->id }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Kontak --}}
                            <td class="py-4 px-6">
                                <div class="text-text-primary">{{ $user->email }}</div>
                                <div class="text-xs text-text-muted">{{ $user->phone ?? 'Belum ada telepon' }}</div>
                            </td>

                            {{-- Status --}}
                            <td class="py-4 px-6 text-center">
                                @if($user->is_active ?? true)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-accent-primary/10 text-accent-primary">
                                        <span class="w-1.5 h-1.5 rounded-full bg-accent-primary"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-accent-danger/10 text-accent-danger">
                                        <span class="w-1.5 h-1.5 rounded-full bg-accent-danger"></span>
                                        Terkunci
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    
                                    {{-- Tombol Toggle Lock / Unlock Status --}}
                                    <form method="POST" action="{{ route('user.toggle-status', $user->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        @if($user->is_active ?? true)
                                            <button type="submit" class="p-2 text-text-muted hover:text-accent-warning hover:bg-accent-warning/10 rounded-lg transition-colors" title="Kunci / Nonaktifkan Akun" onclick="return confirm('Kunci akun warga ini? Pengguna tidak akan bisa login.')">
                                                {{-- Icon Unlocked (Klik untuk mengunci) --}}
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                                    <path d="M7 11V7a5 5 0 0 1 9.9-1"></path>
                                                </svg>
                                            </button>
                                        @else
                                            <button type="submit" class="p-2 text-accent-danger hover:text-accent-primary hover:bg-accent-primary/10 rounded-lg transition-colors" title="Buka Kunci / Aktifkan Akun" onclick="return confirm('Buka kunci akun warga ini?')">
                                                {{-- Icon Locked (Klik untuk membuka kunci) --}}
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </form>

                                    {{-- Edit button --}}
                                    <a href="{{ route('user.edit', $user->id) }}" class="p-2 text-text-muted hover:text-accent-primary hover:bg-accent-primary/10 rounded-lg transition-colors" title="Edit Data Warga">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                    </a>

                                    {{-- Delete button --}}
                                    <form method="POST" action="{{ route('user.destroy', $user->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun masyarakat ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-text-muted hover:text-accent-danger hover:bg-accent-danger/10 rounded-lg transition-colors" title="Hapus Akun Warga">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            </svg>
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
            @if($users->hasPages())
                <div class="p-4 border-t border-border-default">
                    {{ $users->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection