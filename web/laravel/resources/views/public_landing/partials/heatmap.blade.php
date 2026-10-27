<!-- Leaflet CSS -->
<link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.css') }}" onerror="this.onerror=null;this.href='https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css'">

<style>
    .leaflet-popup-content-wrapper { border-radius: 1rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); padding: 0; overflow: hidden; }
    .leaflet-popup-content { margin: 0; line-height: 1.4; }
    #mainMap { min-height: 600px; height: 600px; width: 100%; }
</style>

<section id="peta" class="scroll-mt-24 bg-[#fafaf5] py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <!-- Centered Section Header -->
        <div class="text-center max-w-2xl mx-auto">
            <span class="inline-block text-xs font-bold uppercase tracking-wider text-amber-800 bg-amber-50 border border-amber-200 px-3.5 py-1.5 rounded-full">
                Peta & Sebaran
            </span>
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-black text-stone-900 tracking-tight mt-3 font-fraunces" style="font-family: 'Fraunces', Georgia, serif;">
                Peta Sebaran & Heatmap Sampah
            </h2>
            <p class="text-stone-600 text-sm sm:text-base mt-2 leading-relaxed">
                Heatmap menampilkan laporan yang masih aktif. Laporan berstatus selesai tidak lagi dihitung sebagai sampah aktif, tetapi penandanya dapat dilihat hingga H+{{ $markerDisplayDays }}. Peta juga memuat Bank Sampah dan TPA di {{ $district->name }}.
            </p>
        </div>

        <!-- Quick Stats Bar with Icons -->
        @php
            $activePointsCount = count(array_filter($wastePoints, fn($p) => !$p['is_resolved']));
            $resolvedPointsCount = count(array_filter($wastePoints, fn($p) => $p['is_resolved']));
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <!-- Sampah Aktif -->
            <div class="p-5 rounded-2xl bg-white border border-stone-200/90 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-rose-600 uppercase tracking-wider">Sampah Aktif</span>
                    <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-black font-fraunces text-stone-900" style="font-family: 'Fraunces', Georgia, serif;">{{ $activePointsCount }}</span>
                    <span class="text-xs text-stone-400 font-medium">titik pantau</span>
                </div>
            </div>

            <!-- Selesai -->
            <div class="p-5 rounded-2xl bg-white border border-stone-200/90 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Selesai</span>
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-black font-fraunces text-stone-900" style="font-family: 'Fraunces', Georgia, serif;">{{ $resolvedPointsCount }}</span>
                    <span class="text-xs text-stone-400 font-medium">telah dibersihkan</span>
                </div>
            </div>

            <!-- Bank Sampah -->
            <div class="p-5 rounded-2xl bg-white border border-stone-200/90 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-teal-700 uppercase tracking-wider">Bank Sampah</span>
                    <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-700 border border-teal-100 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-black font-fraunces text-stone-900" style="font-family: 'Fraunces', Georgia, serif;">{{ $wasteBanks->count() }}</span>
                    <span class="text-xs text-stone-400 font-medium">unit aktif</span>
                </div>
            </div>

            <!-- TPA / TPS -->
            <div class="p-5 rounded-2xl bg-white border border-stone-200/90 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-indigo-700 uppercase tracking-wider">TPA / TPS</span>
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-700 border border-indigo-100 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-black font-fraunces text-stone-900" style="font-family: 'Fraunces', Georgia, serif;">{{ $landfills->count() }}</span>
                    <span class="text-xs text-stone-400 font-medium">fasilitas rujukan</span>
                </div>
            </div>
        </div>

        <!-- Map Container with Layer Controls -->
        <div class="bg-white rounded-3xl border border-stone-200/90 shadow-sm overflow-hidden flex flex-col">
            <div class="p-4 bg-stone-50/90 border-b border-stone-200 flex flex-wrap items-center justify-between gap-3 text-xs">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-bold text-stone-700 mr-1">Layer Aktif:</span>

                    <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-stone-200 hover:bg-stone-50 cursor-pointer transition shadow-2xs">
                        <input type="checkbox" id="layerActivePoints" checked class="w-3.5 h-3.5 text-rose-600 rounded border-stone-300">
                        <span class="flex items-center gap-1.5 font-medium text-stone-700">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span> Sampah Aktif ({{ $activePointsCount }})
                        </span>
                    </label>

                    <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-stone-200 hover:bg-stone-50 cursor-pointer transition shadow-2xs">
                        <input type="checkbox" id="layerResolvedPoints" checked class="w-3.5 h-3.5 text-emerald-600 rounded border-stone-300">
                        <span class="flex items-center gap-1.5 font-medium text-stone-700">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span> Selesai H+{{ $markerDisplayDays }} ({{ $resolvedPointsCount }})
                        </span>
                    </label>

                    <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-stone-200 hover:bg-stone-50 cursor-pointer transition shadow-2xs">
                        <input type="checkbox" id="layerHeatmap" checked class="w-3.5 h-3.5 text-amber-500 rounded border-stone-300">
                        <span class="flex items-center gap-1.5 font-medium text-stone-700">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Heatmap Kepadatan
                        </span>
                    </label>

                    <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-stone-200 hover:bg-stone-50 cursor-pointer transition shadow-2xs">
                        <input type="checkbox" id="layerWasteBanks" checked class="w-3.5 h-3.5 text-teal-600 rounded border-stone-300">
                        <span class="flex items-center gap-1.5 font-medium text-stone-700">
                            <span class="w-2.5 h-2.5 rounded-full bg-teal-600"></span> Bank Sampah ({{ $wasteBanks->count() }})
                        </span>
                    </label>

                    <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-stone-200 hover:bg-stone-50 cursor-pointer transition shadow-2xs">
                        <input type="checkbox" id="layerLandfills" checked class="w-3.5 h-3.5 text-indigo-600 rounded border-stone-300">
                        <span class="flex items-center gap-1.5 font-medium text-stone-700">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span> TPA / TPS-3R ({{ $landfills->count() }})
                        </span>
                    </label>
                </div>

                <div class="text-[11px] text-stone-500 font-medium">
                    *Penanda laporan selesai disembunyikan setelah H+{{ $markerDisplayDays }}; laporan dan riwayatnya tetap tersimpan.
                </div>
            </div>

            <!-- Interactive Map Element -->
            <div id="mainMap" class="w-full h-[600px] z-0 rounded-b-3xl overflow-hidden" style="min-height: 600px; height: 600px; width: 100%;"></div>
        </div>
    </div>
