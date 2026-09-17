@extends('layouts.app')

@section('title', "Detail Laporan {$report->report_code} - Pap Sampah")

@push('head')
<!-- Leaflet CSS & JS for Interactive Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<style>
    .leaflet-popup-content-wrapper {
        border-radius: 1rem;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('reports.index') }}"
               class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition shadow-2xs">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-2xl font-bold font-serif text-emerald-950">{{ $report->report_code }}</h1>
                    @php
                        $badgeStyles = match($report->status) {
                            'PENDING_VALIDATION' => 'bg-amber-50 text-amber-800 border-amber-300',
                            'VALIDATED' => 'bg-emerald-50 text-emerald-800 border-emerald-300',
                            'ASSIGNED', 'IN_PROGRESS' => 'bg-blue-50 text-blue-800 border-blue-300',
                            'PENDING_VERIFICATION' => 'bg-purple-50 text-purple-800 border-purple-300',
                            'RESOLVED' => 'bg-green-100 text-green-900 border-green-400',
                            'REJECTED' => 'bg-rose-50 text-rose-800 border-rose-300',
                            default => 'bg-slate-100 text-slate-800 border-slate-200',
                        };
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $badgeStyles }}">
                        {{ $report->status_label }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    Dilaporkan pada {{ $report->created_at->translatedFormat('l, d F Y - H:i') }} WIB ({{ $report->created_at->diffForHumans() }})
                </p>
            </div>
        </div>

        <!-- Action Buttons (For Pending Validation) -->
        @if($report->isPendingValidation())
            <div class="flex items-center gap-3">
                <!-- Reject Button (Opens Modal) -->
                <button type="button"
                        onclick="openRejectModal()"
                        class="px-4 py-2 bg-white hover:bg-rose-50 text-rose-700 hover:text-rose-800 border border-rose-300 rounded-xl text-sm font-semibold transition shadow-2xs flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Tolak Laporan
                </button>

                <!-- Approve Button (Form Submit) -->
                <form method="POST" action="{{ route('reports.validate', $report->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui laporan ini?')">
                    @csrf
                    <button type="submit"
                            class="px-5 py-2 bg-emerald-800 hover:bg-emerald-900 text-white rounded-xl text-sm font-semibold transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Setujui Laporan
                    </button>
                </form>
            </div>
        @elseif($report->isValidated())
            <div class="flex items-center gap-2">
                <span class="px-3.5 py-1.5 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl text-xs font-medium flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Laporan Tervalidasi
                </span>
            </div>
        @endif
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Main Content 2 Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left Column: Photos & Interactive Map (7 Cols) -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Photo Evidence Gallery -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-bold font-serif text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Foto Bukti Sampah
                    </h2>
                    <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">
                        {{ $report->photos->count() }} Foto
                    </span>
                </div>

                @if($report->photos->isEmpty())
                    <div class="py-12 text-center text-slate-400 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                        Tidak ada foto yang diunggah.
                    </div>
                @else
                    <!-- Main Preview Photo -->
                    @php $mainPhoto = $report->photos->first(); @endphp
                    <div class="relative rounded-xl overflow-hidden bg-slate-100 border border-slate-200 h-72 sm:h-96 group">
                        <img id="mainPhotoImg"
                             src="{{ $mainPhoto->url }}"
                             alt="Foto Laporan Utama"
                             class="w-full h-full object-cover cursor-pointer transition group-hover:scale-101"
                             onclick="showImageModal(this.src)">
                        <div class="absolute bottom-3 right-3">
                            <button type="button"
                                    onclick="showImageModal(document.getElementById('mainPhotoImg').src)"
                                    class="p-2 rounded-xl bg-slate-900/70 hover:bg-slate-900 text-white backdrop-blur-xs transition text-xs flex items-center gap-1.5 shadow-md">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                </svg>
                                Perbesar
                            </button>
                        </div>
                    </div>

                    <!-- Thumbnails list (if multiple) -->
                    @if($report->photos->count() > 1)
                        <div class="flex items-center gap-3 mt-3 overflow-x-auto pb-2">
                            @foreach($report->photos as $idx => $photo)
                                <button type="button"
                                        onclick="switchPhoto('{{ $photo->url }}', this)"
                                        class="shrink-0 w-20 h-16 rounded-lg overflow-hidden border-2 transition {{ $idx === 0 ? 'border-emerald-600 ring-2 ring-emerald-500/20' : 'border-slate-200 opacity-70 hover:opacity-100' }}">
                                    <img src="{{ $photo->url }}" alt="Thumbnail" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>

            <!-- Interactive PostGIS Location Map -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-base font-bold font-serif text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Peta Titik Lokasi & Batas Kelurahan
                    </h2>
                    <a href="https://www.google.com/maps?q={{ $report->latitude }},{{ $report->longitude }}"
                       target="_blank"
                       rel="noopener"
                       class="text-xs font-semibold text-emerald-800 hover:text-emerald-950 flex items-center gap-1">
                        Buka Google Maps
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                </div>

                <div id="reportMap" class="w-full h-72 sm:h-80 rounded-xl overflow-hidden border border-slate-200 z-0"></div>

                <div class="mt-3 flex items-center justify-between text-xs text-slate-500">
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-sm bg-emerald-600/30 border border-emerald-700"></span>
                        Batas Wilayah: Kelurahan {{ $report->village?->name }}
                    </span>
                    <span class="font-mono text-slate-600 font-medium">
                        Lat: {{ $report->latitude }}, Lng: {{ $report->longitude }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Right Column: Information, Reporter, & Timeline (5 Cols) -->
        <div class="lg:col-span-5 space-y-6">
            <!-- Details Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs space-y-4">
                <h2 class="text-base font-bold font-serif text-slate-900 border-b border-slate-100 pb-3">
                    Informasi Laporan
                </h2>

                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block mb-1">Kategori Sampah</span>
                        <span class="font-semibold text-slate-900 bg-emerald-50 text-emerald-800 px-2.5 py-1 rounded-lg border border-emerald-200/60 inline-block">
                            {{ $report->category?->name }}
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Kelurahan Wilayah</span>
                        <span class="font-semibold text-slate-900 block text-sm">
                            {{ $report->village?->name }}
                        </span>
                    </div>
                </div>

                <div class="pt-2">
                    <span class="text-xs text-slate-400 block mb-1">Deskripsi Kondisi Sampah</span>
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-xs text-slate-700 leading-relaxed">
                        {{ $report->description ?: 'Tidak ada deskripsi tambahan dari pelapor.' }}
                    </div>
                </div>

                @if($report->validated_by)
                    <div class="pt-2 border-t border-slate-100 text-xs flex items-center justify-between">
                        <span class="text-slate-500">Divalidasi oleh:</span>
                        <span class="font-semibold text-slate-800">
                            {{ $report->validator?->name ?? 'Admin' }} ({{ $report->validated_at?->diffForHumans() }})
                        </span>
                    </div>
                @endif
            </div>

            <!-- Reporter Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <h2 class="text-base font-bold font-serif text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Data Pelapor
                </h2>

                <div class="mt-3 space-y-2.5 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Nama Pelapor:</span>
                        <span class="font-semibold text-slate-900 text-sm">{{ $report->reporter?->name }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Email:</span>
                        <span class="font-medium text-slate-700">{{ $report->reporter?->email }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">No. Telepon:</span>
                        <span class="font-medium text-slate-700 font-mono">{{ $report->reporter?->phone ?: '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Timeline Status History -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <h2 class="text-base font-bold font-serif text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Riwayat Penanganan
                </h2>

                <div class="mt-4 relative pl-6 border-l-2 border-slate-200 space-y-6">
                    @forelse($report->statusHistories as $history)
                        @php
                            $dotColor = match($history->status) {
                                'PENDING_VALIDATION' => 'bg-amber-500',
                                'VALIDATED' => 'bg-emerald-600',
                                'ASSIGNED', 'IN_PROGRESS' => 'bg-blue-600',
                                'PENDING_VERIFICATION' => 'bg-purple-600',
                                'RESOLVED' => 'bg-green-600',
                                'REJECTED' => 'bg-rose-600',
                                default => 'bg-slate-400',
                            };
                        @endphp
                        <div class="relative group">
                            <!-- Timeline Dot -->
                            <span class="absolute -left-[31px] top-1 w-3.5 h-3.5 rounded-full {{ $dotColor }} ring-4 ring-white"></span>

                            <div class="flex items-center justify-between gap-2">
                                <span class="font-semibold text-xs text-slate-900">
                                    {{ $history->status }}
                                </span>
                                <span class="text-2xs text-slate-400">
                                    {{ $history->created_at->format('d/m/y H:i') }}
                                </span>
                            </div>

                            <p class="text-xs text-slate-600 mt-0.5">
                                {{ $history->note ?: '-' }}
                            </p>

                            <div class="text-2xs text-slate-400 mt-1">
                                Oleh: <span class="font-medium text-slate-600">{{ $history->user?->name ?? 'Sistem' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-xs text-slate-400">Belum ada riwayat status.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reject Form -->
<div id="rejectModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl border border-slate-200 overflow-hidden transform transition">
        <form method="POST" action="{{ route('reports.reject', $report->id) }}">
            @csrf
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-bold font-serif text-slate-900 flex items-center gap-2 text-rose-700">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Tolak Laporan Sampah
                </h3>
                <button type="button" onclick="closeRejectModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-5 space-y-3">
                <p class="text-xs text-slate-600 leading-relaxed">
                    Harap berikan catatan alasan penolakan secara jelas. Catatan ini akan dicatat dalam riwayat audit trail dan dapat dilihat oleh pelapor.
                </p>

                <div>
                    <label for="reason" class="block text-xs font-semibold text-slate-700 mb-1">
                        Alasan Penolakan <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="reason"
                              id="reason"
                              rows="4"
                              required
                              minlength="5"
                              maxlength="500"
                              placeholder="Contoh: Titik lokasi yang dilaporkan bukan tumpukan sampah liar, melainkan material bangunan proyek warga..."
                              class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-rose-500/20 focus:border-rose-600 focus:outline-none placeholder-slate-400"></textarea>
                </div>
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button"
                        onclick="closeRejectModal()"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-200 transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded-xl text-xs font-semibold bg-rose-700 hover:bg-rose-800 text-white transition shadow-xs">
                    Konfirmasi Penolakan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Full Image Preview -->
<div id="imageModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 hidden" onclick="closeImageModal()">
    <div class="relative max-w-4xl max-h-[90vh]" onclick="event.stopPropagation()">
        <img id="modalImg" src="" alt="Preview Full" class="max-w-full max-h-[85vh] rounded-xl object-contain shadow-2xl">
        <button type="button"
                onclick="closeImageModal()"
                class="absolute -top-3 -right-3 w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center hover:bg-slate-800 shadow-lg">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Photo switcher
    function switchPhoto(url, btn) {
        document.getElementById('mainPhotoImg').src = url;
        const allBtns = btn.parentElement.querySelectorAll('button');
        allBtns.forEach(b => {
            b.classList.remove('border-emerald-600', 'ring-2', 'ring-emerald-500/20');
            b.classList.add('border-slate-200', 'opacity-70');
        });
        btn.classList.remove('border-slate-200', 'opacity-70');
        btn.classList.add('border-emerald-600', 'ring-2', 'ring-emerald-500/20');
    }

    // Full image modal
    function showImageModal(src) {
        document.getElementById('modalImg').src = src;
        document.getElementById('imageModal').classList.remove('hidden');
    }
    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
    }

    // Reject Modal
    function openRejectModal() {
        document.getElementById('rejectModal').classList.remove('hidden');
    }
    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }

    // Leaflet PostGIS Map Initialization
    document.addEventListener('DOMContentLoaded', function() {
        const reportLat = {{ $report->latitude ?? -8.172 }};
        const reportLng = {{ $report->longitude ?? 113.715 }};

        const map = L.map('reportMap').setView([reportLat, reportLng], 14);

        // OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Render Village Boundary GeoJSON (if available)
        @if(!empty($villageGeoJson))
            try {
                const boundaryData = {!! $villageGeoJson !!};
                const boundaryLayer = L.geoJSON(boundaryData, {
                    style: {
                        color: '#059669',
                        weight: 2,
                        opacity: 0.8,
                        fillColor: '#10b981',
                        fillOpacity: 0.12
                    }
                }).addTo(map);

                // Fit map to boundary if appropriate
                // map.fitBounds(boundaryLayer.getBounds());
            } catch (e) {
                console.warn('GeoJSON render error:', e);
            }
        @endif

        // Custom Report Marker
        const reportIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `
                <div style="background-color: #dc2626; width: 28px; height: 28px; border-radius: 50%; border: 3px solid #ffffff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white;">
                    <svg style="width: 14px; height: 14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
            `,
            iconSize: [28, 28],
            iconAnchor: [14, 14]
        });

        const marker = L.marker([reportLat, reportLng], { icon: reportIcon }).addTo(map);
        marker.bindPopup(`
            <div class="p-1">
                <div class="font-bold text-xs text-slate-900">{{ $report->report_code }}</div>
                <div class="text-2xs text-slate-500">Kelurahan {{ $report->village?->name }}</div>
                <div class="text-2xs font-semibold text-emerald-800 mt-1">{{ $report->category?->name }}</div>
            </div>
        `).openPopup();
    });
</script>
@endpush
