@extends('layouts.app')

@section('title', 'Manajemen TPA & TPS-3R - Pap Sampah')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-6 space-y-6">
    <!-- Header & Action Button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-indigo-700 text-white flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </span>
                <h1 class="text-2xl font-bold font-serif text-slate-900">Manajemen TPA & TPS-3R</h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Kelola titik Tempat Pembuangan Akhir dan Fasilitas TPS-3R rujukan di wilayah Kecamatan Sumbersari.
            </p>
        </div>

        <a href="{{ route('landfills.create') }}"
           class="px-4 py-2 bg-emerald-800 hover:bg-emerald-900 text-white rounded-xl text-xs font-semibold shadow-xs flex items-center gap-2 self-start sm:self-auto transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Fasilitas TPA
        </a>
    </div>

    <!-- Table of Landfills -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase tracking-wider text-2xs">
                    <tr>
                        <th class="px-5 py-3.5">Nama Fasilitas</th>
                        <th class="px-5 py-3.5">Wilayah Rujukan</th>
                        <th class="px-5 py-3.5">Alamat</th>
                        <th class="px-5 py-3.5">Koordinat</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($landfills as $lf)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-5 py-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $lf->name }}</div>
                                <div class="text-2xs text-slate-500 line-clamp-1 mt-0.5">{{ $lf->description ?: 'Tidak ada deskripsi tambahan.' }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-md text-2xs font-semibold bg-indigo-50 text-indigo-800 border border-indigo-200">
                                    Kelurahan {{ $lf->village?->name }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-700">
                                {{ $lf->address }}
                            </td>
                            <td class="px-5 py-4 font-mono text-2xs text-slate-600">
                                {{ number_format($lf->latitude, 4) }}, {{ number_format($lf->longitude, 4) }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                <form method="POST" action="{{ route('landfills.toggle-status', $lf->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="inline-flex items-center px-2.5 py-1 rounded-full text-2xs font-semibold cursor-pointer {{ $lf->is_active ? 'bg-emerald-50 text-emerald-800 border border-emerald-300' : 'bg-rose-50 text-rose-800 border border-rose-300' }}">
                                        {{ $lf->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('landfills.edit', $lf->id) }}"
                                   class="px-3 py-1.5 rounded-lg text-2xs font-semibold text-emerald-800 hover:bg-emerald-50 transition border border-emerald-200">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                                Belum ada fasilitas TPA / TPS-3R yang didaftarkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
