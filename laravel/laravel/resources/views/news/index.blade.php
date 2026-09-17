@extends('layouts.app')

@section('title', 'Manajemen Berita & Edukasi - Pap Sampah')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-6 space-y-6">
    <!-- Header & Action Button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-amber-600 text-white flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                </span>
                <h1 class="text-2xl font-bold font-serif text-slate-900">Manajemen Berita & Edukasi</h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Kelola konten edukasi lingkungan, info pemilahan sampah, dan pengumuman untuk aplikasi mobile dan web.
            </p>
        </div>

        <a href="{{ route('news.create') }}"
           class="px-4 py-2 bg-emerald-800 hover:bg-emerald-900 text-white rounded-xl text-xs font-semibold shadow-xs flex items-center gap-2 self-start sm:self-auto transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tulis Artikel Baru
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-2xs flex flex-col md:flex-row gap-3 items-center justify-between">
        <form method="GET" action="{{ route('news.index') }}" class="w-full flex flex-col sm:flex-row gap-3 items-center">
            <div class="flex-1 relative w-full">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari judul atau isi berita (tekan Enter)..."
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-700 focus:outline-none">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <select name="status"
                    onchange="this.form.submit()"
                    class="w-full sm:w-auto px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white text-slate-800 focus:ring-2 focus:ring-emerald-700">
                <option value="">Semua Status</option>
                <option value="PUBLISHED" {{ request('status') === 'PUBLISHED' ? 'selected' : '' }}>Published</option>
                <option value="DRAFT" {{ request('status') === 'DRAFT' ? 'selected' : '' }}>Draft</option>
                <option value="ARCHIVED" {{ request('status') === 'ARCHIVED' ? 'selected' : '' }}>Archived</option>
            </select>

            @if(request('search') || request('status'))
                <a href="{{ route('news.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-800 self-center whitespace-nowrap">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Table of Articles -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase tracking-wider text-2xs">
                    <tr>
                        <th class="px-5 py-3.5">Judul & Cuplikan</th>
                        <th class="px-5 py-3.5">Penulis</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5">Tanggal Publish</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($news as $article)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    @if($article->thumbnail_url)
                                        <img src="{{ $article->thumbnail_url }}" alt="Thumbnail" class="w-12 h-12 rounded-lg object-cover border border-slate-200 shrink-0">
                                    @else
                                        <div class="w-12 h-12 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 shrink-0">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-bold text-slate-900 text-sm hover:text-emerald-800 line-clamp-1">{{ $article->title }}</div>
                                        <div class="text-2xs text-slate-500 font-mono mt-0.5">/news/{{ $article->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-slate-700">
                                <div class="font-medium text-slate-900">{{ $article->author?->name ?? 'Admin' }}</div>
                                @if($article->author?->isSuperAdmin())
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded-md text-2xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        Kecamatan Sumbersari
                                    </span>
                                @elseif($article->author?->village)
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded-md text-2xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        Kelurahan {{ $article->author->village->name }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @php
                                    $badge = match($article->status) {
                                        'PUBLISHED' => 'bg-emerald-50 text-emerald-800 border-emerald-300',
                                        'DRAFT' => 'bg-amber-50 text-amber-800 border-amber-300',
                                        default => 'bg-slate-100 text-slate-700 border-slate-300',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-2xs font-semibold border {{ $badge }}">
                                    {{ $article->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-500 text-2xs">
                                {{ $article->published_at ? $article->published_at->translatedFormat('d M Y H:i') : '-' }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                @php
                                    $canManage = auth()->user()->canManageNews($article);
                                @endphp
                                @if($canManage)
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('news.edit', $article->id) }}"
                                           class="px-3 py-1.5 rounded-lg text-2xs font-semibold text-emerald-800 hover:bg-emerald-50 transition border border-emerald-200">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('news.destroy', $article->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg text-2xs font-semibold text-rose-700 hover:bg-rose-50 transition border border-rose-200">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-2xs font-medium text-slate-400 bg-slate-50 border border-slate-200">
                                        Hanya Baca
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-slate-400">
                                Belum ada artikel berita yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($news->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $news->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
