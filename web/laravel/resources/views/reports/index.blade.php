@extends('layouts.app')

@section('title', 'Laporan Sampah - Pap Sampah')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold font-serif text-emerald-950 tracking-tight">Daftar Laporan Sampah</h1>
            <p class="text-sm text-slate-500 mt-1">
                @if($isSuperAdmin)
                    Monitoring dan rekap seluruh laporan sampah di wilayah Kecamatan Sumbersari.
                @else
                    Kelola dan validasi laporan sampah liar masyarakat di wilayah <strong>Kelurahan {{ auth()->user()->village?->name ?? 'Anda' }}</strong>.
                @endif
            </p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-800 border border-emerald-200/80">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Total {{ $statusCounts->all_count ?? 0 }} Laporan
            </span>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-slate-200 text-sm">
        @php
            $currentStatus = request('status', 'ALL');
            $tabs = [
                ['key' => 'ALL', 'label' => 'Semua', 'count' => $statusCounts->all_count ?? 0],
                ['key' => 'PENDING_VALIDATION', 'label' => 'Menunggu Validasi', 'count' => $statusCounts->pending_count ?? 0, 'highlight' => true],
                ['key' => 'VALIDATED', 'label' => 'Tervalidasi', 'count' => $statusCounts->validated_count ?? 0],
                ['key' => 'IN_PROGRESS', 'label' => 'Dalam Penanganan', 'count' => $statusCounts->active_count ?? 0],
                ['key' => 'RESOLVED', 'label' => 'Selesai', 'count' => $statusCounts->resolved_count ?? 0],
                ['key' => 'REJECTED', 'label' => 'Ditolak', 'count' => $statusCounts->rejected_count ?? 0],
            ];
        @endphp

        @foreach($tabs as $tab)
            @php
                $isActive = $currentStatus === $tab['key'];
            @endphp
            <a href="{{ request()->fullUrlWithQuery(['status' => $tab['key'], 'page' => 1]) }}"
               class="flex items-center gap-2 px-3.5 py-2 rounded-xl font-medium transition whitespace-nowrap {{ $isActive ? 'bg-emerald-800 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <span>{{ $tab['label'] }}</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $isActive ? 'bg-emerald-700/80 text-white' : ($tab['key'] === 'PENDING_VALIDATION' && $tab['count'] > 0 ? 'bg-amber-100 text-amber-800' : 'bg-slate-200 text-slate-700') }}">
                    {{ $tab['count'] }}
                </span>
            </a>
        @endforeach
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-col sm:flex-row gap-3 items-center">
            <input type="hidden" name="status" value="{{ request('status', 'ALL') }}">

            <!-- Search Field -->
            <div class="relative flex-1 w-full">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari kode laporan, pelapor, atau deskripsi (tekan Enter)..."
                       class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 text-sm placeholder-slate-400">
            </div>

            <!-- Category Filter -->
            <div class="w-full sm:w-auto">
                <select name="category_id"
                        onchange="this.form.submit()"
                        class="w-full sm:w-48 px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 text-sm text-slate-700 bg-white">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Village Filter (Super Admin only) -->
            @if($isSuperAdmin)
                <div class="w-full sm:w-auto">
                    <select name="village_id"
                            onchange="this.form.submit()"
                            class="w-full sm:w-52 px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 text-sm text-slate-700 bg-white">
                        <option value="">Semua Kelurahan</option>
                        @foreach($villages as $vil)
                            <option value="{{ $vil->id }}" {{ request('village_id') == $vil->id ? 'selected' : '' }}>
                                Kelurahan {{ $vil->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if(request()->anyFilled(['search', 'category_id', 'village_id']))
                <a href="{{ route('reports.index', ['status' => request('status', 'ALL')]) }}" class="px-3 py-2 text-slate-500 hover:text-slate-800 text-sm font-medium transition whitespace-nowrap self-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Reports Grid List -->
    @if($reports->isEmpty())
        <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-12 text-center">
            <div class="w-14 h-14 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="text-base font-semibold text-slate-800">Tidak ada laporan ditemukan</h3>
            <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">
                Belum ada laporan sampah liar dengan kriteria filter yang Anda pilih.
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($reports as $rep)
                @php
                    $firstPhoto = $rep->photos->first();
                    $badgeStyles = match($rep->status) {
                        'PENDING_VALIDATION' => 'bg-amber-50 text-amber-700 border-amber-200/80',
                        'VALIDATED' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                        'ASSIGNED', 'IN_PROGRESS' => 'bg-blue-50 text-blue-700 border-blue-200/80',
                        'PENDING_VERIFICATION' => 'bg-purple-50 text-purple-700 border-purple-200/80',
                        'RESOLVED' => 'bg-green-50 text-green-700 border-green-200/80',
                        'REJECTED' => 'bg-rose-50 text-rose-700 border-rose-200/80',
                        default => 'bg-slate-50 text-slate-700 border-slate-200',
                    };
                @endphp

                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition flex flex-col overflow-hidden group">
                    <!-- Photo Header Thumbnail -->
                    <div class="relative h-44 bg-slate-100 overflow-hidden">
                        @if($firstPhoto && $firstPhoto->url)
                            <img src="{{ $firstPhoto->url }}"
                                 alt="Foto Laporan"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 bg-gradient-to-br from-slate-100 to-slate-200">
                                <svg class="w-8 h-8 opacity-40 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-xs">Foto tidak tersedia</span>
                            </div>
                        @endif

                        <!-- Photo count badge -->
                        @if($rep->photos->count() > 1)
                            <span class="absolute bottom-2.5 right-2.5 px-2 py-0.5 rounded-md text-xs font-semibold bg-slate-900/70 text-white backdrop-blur-xs flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $rep->photos->count() }} Foto
                            </span>
                        @endif

                        <!-- Status Badge -->
                        <div class="absolute top-2.5 left-2.5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold border backdrop-blur-xs shadow-xs {{ $badgeStyles }}">
                                {{ $rep->status_label }}
                            </span>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                        <div>
                            <div class="flex items-center justify-between gap-2 text-xs text-slate-500 mb-1">
                                <span class="font-mono font-medium text-slate-700 bg-slate-100 px-2 py-0.5 rounded">
                                    {{ $rep->report_code }}
                                </span>
                                <span>{{ $rep->created_at->diffForHumans() }}</span>
                            </div>

                            <h4 class="font-semibold text-slate-900 text-sm line-clamp-1 group-hover:text-emerald-800 transition">
                                Kelurahan {{ $rep->village?->name }}
                            </h4>

                            <div class="mt-1 flex items-center gap-2">
                                <span class="inline-flex items-center text-xs font-medium text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/50">
                                    {{ $rep->category?->name }}
                                </span>
                                <span class="text-xs text-slate-400">•</span>
                                <span class="text-xs text-slate-500 truncate">
                                    Oleh: {{ $rep->reporter?->name }}
                                </span>
                            </div>

                            <p class="text-xs text-slate-600 mt-2 line-clamp-2 leading-relaxed">
                                {{ $rep->description ?: 'Tidak ada deskripsi tambahan dari pelapor.' }}
                            </p>
                        </div>

                        <!-- Card Footer -->
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-xs font-mono text-slate-400">
                                {{ number_format($rep->latitude, 4) }}, {{ number_format($rep->longitude, 4) }}
                            </span>
                            <a href="{{ route('reports.show', $rep->id) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-emerald-800 hover:bg-emerald-50 transition">
                                Detail & Aksi
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $reports->links() }}
        </div>
    @endif
</div>
@endsection
