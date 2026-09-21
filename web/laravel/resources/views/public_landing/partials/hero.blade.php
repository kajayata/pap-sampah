<section class="hero-bg relative bg-gradient-to-b from-[#263e1c] via-[#2d4b21] to-[#203617] text-white pt-12 pb-24 md:pt-16 md:pb-32 overflow-hidden" style="background: linear-gradient(180deg, #263e1c 0%, #2d4b21 50%, #203617 100%);">
    <!-- Decorative Bokeh Circles -->
    <div class="absolute top-10 left-10 w-72 h-72 rounded-full bg-white/5 blur-2xl pointer-events-none"></div>
    <div class="absolute top-1/4 right-20 w-96 h-96 rounded-full bg-[#ffd65a]/10 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-10 left-1/3 w-80 h-80 rounded-full bg-emerald-500/10 blur-2xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <!-- Left Column: Hero Texts -->
            <div class="lg:col-span-8 space-y-6">
                <!-- Status Badge -->
                <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-white/10 border border-white/20 backdrop-blur-md">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-400"></span>
                    </span>
                    <span class="text-xs font-semibold text-emerald-100 tracking-wide">{{ $district->name }} — Aktif Dipantau</span>
                </div>

                <!-- Main Heading (Fraunces Serif) -->
                <h1 class="text-4xl sm:text-5xl md:text-6xl font-black tracking-tight leading-[1.1] font-fraunces" style="font-family: 'Fraunces', Georgia, serif;">
                    Bersama Jaga <br class="hidden sm:block">
                    <span class="text-[#ffd65a]">Kebersihan</span> Jember
                </h1>

                <!-- Subtitle -->
                <p class="text-base sm:text-lg text-emerald-100/90 max-w-xl font-normal leading-relaxed">
                    Platform pemantauan dan pelaporan pembuangan sampah liar di {{ $district->name }}. Laporkan, pantau, dan bersihkan bersama.
                </p>

                <!-- Action Buttons -->
                <div class="flex flex-wrap items-center gap-4 pt-2">
                    <a href="#peta" class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-white/15 hover:bg-white/25 border border-white/30 text-white font-semibold text-sm backdrop-blur-md hover:-translate-y-0.5 transition-all">
                        <svg class="w-4 h-4 text-[#ffd65a]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        <span>Lihat Peta Heatmap</span>
                    </a>
                    <a href="#unduh" class="inline-flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-xl bg-[#ffd65a] hover:bg-[#f1c640] text-stone-950 font-bold text-sm shadow-md hover:-translate-y-0.5 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/>
                            <line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                        <span>Unduh Aplikasi</span>
                    </a>
                </div>
            </div>

            <!-- Right Column: Vertical Glass Stats Cards Stack -->
            <div class="lg:col-span-4 flex flex-col gap-3.5 max-w-xs lg:ml-auto w-full">
                <!-- Card 1: Total Laporan -->
                <div class="p-4 rounded-2xl bg-white/10 border border-white/15 backdrop-blur-md shadow-lg flex items-center justify-between">
                    <div>
                        <span class="block text-2xl sm:text-3xl font-black text-white font-fraunces leading-tight" style="font-family: 'Fraunces', Georgia, serif;">
                            {{ $stats['total_laporan'] ?? '1.247' }}
                        </span>
                        <span class="text-xs text-emerald-200/80 font-medium">Total Laporan</span>
                    </div>
                    <span class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-lg">
                        📋
                    </span>
                </div>

                <!-- Card 2: Terselesaikan -->
                <div class="p-4 rounded-2xl bg-white/10 border border-white/15 backdrop-blur-md shadow-lg flex items-center justify-between">
                    <div>
                        <span class="block text-2xl sm:text-3xl font-black text-[#ffd65a] font-fraunces leading-tight" style="font-family: 'Fraunces', Georgia, serif;">
                            {{ $stats['persentase_selesai'] ?? '89%' }}
                        </span>
                        <span class="text-xs text-emerald-200/80 font-medium">Terselesaikan</span>
                    </div>
                    <span class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-lg">
                        ✅
                    </span>
                </div>

                <!-- Card 3: Kecamatan Dipantau -->
                <div class="p-4 rounded-2xl bg-white/10 border border-white/15 backdrop-blur-md shadow-lg flex items-center justify-between">
                    <div>
                        <span class="block text-2xl sm:text-3xl font-black text-white font-fraunces leading-tight" style="font-family: 'Fraunces', Georgia, serif;">
                            {{ $stats['kelurahan_dipantau'] ?? 0 }}
                        </span>
                        <span class="text-xs text-emerald-200/80 font-medium">Kelurahan Dipantau</span>
                    </div>
                    <span class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-lg">
                        🗺️
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Soft bottom curve into cream background -->
    <div class="absolute bottom-0 inset-x-0 h-8 bg-[#fafaf5] rounded-t-[32px]"></div>
</section>
