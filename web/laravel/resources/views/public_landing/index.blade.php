@extends('layouts.public_landing')

@section('title', 'PapSampah — Platform Pemantauan Sampah Liar Terpadu Kabupaten Jember')

@section('content')

    {{-- 1. Hero --}}
    @include('public_landing.partials.hero', [
        'stats' => $stats,
        'district' => $district
    ])

    {{-- 2. About Platform --}}
    @include('public_landing.partials.about', [
        'stats' => $stats
    ])

    {{-- 3. Peta Heatmap --}}
    @include('public_landing.partials.heatmap', compact(
        'district',
        'heatmapData',
        'wastePoints',
        'boundariesGeoJson',
        'villages',
        'wasteBanks',
        'landfills',
        'markerDisplayDays'
    ))

    {{-- 4. Data Kecamatan --}}
    @include('public_landing.partials.kecamatan', [
        'villageCards' => $villageCards,
        'district' => $district
    ])

    {{-- 5. Berita & Edukasi --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6">

            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-10">

                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 text-xs font-semibold uppercase tracking-wider">
                        Berita & Edukasi
                    </span>

                    <h2 class="mt-3 font-display text-3xl md:text-4xl font-bold text-slate-900">
                        Informasi Terbaru
                    </h2>

                    <p class="mt-2 text-sm text-slate-500 max-w-2xl">
                        Informasi terbaru mengenai kebersihan lingkungan,
                        pengelolaan sampah, edukasi masyarakat, dan kegiatan
                        kebersihan di Kecamatan Sumbersari.
                    </p>
                </div>

                <a href="{{ route('news.index') }}"
                   class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-800 hover:text-emerald-900 transition">
                    Lihat Semua Berita

                    <svg class="w-4 h-4"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M9 5l7 7-7 7" />
                    </svg>
                </a>

            </div>

            {{-- Berita --}}
            @if($news->isNotEmpty())

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    @foreach($news as $article)

                        <article class="group bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">

                            {{-- Thumbnail --}}
                            <div class="relative aspect-[16/9] bg-slate-100 overflow-hidden">

                                @if($article->thumbnail_url)

                                    <img src="{{ $article->thumbnail_url }}"
                                         alt="{{ $article->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

                                @else

                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-emerald-50 to-emerald-100">

                                        <svg class="w-12 h-12 text-emerald-700/40"
                                             fill="none"
                                             viewBox="0 0 24 24"
                                             stroke="currentColor"
                                             stroke-width="1.5">
                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                        </svg>

                                    </div>

                                @endif

                                {{-- Badge Published --}}
                                <div class="absolute top-4 left-4">
                                    <span class="inline-flex px-2.5 py-1 rounded-full bg-emerald-700 text-white text-2xs font-semibold shadow-sm">
                                        Published
                                    </span>
                                </div>

                            </div>

                            {{-- Content --}}
                            <div class="p-5">

                                {{-- Tanggal --}}
                                <div class="flex items-center gap-2 text-2xs text-slate-500 mb-2">

                                    <svg class="w-3.5 h-3.5"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke="currentColor">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>

                                    @if($article->published_at)
                                        {{ $article->published_at->timezone('Asia/Jakarta')->translatedFormat('d M Y') }}
                                    @endif

                                </div>

                                {{-- Judul --}}
                                <h3 class="font-display text-lg font-bold text-slate-900 line-clamp-2 group-hover:text-emerald-800 transition-colors">
                                    {{ $article->title }}
                                </h3>

                                {{-- Cuplikan --}}
                                <p class="mt-2 text-sm text-slate-500 leading-relaxed line-clamp-3">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($article->content), 150) }}
                                </p>

                                {{-- Footer Card --}}
                                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between gap-3">

                                    <div class="text-2xs text-slate-400">
                                        @if($article->author?->isSuperAdmin())
                                            Kecamatan Sumbersari
                                        @elseif($article->author?->village)
                                            Kelurahan {{ $article->author->village->name }}
                                        @else
                                            PapSampah
                                        @endif
                                    </div>

                                    <a href="{{ route('news.index', ['search' => $article->title]) }}"
                                       class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-800 hover:text-emerald-900">
                                        Baca

                                        <svg class="w-3.5 h-3.5"
                                             fill="none"
                                             viewBox="0 0 24 24"
                                             stroke="currentColor">
                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>

                                </div>

                            </div>

                        </article>

                    @endforeach

                </div>

            @else

                {{-- Empty State --}}
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-12 text-center">

                    <div class="w-16 h-16 mx-auto rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center">

                        <svg class="w-8 h-8"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="1.5">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>

                    </div>

                    <h3 class="mt-4 text-lg font-bold text-slate-800">
                        Belum Ada Berita
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Berita dan edukasi terbaru akan ditampilkan di sini.
                    </p>

                </div>

            @endif

        </div>
    </section>

    {{-- 6. Panduan --}}
    @include('public_landing.partials.panduan', [
        'guideSteps' => $guideSteps
    ])

    {{-- 7. Download App --}}
    @include('public_landing.partials.download')

@endsection