</section>

<!-- Modal Foto Perbandingan Before / After -->
<div id="beforeAfterModal" class="fixed inset-0 z-[60] bg-stone-950/80 backdrop-blur-sm flex items-center justify-center p-4 hidden" onclick="closeBeforeAfterModal()">
    <div class="bg-white w-full max-w-3xl rounded-3xl shadow-2xl border border-stone-200 overflow-hidden transform transition max-h-[90vh] flex flex-col" onclick="event.stopPropagation()">
        <div class="p-6 border-b border-stone-100 flex items-center justify-between">
            <div>
                <h3 id="modalReportCode" class="text-lg font-bold font-fraunces text-stone-900" style="font-family: 'Fraunces', Georgia, serif;">Bukti Verifikasi Pembersihan</h3>
                <span id="modalReportVillage" class="text-xs text-stone-500 font-medium">Kelurahan</span>
            </div>
            <button type="button" onclick="closeBeforeAfterModal()" class="text-stone-400 hover:text-stone-600 p-1 rounded-xl transition">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="p-6 overflow-y-auto space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-rose-600 bg-rose-50 px-3 py-1 rounded-lg border border-rose-200 block text-center">1. Kondisi Awal (Laporan Warga)</span>
                    <div class="h-64 rounded-2xl overflow-hidden bg-stone-100 border border-stone-200">
                        <img id="modalBeforeImg" src="" alt="Foto Awal" class="w-full h-full object-cover">
                    </div>
                </div>

                <div class="space-y-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-3 py-1 rounded-lg border border-emerald-200 block text-center">2. Hasil Pembersihan (Petugas)</span>
                    <div class="h-64 rounded-2xl overflow-hidden bg-stone-100 border border-stone-200">
                        <img id="modalAfterImg" src="" alt="Foto Hasil" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-stone-50 border border-stone-200 text-xs text-stone-600 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                <span id="modalReportDate" class="font-medium">Dilaporkan pada: -</span>
                <span id="modalResolvedDate" class="font-bold text-emerald-800">Selesai pada: -</span>
            </div>
        </div>

        <div class="p-4 bg-stone-50 border-t border-stone-100 flex justify-end">
            <button type="button" onclick="closeBeforeAfterModal()" class="px-6 py-2.5 rounded-xl text-xs font-semibold bg-stone-900 hover:bg-stone-800 text-white transition shadow-sm">Tutup</button>
        </div>
    </div>
</div>

<!-- Leaflet & Heatmap JS -->
<script src="{{ asset('vendor/leaflet/leaflet.js') }}" onerror="this.onerror=null;this.src='https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js'"></script>
<script src="{{ asset('vendor/leaflet/leaflet-heat.js') }}" onerror="this.onerror=null;this.src='https://cdnjs.cloudflare.com/ajax/libs/leaflet.heat/0.2.0/leaflet-heat.js'"></script>

