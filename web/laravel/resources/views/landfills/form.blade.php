@extends('layouts.app')

@section('title', ($landfill->exists ? 'Edit' : 'Tambah') . ' Fasilitas TPA / TPS-3R - Pap Sampah')

@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

@section('content')
<div class="max-w-4xl mx-auto px-6 py-6 space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('landfills.index') }}"
           class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-slate-900 transition">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold font-serif text-slate-900">
                {{ $landfill->exists ? 'Edit Fasilitas TPA' : 'Tambah Fasilitas TPA Baru' }}
            </h1>
            <p class="text-xs text-slate-500">
                Tentukan koordinat dan informasi fasilitas Tempat Pemrosesan Akhir / TPS-3R.
            </p>
        </div>
    </div>

    <form method="POST"
          action="{{ $landfill->exists ? route('landfills.update', $landfill->id) : route('landfills.store') }}"
          class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-6">
        @csrf
        @if($landfill->exists)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-xs">
            <!-- Nama Fasilitas -->
            <div class="md:col-span-2">
                <label for="name" class="block font-semibold text-slate-700 mb-1">
                    Nama Fasilitas TPA / TPS-3R <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       name="name"
                       id="name"
                       value="{{ old('name', $landfill->name) }}"
                       required
                       placeholder="Contoh: TPA Pakusari (Rujukan Sumbersari)"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-700 focus:outline-none">
                @error('name') <span class="text-rose-600 text-2xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Kelurahan Rujukan -->
            <div>
                <label for="village_id" class="block font-semibold text-slate-700 mb-1">
                    Wilayah Kelurahan Terdekat / Rujukan <span class="text-rose-500">*</span>
                </label>
                <select name="village_id" id="village_id" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs bg-white text-slate-800 focus:ring-2 focus:ring-emerald-700 focus:outline-none">
                    @foreach($villages as $vil)
                        <option value="{{ $vil->id }}" {{ old('village_id', $landfill->village_id) == $vil->id ? 'selected' : '' }}>
                            Kelurahan {{ $vil->name }}
                        </option>
                    @endforeach
                </select>
                @error('village_id') <span class="text-rose-600 text-2xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Alamat Lengkap -->
            <div>
                <label for="address" class="block font-semibold text-slate-700 mb-1">
                    Alamat Fasilitas <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       name="address"
                       id="address"
                       value="{{ old('address', $landfill->address) }}"
                       required
                       placeholder="Contoh: Jl. Perbatasan Pakusari - Sumbersari"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-700 focus:outline-none">
                @error('address') <span class="text-rose-600 text-2xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Deskripsi -->
            <div class="md:col-span-2">
                <label for="description" class="block font-semibold text-slate-700 mb-1">
                    Deskripsi / Informasi Layanan
                </label>
                <textarea name="description"
                          id="description"
                          rows="3"
                          placeholder="Informasi kapasitas, jam operasional, atau tipe pengolahan sampah..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-700 focus:outline-none">{{ old('description', $landfill->description) }}</textarea>
                @error('description') <span class="text-rose-600 text-2xs mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Interactive Coordinate Picker Map -->
        <div class="space-y-2 pt-2 border-t border-slate-100">
            <label class="block font-semibold text-xs text-slate-700">
                Pilih Titik Lokasi pada Peta <span class="text-rose-500">*</span>
            </label>
            <p class="text-2xs text-slate-500">
                Klik pada peta di bawah ini untuk menentukan titik koordinat fasilitas TPA / TPS-3R.
            </p>

            <div id="pickerMap" class="w-full h-72 rounded-2xl overflow-hidden border border-slate-200"></div>

            <div class="grid grid-cols-2 gap-3 pt-2 text-xs">
                <div>
                    <label for="latitude" class="block text-2xs font-semibold text-slate-600 mb-0.5">Latitude</label>
                    <input type="text"
                           name="latitude"
                           id="latitude"
                           readonly
                           value="{{ old('latitude', $landfill->latitude ?? -8.1880) }}"
                           class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 font-mono text-xs text-slate-700">
                    @error('latitude') <span class="text-rose-600 text-2xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="longitude" class="block text-2xs font-semibold text-slate-600 mb-0.5">Longitude</label>
                    <input type="text"
                           name="longitude"
                           id="longitude"
                           readonly
                           value="{{ old('longitude', $landfill->longitude ?? 113.7650) }}"
                           class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 font-mono text-xs text-slate-700">
                    @error('longitude') <span class="text-rose-600 text-2xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('landfills.index') }}"
               class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                Batal
            </a>
            <button type="submit"
                    class="px-5 py-2.5 rounded-xl text-xs font-semibold bg-emerald-800 hover:bg-emerald-900 text-white transition shadow-xs">
                {{ $landfill->exists ? 'Perbarui Fasilitas TPA' : 'Simpan Fasilitas TPA' }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const initialLat = parseFloat(document.getElementById('latitude').value) || -8.1880;
        const initialLng = parseFloat(document.getElementById('longitude').value) || 113.7650;

        const map = L.map('pickerMap').setView([initialLat, initialLng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        let marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);

        function updateInputs(lat, lng) {
            document.getElementById('latitude').value = lat.toFixed(6);
            document.getElementById('longitude').value = lng.toFixed(6);
        }

        marker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            updateInputs(pos.lat, pos.lng);
        });

        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateInputs(e.latlng.lat, e.latlng.lng);
        });
    });
</script>
@endpush
