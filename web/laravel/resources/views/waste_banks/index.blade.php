@extends('layouts.app')

@section('title', 'Manajemen Bank Sampah - Pap Sampah')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-6 space-y-6">
    <!-- Header & Action Button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-teal-700 text-white flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </span>
                <h1 class="text-2xl font-bold font-serif text-slate-900">Manajemen Bank Sampah</h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Kelola titik lokasi fasilitas Bank Sampah di wilayah Kecamatan Sumbersari untuk informasi masyarakat.
            </p>
        </div>

        <a href="{{ route('waste-banks.create') }}"
           class="px-4 py-2 bg-emerald-800 hover:bg-emerald-900 text-white rounded-xl text-xs font-semibold shadow-xs flex items-center gap-2 self-start sm:self-auto transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Bank Sampah
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-2xs flex flex-col md:flex-row gap-3 items-center justify-between">
        <form method="GET" action="{{ route('waste-banks.index') }}" class="w-full flex flex-col sm:flex-row gap-3 items-center">
            <div class="flex-1 relative w-full">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari nama atau alamat bank sampah (tekan Enter)..."
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-700 focus:outline-none">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            @if($isSuperAdmin)
                <select name="village_id"
                        onchange="this.form.submit()"
                        class="w-full sm:w-auto px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white text-slate-800 focus:ring-2 focus:ring-emerald-700">
                    <option value="">Semua Kelurahan</option>
                    @foreach($villages as $vil)
                        <option value="{{ $vil->id }}" {{ request('village_id') == $vil->id ? 'selected' : '' }}>
                            Kelurahan {{ $vil->name }}
                        </option>
                    @endforeach
                </select>
            @endif

            @if(request('search') || request('village_id'))
                <a href="{{ route('waste-banks.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-800 self-center whitespace-nowrap">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Table of Waste Banks -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase tracking-wider text-2xs">
                    <tr>
                        <th class="px-5 py-3.5">Nama & Deskripsi</th>
                        <th class="px-5 py-3.5">Kelurahan</th>
                        <th class="px-5 py-3.5">Alamat & Kontak</th>
                        <th class="px-5 py-3.5">Koordinat (Lat, Lng)</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($wasteBanks as $wb)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-5 py-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $wb->name }}</div>
                                <div class="text-2xs text-slate-500 line-clamp-1 mt-0.5">{{ $wb->description ?: 'Tidak ada deskripsi.' }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-md text-2xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    Kelurahan {{ $wb->village?->name }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-slate-700">{{ $wb->address }}</div>
                                <div class="text-2xs text-slate-500 font-mono mt-0.5">{{ $wb->phone ?: 'No Phone' }}</div>
                            </td>
                            <td class="px-5 py-4 font-mono text-2xs text-slate-600">
                                {{ number_format($wb->latitude, 4) }}, {{ number_format($wb->longitude, 4) }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                <form method="POST" action="{{ route('waste-banks.toggle-status', $wb->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="inline-flex items-center px-2.5 py-1 rounded-full text-2xs font-semibold cursor-pointer {{ $wb->is_active ? 'bg-emerald-50 text-emerald-800 border border-emerald-300' : 'bg-rose-50 text-rose-800 border border-rose-300' }}">
                                        {{ $wb->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('waste-banks.edit', $wb->id) }}"
                                   class="px-3 py-1.5 rounded-lg text-2xs font-semibold text-emerald-800 hover:bg-emerald-50 transition border border-emerald-200">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                                Belum ada data Bank Sampah yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($wasteBanks->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $wasteBanks->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
