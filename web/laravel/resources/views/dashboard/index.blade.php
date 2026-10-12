@extends('layouts.app')

@section('title', 'Dashboard — Pap Sampah')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">

    {{-- Ringkasan --}}
    <div class="bg-surface rounded-2xl border border-border-default p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center gap-6">

            {{-- Progress penyelesaian --}}
            <div class="lg:flex-1">
                <div class="flex items-baseline justify-between mb-2">
                    <span class="text-sm font-medium text-text-muted">Tingkat Penyelesaian</span>
                    <span class="font-display text-3xl font-black text-accent-primary">{{ $stats['completion_rate'] }}%</span>
                </div>
                <div class="w-full h-2.5 rounded-full bg-base overflow-hidden">
                    <div class="h-full bg-accent-primary rounded-full transition-all" style="width: {{ $stats['completion_rate'] }}%"></div>
                </div>
                <p class="text-xs text-text-muted mt-2">
                    <span class="font-semibold text-text-primary">{{ $stats['resolved'] }}</span> dari
                    <span class="font-semibold text-text-primary">{{ $stats['total'] }}</span> laporan selesai ditangani
                </p>
            </div>

            {{-- Metrik ringkas (khusus admin kelurahan) --}}
            @unless($user->hasRole('super_admin_kecamatan'))
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 lg:flex-none">
                    <div class="rounded-xl border border-border-default bg-base px-4 py-3">
                        <div class="text-xs text-text-muted mb-1">Perlu Validasi</div>
                        <div class="font-display text-xl font-black text-accent-warning">{{ $stats['pending_validation'] }}</div>
                    </div>
                    <div class="rounded-xl border border-border-default bg-base px-4 py-3">
                        <div class="text-xs text-text-muted mb-1">Sedang Ditangani</div>
                        <div class="font-display text-xl font-black text-accent-primary">{{ $stats['in_progress'] }}</div>
                    </div>
                    <div class="rounded-xl border border-border-default bg-base px-4 py-3">
                        <div class="text-xs text-text-muted mb-1">Menunggu Verifikasi</div>
                        <div class="font-display text-xl font-black text-accent-warning">{{ $stats['pending_verification'] }}</div>
                    </div>
                    <div class="rounded-xl border border-border-default bg-base px-4 py-3">
                        <div class="text-xs text-text-muted mb-1">Tren 7 Hari</div>
                        <div class="font-display text-xl font-black {{ $trendDelta >= 0 ? 'text-accent-primary' : 'text-accent-danger' }}">
                            {{ $trendDelta >= 0 ? '▲' : '▼' }} {{ abs($trendDelta) }}%
                        </div>
                    </div>
                </div>
            @endunless
        </div>
    </div>

    {{-- Tren & Distribusi --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        {{-- Tren 7 Hari --}}
        <div class="bg-surface rounded-2xl border border-border-default p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-display text-xl font-bold text-text-primary">Tren Laporan (7 Hari)</h2>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $trendDelta >= 0 ? 'bg-accent-primary/10 text-accent-primary' : 'bg-accent-danger/10 text-accent-danger' }}">
                    {{ $trendDelta >= 0 ? '▲' : '▼' }} {{ abs($trendDelta) }}%
                </span>
            </div>
            @php $maxTrend = max(1, max(array_column($trend, 'count'))); @endphp
            <div class="flex items-end gap-2 h-40">
                @foreach($trend as $t)
                    <div class="flex-1 h-full flex flex-col items-center gap-1">
                        <span class="text-xs font-semibold text-text-primary">{{ $t['count'] }}</span>
                        <div class="flex-1 w-full flex items-end">
                            <div class="w-full bg-accent-primary rounded-t-lg" style="height: {{ $t['count'] > 0 ? max(5, round($t['count'] / $maxTrend * 100)) : 2 }}%"></div>
                        </div>
                        <span class="text-xs text-text-muted">{{ $t['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Distribusi Status --}}
        <div class="bg-surface rounded-2xl border border-border-default p-6">
            <h2 class="font-display text-xl font-bold text-text-primary mb-4">Distribusi Status</h2>
            @php
                $statusTotal = array_sum(array_column($statusBreakdown, 'count'));
                $stops = [];
                $acc = 0;
                foreach ($statusBreakdown as $s) {
                    $start = $statusTotal > 0 ? $acc / $statusTotal * 100 : 0;
                    $acc += $s['count'];
                    $end = $statusTotal > 0 ? $acc / $statusTotal * 100 : 0;
                    $stops[] = "{$s['color']} {$start}% {$end}%";
                }
                $donutBg = $statusTotal > 0 ? 'conic-gradient(' . implode(', ', $stops) . ')' : '#FAFAF5';
            @endphp
            @if($statusTotal === 0)
                <p class="text-sm text-text-muted py-6 text-center">Belum ada data laporan.</p>
            @else
                <div class="flex flex-col sm:flex-row items-center gap-6">
                    <div class="relative w-40 h-40 shrink-0 rounded-full" style="background: {{ $donutBg }};">
                        <div class="absolute inset-[24%] rounded-full bg-surface"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="font-display text-2xl font-black text-text-primary">{{ $statusTotal }}</span>
                        </div>
                    </div>
                    <div class="flex-1 w-full space-y-2">
                        @foreach($statusBreakdown as $s)
                            <div class="flex items-center gap-2 text-sm">
                                <span class="w-3 h-3 rounded-full shrink-0" style="background: {{ $s['color'] }}"></span>
                                <span class="flex-1 text-text-muted truncate">{{ $s['label'] }}</span>
                                <span class="font-semibold text-text-primary">{{ $s['count'] }}</span>
                                <span class="text-xs text-text-muted w-10 text-right">{{ round($s['count'] / $statusTotal * 100) }}%</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Kategori & Petugas Teraktif --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        {{-- Kategori Terbanyak --}}
        <div class="bg-surface rounded-2xl border border-border-default p-6">
            <h2 class="font-display text-xl font-bold text-text-primary mb-4">Kategori Terbanyak</h2>
            @if($topCategories->isEmpty())
                <p class="text-sm text-text-muted py-6 text-center">Belum ada data laporan.</p>
            @else
                @php $maxCat = max(1, $topCategories->max('total')); @endphp
                <div class="space-y-3">
                    @foreach($topCategories as $cat)
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="text-text-muted truncate">{{ $cat->name }}</span>
                                <span class="font-semibold text-text-primary">{{ $cat->total }}</span>
                            </div>
                            <div class="w-full h-2 rounded-full bg-base overflow-hidden">
                                <div class="h-full rounded-full bg-accent-secondary" style="width: {{ round($cat->total / $maxCat * 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Petugas Teraktif (bulan ini) --}}
        <div class="bg-surface rounded-2xl border border-border-default p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-display text-xl font-bold text-text-primary">Petugas Teraktif</h2>
                <span class="text-xs text-text-muted">Bulan ini</span>
            </div>
            @if($topWorkers->isEmpty())
                <p class="text-sm text-text-muted py-6 text-center">Belum ada tugas selesai bulan ini.</p>
            @else
                <div class="space-y-2">
                    @foreach($topWorkers as $i => $w)
                        <div class="flex items-center gap-3 p-2.5 rounded-xl bg-base">
                            <span class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0 {{ $i === 0 ? 'bg-accent-secondary text-text-primary' : 'bg-accent-primary/10 text-accent-primary' }}">
                                {{ $i + 1 }}
                            </span>
                            <span class="flex-1 text-sm font-medium text-text-primary truncate">{{ $w->name }}</span>
                            <span class="text-xs font-semibold text-accent-primary shrink-0">{{ $w->total }} tugas</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Role-specific content --}}
    @if($user->hasRole('super_admin_kecamatan'))
        {{-- Laporan Aktif per Kelurahan --}}
        <div class="bg-surface rounded-2xl border border-border-default p-6 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                <h2 class="font-display text-xl font-bold text-text-primary">Laporan Aktif per Kelurahan</h2>
                <div class="flex flex-wrap items-center gap-3 text-xs text-text-muted">
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full" style="background:#FF9D23"></span> Menunggu Validasi</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full" style="background:#5B7E3C"></span> Sedang Ditangani</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full" style="background:#8B5CF6"></span> Menunggu Verifikasi</span>
                </div>
            </div>
            @if($topVillages->isEmpty())
                <p class="text-sm text-text-muted py-6 text-center">Belum ada laporan aktif.</p>
            @else
                @php $maxVillage = max(1, $topVillages->max('total')); @endphp
                <div class="space-y-3">
                    @foreach($topVillages as $v)
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="text-text-muted truncate">{{ $v->name }}</span>
                                <span class="font-semibold text-text-primary">{{ $v->total }}</span>
                            </div>
                            <div class="w-full h-3 rounded-full bg-base overflow-hidden">
                                @if($v->total > 0)
                                    <div class="h-full flex" style="width: {{ round($v->total / $maxVillage * 100) }}%">
                                        <div style="width: {{ $v->pending / $v->total * 100 }}%; background:#FF9D23" title="Menunggu Validasi: {{ $v->pending }}"></div>
                                        <div style="width: {{ $v->in_progress / $v->total * 100 }}%; background:#5B7E3C" title="Sedang Ditangani: {{ $v->in_progress }}"></div>
                                        <div style="width: {{ $v->pending_verification / $v->total * 100 }}%; background:#8B5CF6" title="Menunggu Verifikasi: {{ $v->pending_verification }}"></div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Aksi Cepat --}}
        <div class="bg-surface rounded-2xl border border-border-default p-5">
            <h3 class="font-display text-lg font-bold text-text-primary mb-4">Aksi Cepat</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                <a href="{{ route('map.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-accent-primary/5 border border-transparent hover:border-accent-primary/15 transition-all duration-200 group">
                    <span class="w-9 h-9 bg-accent-primary/10 rounded-xl flex items-center justify-center group-hover:bg-accent-primary/20 transition-colors">
                        <svg class="w-5 h-5 text-accent-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </span>
                    <span class="text-sm font-medium text-text-primary">Lihat Peta</span>
                </a>
                <a href="{{ route('reports.index', ['status' => 'IN_PROGRESS']) }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-accent-primary/5 border border-transparent hover:border-accent-primary/15 transition-all duration-200 group">
                    <span class="w-9 h-9 bg-accent-warning/10 rounded-xl flex items-center justify-center group-hover:bg-accent-warning/20 transition-colors">
                        <svg class="w-5 h-5 text-accent-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            <line x1="12" y1="9" x2="12" y2="13"/>
                            <line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                    </span>
                    <span class="text-sm font-medium text-text-primary">Laporan Aktif</span>
                </a>
                <a href="{{ route('reports.index', ['status' => 'PENDING_VALIDATION']) }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-accent-warning/5 border border-transparent hover:border-accent-warning/15 transition-all duration-200 group">
                    <span class="w-9 h-9 bg-accent-warning/10 rounded-xl flex items-center justify-center group-hover:bg-accent-warning/20 transition-colors">
                        <svg class="w-5 h-5 text-accent-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 11l3 3L22 4"/>
                            <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                        </svg>
                    </span>
                    <span class="text-sm font-medium text-text-primary">Validasi Laporan</span>
                </a>
            </div>
        </div>
    @endif

    @if($user->hasRole('admin_desa'))
        {{-- Admin Desa --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Recent Reports --}}
            <div class="lg:col-span-2 bg-surface rounded-2xl border border-border-default p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="font-display text-xl font-bold text-text-primary">Laporan Terbaru</h2>
                        <span class="text-xs text-text-muted">Wilayah {{ $user->village?->name }}</span>
                    </div>
                    <a href="{{ route('reports.index') }}" class="text-xs font-semibold text-accent-primary hover:underline flex items-center gap-1">
                        Lihat Semua
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                @if($recentReports->isEmpty())
                    {{-- Empty state --}}
                    <div class="bg-base rounded-2xl border border-border-default p-8 text-center">
                        <svg class="w-12 h-12 text-text-muted/30 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                        </svg>
                        <p class="text-sm text-text-primary font-medium">Belum ada laporan masuk</p>
                        <p class="text-xs text-text-muted mt-1">Laporan dari masyarakat di wilayah kelurahan ini akan muncul di sini</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($recentReports as $rep)
                            <a href="{{ route('reports.show', $rep->id) }}" class="p-4 rounded-xl bg-base border border-border-default flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 hover:border-accent-primary/50 hover:bg-surface transition-all block group">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-xs font-bold text-text-primary group-hover:text-accent-primary transition-colors">{{ $rep->report_code }}</span>
                                        <span class="text-xs text-text-muted">• {{ $rep->category?->name ?? 'Sampah' }}</span>
                                    </div>
                                    <p class="text-sm text-text-primary line-clamp-1">{{ $rep->description ?: 'Tidak ada deskripsi' }}</p>
                                    <p class="text-xs text-text-muted">Dilaporkan {{ $rep->created_at->diffForHumans() }} oleh {{ $rep->reporter?->name ?? 'Warga' }}</p>
                                </div>
                                <div class="flex items-center gap-2 self-start sm:self-center shrink-0">
                                    @if($rep->status === 'PENDING_VALIDATION')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-accent-warning/10 text-accent-warning">Menunggu Validasi</span>
                                    @elseif($rep->status === 'VALIDATED')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-accent-secondary/30 text-text-primary">Tervalidasi</span>
                                    @elseif($rep->status === 'ASSIGNED' || $rep->status === 'IN_PROGRESS')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-accent-warning/15 text-accent-warning">Sedang Ditangani</span>
                                    @elseif($rep->status === 'RESOLVED')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-accent-primary/10 text-accent-primary">Selesai</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-base text-text-muted border border-border-default">{{ $rep->status }}</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-4">
                {{-- Info Desa --}}
                <div class="bg-surface rounded-2xl border border-border-default p-5">
                    <h3 class="font-display text-lg font-bold text-text-primary mb-4">Informasi Kelurahan</h3>
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
                                <span class="font-medium text-accent-primary">Admin Kelurahan</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-text-muted">Kelurahan</span>
                                <span class="font-medium text-text-primary">{{ $user->village?->name ?? '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Aksi Cepat --}}
                <div class="bg-surface rounded-2xl border border-border-default p-5">
                    <h3 class="font-display text-lg font-bold text-text-primary mb-4">Aksi Cepat</h3>
                    <div class="space-y-2">
                        <a href="{{ route('petugas.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-accent-primary/5 border border-transparent hover:border-accent-primary/15 transition-all duration-200 group">
                            <span class="w-9 h-9 bg-accent-primary/10 rounded-xl flex items-center justify-center group-hover:bg-accent-primary/20 transition-colors">
                                <svg class="w-5 h-5 text-accent-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <line x1="19" y1="8" x2="19" y2="14"/>
                                    <line x1="22" y1="11" x2="16" y2="11"/>
                                </svg>
                            </span>
                            <div>
                                <span class="block text-sm font-medium text-text-primary">Kelola Petugas</span>
                                <span class="block text-xs text-text-muted">Daftar & atur tim kebersihan</span>
                            </div>
                        </a>
                        <a href="{{ route('reports.index', ['status' => 'PENDING_VALIDATION']) }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-accent-warning/5 border border-transparent hover:border-accent-warning/15 transition-all duration-200 group">
                            <span class="w-9 h-9 bg-accent-warning/10 rounded-xl flex items-center justify-center group-hover:bg-accent-warning/20 transition-colors">
                                <svg class="w-5 h-5 text-accent-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                    <line x1="12" y1="9" x2="12" y2="13"/>
                                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                                </svg>
                            </span>
                            <div>
                                <span class="block text-sm font-medium text-text-primary">Validasi Laporan</span>
                                <span class="block text-xs text-text-muted">Tinjau laporan masuk</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
