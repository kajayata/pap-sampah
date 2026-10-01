@extends('layouts.app')

@section('title', 'Manajemen Berita & Edukasi - Pap Sampah')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-6 space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-amber-600 text-white flex items-center justify-center">
                    <svg class="w-5 h-5"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                </span>

                <h1 class="text-2xl font-bold font-serif text-slate-900">
                    Manajemen Berita & Edukasi
                </h1>
            </div>

            <p class="text-xs text-slate-500 mt-1">
                Kelola konten edukasi lingkungan, informasi pemilahan sampah,
                dan pengumuman resmi untuk aplikasi mobile dan web.
            </p>
        </div>

        {{-- TOMBOL TAMBAH --}}
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isVillageAdmin())
            <a href="{{ route('news.create') }}"
               class="px-4 py-2 bg-emerald-800 hover:bg-emerald-900 text-white rounded-xl text-xs font-semibold shadow-xs flex items-center gap-2 self-start sm:self-auto transition">

                <svg class="w-4 h-4"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M12 4v16m8-8H4" />
                </svg>

                Tulis Artikel Baru
            </a>
        @endif
    </div>

    {{-- INFORMASI HAK AKSES --}}
    @if(auth()->user()->isSuperAdmin())
        <div class="rounded-2xl border border-indigo-200 bg-indigo-50 px-4 py-3">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-indigo-600 shrink-0 mt-0.5"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M13 16h-1v-4h-1m1-8h.01M12 22a10 10 0 100-20 10 10 0 000 20z" />
                </svg>

                <div>
                    <p class="text-xs font-semibold text-indigo-900">
                        Akses Super Admin Kecamatan
                    </p>
                    <p class="text-2xs text-indigo-700 mt-0.5">
                        Anda dapat mengelola berita Published, Draft, dan Archived.
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-emerald-700 shrink-0 mt-0.5"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M13 16h-1v-4h-1m1-8h.01M12 22a10 10 0 100-20 10 10 0 000-20 10 10 0 000 20z" />
                </svg>

                <div>
                    <p class="text-xs font-semibold text-emerald-900">
                        Akses Admin Kelurahan
                    </p>
                    <p class="text-2xs text-emerald-700 mt-0.5">
                        Hanya berita Published yang dapat dikelola.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- FILTER DAN PENCARIAN --}}
    <div class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-2xs">

        <form method="GET"
              action="{{ route('news.index') }}"
              class="w-full">

            <div class="flex flex-col lg:flex-row gap-3 items-stretch lg:items-center">

                {{-- SEARCH --}}
                <div class="flex-1 relative">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           maxlength="100"
                           placeholder="Cari judul atau isi berita..."
                           class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-emerald-700 focus:border-emerald-700 focus:outline-none">

                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                {{-- FILTER STATUS KHUSUS SUPER ADMIN --}}
                @if(auth()->user()->isSuperAdmin())

                    <select name="status"
                            onchange="this.form.submit()"
                            class="w-full lg:w-44 px-3 py-2.5 rounded-xl border border-slate-200 text-xs bg-white text-slate-800 focus:ring-2 focus:ring-emerald-700 focus:border-emerald-700 focus:outline-none">

                        <option value="">
                            Semua Status
                        </option>

                        <option value="PUBLISHED"
                            {{ request('status') === 'PUBLISHED' ? 'selected' : '' }}>
                            Published
                        </option>

                        <option value="DRAFT"
                            {{ request('status') === 'DRAFT' ? 'selected' : '' }}>
                            Draft
                        </option>

                        <option value="ARCHIVED"
                            {{ request('status') === 'ARCHIVED' ? 'selected' : '' }}>
                            Archived
                        </option>

                    </select>

                @else

                    <div class="w-full lg:w-44 px-3 py-2.5 rounded-xl border border-emerald-200 bg-emerald-50 text-xs text-emerald-800 font-semibold text-center">
                        Published
                    </div>

                @endif

                {{-- TOMBOL CARI --}}
                <button type="submit"
                        class="px-4 py-2.5 rounded-xl bg-emerald-800 hover:bg-emerald-900 text-white text-xs font-semibold transition">
                    Cari
                </button>

                {{-- RESET --}}
                @if(request('search') || request('status'))
                    <a href="{{ route('news.index') }}"
                       class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition text-center">
                        Reset
                    </a>
                @endif
            </div>

        </form>
    </div>

    {{-- HASIL PENCARIAN --}}
    @if(request('search') || request('status'))
        <div class="text-xs text-slate-500">
            Menampilkan hasil
            @if(request('search'))
                untuk
                <span class="font-semibold text-slate-800">
                    "{{ request('search') }}"
                </span>
            @endif

            @if(request('status'))
                dengan status
                <span class="font-semibold text-slate-800">
                    {{ request('status') }}
                </span>
            @endif
        </div>
    @endif

    {{-- TABEL BERITA --}}
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">

        <div class="overflow-x-auto">
            <table class="min-w-[1000px] w-full text-left text-xs">

                <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase tracking-wider text-2xs">
                    <tr>
                        <th class="px-5 py-3.5">
                            Judul & Cuplikan
                        </th>

                        <th class="px-5 py-3.5">
                            Penulis
                        </th>

                        <th class="px-5 py-3.5">
                            Status
                        </th>

                        <th class="px-5 py-3.5">
                            Tanggal Publish
                        </th>

                        <th class="px-5 py-3.5 text-right">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">

                    @forelse($news as $article)

                        <tr class="hover:bg-slate-50/70 transition">

                            {{-- JUDUL --}}
                            <td class="px-5 py-4">

                                <div class="flex items-center gap-3">

                                    {{-- THUMBNAIL --}}
                                    @if($article->thumbnail_url)

                                        <img src="{{ $article->thumbnail_url }}"
                                             alt="Thumbnail {{ $article->title }}"
                                             class="w-14 h-14 rounded-xl object-cover border border-slate-200 shrink-0">

                                    @else

                                        <div class="w-14 h-14 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 shrink-0">

                                            <svg class="w-6 h-6"
                                                 fill="none"
                                                 viewBox="0 0 24 24"
                                                 stroke="currentColor">
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>

                                        </div>

                                    @endif

                                    <div class="min-w-0">

                                        <div class="font-bold text-slate-900 text-sm line-clamp-2">
                                            {{ $article->title }}
                                        </div>

                                        <div class="text-2xs text-slate-500 font-mono mt-1 truncate">
                                            /news/{{ $article->slug }}
                                        </div>

                                    </div>

                                </div>

                            </td>

                            {{-- PENULIS --}}
                            <td class="px-5 py-4 text-slate-700">

                                <div class="font-medium text-slate-900">
                                    {{ $article->author?->name ?? 'Admin' }}
                                </div>

                                @if($article->author?->isSuperAdmin())

                                    <span class="inline-flex whitespace-nowrap mt-1 px-2 py-0.5 rounded-md text-2xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        Kecamatan Sumbersari
                                    </span>

                                @elseif($article->author?->village)

                                    <span class="inline-flex whitespace-nowrap mt-1 px-2 py-0.5 rounded-md text-2xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        Kelurahan {{ $article->author->village->name }}
                                    </span>

                                @endif

                            </td>

                            {{-- STATUS --}}
                            <td class="px-5 py-4">

                                @php
                                    $statusConfig = match($article->status) {
                                        'PUBLISHED' => [
                                            'label' => 'PUBLISHED',
                                            'class' => 'bg-emerald-50 text-emerald-800 border-emerald-300',
                                        ],

                                        'DRAFT' => [
                                            'label' => 'DRAFT',
                                            'class' => 'bg-amber-50 text-amber-800 border-amber-300',
                                        ],

                                        'ARCHIVED' => [
                                            'label' => 'ARCHIVED',
                                            'class' => 'bg-slate-100 text-slate-700 border-slate-300',
                                        ],

                                        default => [
                                            'label' => $article->status,
                                            'class' => 'bg-slate-100 text-slate-700 border-slate-300',
                                        ],
                                    };
                                @endphp

                                <span class="inline-flex px-2.5 py-1 rounded-full text-2xs font-semibold border {{ $statusConfig['class'] }}">
                                    {{ $statusConfig['label'] }}
                                </span>

                                {{-- STATUS FUTURE --}}
                                @if(
                                    $article->status === 'PUBLISHED'
                                    && $article->published_at
                                    && $article->published_at->isFuture()
                                )

                                    <div class="mt-1 text-2xs text-amber-600">
                                        Belum tayang
                                    </div>

                                @elseif($article->status === 'PUBLISHED')

                                    <div class="mt-1 text-2xs text-emerald-600">
                                        Sudah tayang
                                    </div>

                                @endif

                            </td>

                            {{-- TANGGAL PUBLISH --}}
                            <td class="px-5 py-4 text-slate-500 text-2xs">

                                @if($article->published_at)

                                    <div>
                                        {{ $article->published_at->translatedFormat('d M Y') }}
                                    </div>

                                    <div class="text-slate-400 mt-0.5">
                                        {{ $article->published_at->format('H:i') }} WIB
                                    </div>

                                @else

                                    <span class="text-slate-400">
                                        -
                                    </span>

                                @endif

                            </td>

                            {{-- AKSI --}}
                            <td class="px-5 py-4 text-right">

                                @php
                                    $currentUser = auth()->user();

                                    $canManage =
                                        $currentUser->isSuperAdmin()
                                        || (
                                            $article->status === 'PUBLISHED'
                                            && $currentUser->canManageNews($article)
                                        );
                                @endphp

                                @if($canManage)

                                    <div class="flex items-center justify-end gap-2">

                                        {{-- EDIT --}}
                                        <a href="{{ route('news.edit', $article->id) }}"
                                           class="px-3 py-1.5 rounded-lg text-2xs font-semibold text-emerald-800 hover:bg-emerald-50 transition border border-emerald-200">
                                            Edit
                                        </a>

                                        {{-- HAPUS --}}
                                        <form method="POST"
                                              action="{{ route('news.destroy', $article->id) }}"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel ini?')">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="px-2.5 py-1.5 rounded-lg text-2xs font-semibold text-rose-700 hover:bg-rose-50 transition border border-rose-200">
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

                            <td colspan="5"
                                class="px-5 py-14 text-center">

                                <div class="flex flex-col items-center justify-center">

                                    <div class="w-14 h-14 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 mb-3">

                                        <svg class="w-7 h-7"
                                             fill="none"
                                             viewBox="0 0 24 24"
                                             stroke="currentColor">
                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                        </svg>

                                    </div>

                                    <p class="text-sm font-semibold text-slate-700">
                                        Belum ada artikel berita
                                    </p>

                                    <p class="text-xs text-slate-400 mt-1">
                                        Belum ditemukan artikel yang sesuai dengan pencarian.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>
        </div>

        {{-- PAGINATION --}}
        @if($news->hasPages())

            <div class="p-4 border-t border-slate-100">
                {{ $news->links() }}
            </div>

        @endif

    </div>

</div>
@endsection