<script>
    const wastePoints = {!! json_encode($wastePoints) !!};
    const heatmapPoints = {!! json_encode($heatmapData) !!};
    const boundariesData = {!! json_encode($boundariesGeoJson) !!};
    const wasteBanks = {!! json_encode($wasteBanks) !!};
    const landfills = {!! json_encode($landfills) !!};

    function initLeafletMap() {
        if (typeof L === 'undefined') {
            setTimeout(initLeafletMap, 100);
            return;
        }

        const defaultLat = -8.1724;
        const defaultLng = 113.7153;

        const map = L.map('mainMap', {
            minZoom: 13,
            maxZoom: 19
        }).setView([defaultLat, defaultLng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        const invalidate = () => { try { map.invalidateSize(); } catch(e) {} };
        invalidate();
        setTimeout(invalidate, 150);
        setTimeout(invalidate, 500);
        setTimeout(invalidate, 1200);
        window.addEventListener('resize', invalidate);

        let boundaryLayer = null;
        if (boundariesData && boundariesData.features && boundariesData.features.length > 0) {
            boundaryLayer = L.geoJSON(boundariesData, {
                style: {
                    color: '#047857',
                    weight: 2,
                    opacity: 0.7,
                    fillColor: '#10b981',
                    fillOpacity: 0.08
                },
                onEachFeature: function(feature, layer) {
                    if (feature.properties && feature.properties.name) {
                        layer.bindTooltip("Kelurahan " + feature.properties.name, {
                            permanent: false,
                            direction: 'center',
                            className: 'bg-white/90 text-stone-800 font-bold px-2 py-0.5 rounded shadow text-xs'
                        });
                    }
                }
            }).addTo(map);

            try {
                const bounds = boundaryLayer.getBounds();
                map.setMaxBounds(bounds.pad(0.1));
                map.fitBounds(bounds, { padding: [20, 20] });
            } catch(e) {}
        }

        const activeLayerGroup = L.layerGroup().addTo(map);
        wastePoints.filter(p => !p.is_resolved).forEach(p => {
            if (!p.latitude || !p.longitude) return;
            const icon = L.divIcon({
                className: 'active-waste-marker',
                html: `<div style="background-color: #dc2626; width: 26px; height: 26px; border-radius: 50%; border: 2.5px solid #ffffff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white;"><svg style="width: 14px; height: 14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></div>`,
                iconSize: [26, 26], iconAnchor: [13, 13]
            });
            const marker = L.marker([p.latitude, p.longitude], { icon: icon });
            const photoHtml = p.primary_photo_url ? `<img src="${p.primary_photo_url}" class="w-full h-28 object-cover mb-2">` : '';
            marker.bindPopup(`<div class="w-56 overflow-hidden">${photoHtml}<div class="p-3"><div class="flex items-center justify-between mb-1"><span class="font-mono text-xs font-bold text-stone-800">${p.report_code}</span><span class="text-2xs px-1.5 py-0.5 rounded bg-amber-50 text-amber-800 border border-amber-200 font-semibold">${p.status_label}</span></div><div class="text-xs text-stone-600 font-medium">Kelurahan ${p.village_name}</div><div class="text-2xs text-emerald-800 font-semibold mt-0.5">${p.category_name}</div><div class="text-2xs text-stone-400 mt-1">${p.created_at_formatted}</div></div></div>`);
            activeLayerGroup.addLayer(marker);
        });

        const resolvedLayerGroup = L.layerGroup().addTo(map);
        wastePoints.filter(p => p.is_resolved).forEach(p => {
            if (!p.latitude || !p.longitude) return;
            const icon = L.divIcon({
                className: 'resolved-waste-marker',
                html: `<div style="background-color: #16a34a; width: 26px; height: 26px; border-radius: 50%; border: 2.5px solid #ffffff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white;"><svg style="width: 14px; height: 14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg></div>`,
                iconSize: [26, 26], iconAnchor: [13, 13]
            });
            const marker = L.marker([p.latitude, p.longitude], { icon: icon });
            const beforeImg = p.before_photos && p.before_photos.length > 0 ? p.before_photos[0].url : (p.primary_photo_url || '');
            const afterImg = p.after_photos && p.after_photos.length > 0 ? p.after_photos[0].url : '';
            marker.bindPopup(`<div class="w-60 p-3"><div class="flex items-center justify-between mb-1.5"><span class="font-mono text-xs font-bold text-stone-800">${p.report_code}</span><span class="text-2xs px-2 py-0.5 rounded bg-emerald-100 text-emerald-900 font-bold">SELESAI</span></div><div class="text-xs text-stone-600 font-medium">Kelurahan ${p.village_name}</div><div class="text-2xs text-stone-400 mt-1">Selesai: ${p.resolved_at_formatted}</div><div class="mt-3 pt-2 border-t border-stone-100"><button type="button" onclick='openBeforeAfterModal("${p.report_code}", "${p.village_name}", "${beforeImg}", "${afterImg}", "${p.created_at_formatted}", "${p.resolved_at_formatted}")' class="w-full py-1.5 rounded-lg bg-emerald-800 hover:bg-emerald-900 text-white font-semibold text-2xs text-center shadow-xs transition">Bandingkan Foto Before & After</button></div></div>`);
            resolvedLayerGroup.addLayer(marker);
        });

        let heatLayer = null;
        if (heatmapPoints && heatmapPoints.length > 0) {
            heatLayer = L.heatLayer(heatmapPoints, {
                radius: 28,
                blur: 18,
                maxZoom: 16,
                gradient: {
                    0.2: '#ffd65a',
                    0.6: '#ff9d23',
                    1.0: '#ea5252'
                }
            }).addTo(map);
        }

        const wasteBankLayerGroup = L.layerGroup().addTo(map);
        wasteBanks.forEach(wb => {
            if (!wb.latitude || !wb.longitude) return;
            const icon = L.divIcon({
                className: 'waste-bank-marker',
                html: `<div style="background-color: #0d9488; width: 26px; height: 26px; border-radius: 50%; border: 2.5px solid #ffffff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white;"><svg style="width: 14px; height: 14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg></div>`,
                iconSize: [26, 26], iconAnchor: [13, 13]
            });
            const marker = L.marker([wb.latitude, wb.longitude], { icon: icon });
            marker.bindPopup(`<div class="p-3 w-56"><span class="text-2xs font-bold uppercase tracking-wider text-teal-700 bg-teal-50 px-2 py-0.5 rounded">Bank Sampah</span><div class="font-bold text-xs text-stone-900 mt-1.5">${wb.name}</div><div class="text-2xs text-stone-600 mt-1">${wb.address}</div></div>`);
            wasteBankLayerGroup.addLayer(marker);
        });

        const landfillLayerGroup = L.layerGroup().addTo(map);
        landfills.forEach(lf => {
            if (!lf.latitude || !lf.longitude) return;
            const icon = L.divIcon({
                className: 'landfill-marker',
                html: `<div style="background-color: #4f46e5; width: 26px; height: 26px; border-radius: 50%; border: 2.5px solid #ffffff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white;"><svg style="width: 14px; height: 14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg></div>`,
                iconSize: [26, 26], iconAnchor: [13, 13]
            });
            const marker = L.marker([lf.latitude, lf.longitude], { icon: icon });
            marker.bindPopup(`<div class="p-3 w-56"><span class="text-2xs font-bold uppercase tracking-wider text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded">TPA / TPS-3R</span><div class="font-bold text-xs text-stone-900 mt-1.5">${lf.name}</div><div class="text-2xs text-stone-600 mt-1">${lf.address}</div></div>`);
            landfillLayerGroup.addLayer(marker);
        });

        document.getElementById('layerActivePoints').addEventListener('change', e => e.target.checked ? map.addLayer(activeLayerGroup) : map.removeLayer(activeLayerGroup));
        document.getElementById('layerResolvedPoints').addEventListener('change', e => e.target.checked ? map.addLayer(resolvedLayerGroup) : map.removeLayer(resolvedLayerGroup));
        document.getElementById('layerHeatmap').addEventListener('change', e => { if(heatLayer) e.target.checked ? map.addLayer(heatLayer) : map.removeLayer(heatLayer); });
        document.getElementById('layerWasteBanks').addEventListener('change', e => e.target.checked ? map.addLayer(wasteBankLayerGroup) : map.removeLayer(wasteBankLayerGroup));
        document.getElementById('layerLandfills').addEventListener('change', e => e.target.checked ? map.addLayer(landfillLayerGroup) : map.removeLayer(landfillLayerGroup));
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLeafletMap);
    } else {
        initLeafletMap();
    }

    function openBeforeAfterModal(reportCode, village, beforeUrl, afterUrl, reportDate, resolveDate) {
        document.getElementById('modalReportCode').innerText = 'Laporan ' + reportCode;
        document.getElementById('modalReportVillage').innerText = 'Kelurahan ' + village;
        document.getElementById('modalBeforeImg').src = beforeUrl || '';
        document.getElementById('modalAfterImg').src = afterUrl || '';
        document.getElementById('modalReportDate').innerText = 'Dilaporkan: ' + reportDate;
        document.getElementById('modalResolvedDate').innerText = 'Selesai: ' + resolveDate;
        document.getElementById('beforeAfterModal').classList.remove('hidden');
    }

    function closeBeforeAfterModal() {
        document.getElementById('beforeAfterModal').classList.add('hidden');
    }
</script>
