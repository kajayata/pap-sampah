<section id="tentang" class="py-20 bg-[#fafaf5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <!-- Left Info Content -->
            <div class="lg:col-span-7 space-y-6">
                <div>
                    <span class="inline-block text-xs font-bold uppercase tracking-wider text-[#3c6426] bg-[#eef5e9] border border-[#3c6426]/20 px-3.5 py-1.5 rounded-full">
                        Tentang Platform
                    </span>
                    <h2 class="text-3xl sm:text-4xl md:text-5xl font-black text-stone-900 tracking-tight mt-4 font-fraunces leading-tight" style="font-family: 'Fraunces', Georgia, serif;">
                        Sistem Pemantauan <br class="hidden sm:block">
                        <span class="text-[#3c6426]">Sampah Liar</span> Terintegrasi
                    </h2>
                </div>

                <div class="space-y-4 text-stone-600 text-base sm:text-lg leading-relaxed max-w-xl">
                    <p>
                        <strong class="text-stone-900 font-semibold">SampahJember</strong> adalah platform publik untuk memantau, melaporkan, dan menindaklanjuti titik-titik pembuangan sampah liar di wilayah kecamatan ini.
                    </p>
                    <p>
                        Warga dapat melihat persebaran sampah dan mengunduh aplikasi mobile untuk melaporkan langsung dari genggaman tangan.
                    </p>
                </div>

                <!-- Feature Pills with verified badge-check icon -->
                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white border border-stone-200 shadow-2xs text-xs font-semibold text-stone-700">
                        <img src="{{ asset('sampah-jember/icons/badge-check.svg') }}" class="w-4 h-4" alt="Verified">
                        Data Real-Time
                    </div>
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white border border-stone-200 shadow-2xs text-xs font-semibold text-stone-700">
                        <img src="{{ asset('sampah-jember/icons/badge-check.svg') }}" class="w-4 h-4" alt="Verified">
                        Akses Publik Gratis
                    </div>
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white border border-stone-200 shadow-2xs text-xs font-semibold text-stone-700">
                        <img src="{{ asset('sampah-jember/icons/badge-check.svg') }}" class="w-4 h-4" alt="Verified">
                        Transparan & Akuntabel
                    </div>
                </div>
            </div>

            <!-- Right 2x2 Metrics Grid -->
            <div class="lg:col-span-5">
                <div class="grid grid-cols-2 gap-4">
                    <!-- Card 1: Kelurahan dalam scope kecamatan -->
                    <div class="p-6 rounded-2xl bg-white border border-stone-200/90 shadow-sm hover:shadow-md transition-all">
                        <div class="text-2xl mb-3">🗺️</div>
                        <div class="text-3xl sm:text-4xl font-black text-stone-900 font-fraunces" style="font-family: 'Fraunces', Georgia, serif;">
                            {{ $stats['kelurahan_dipantau'] ?? 0 }}
                        </div>
                        <div class="text-xs font-semibold text-stone-500 uppercase tracking-wider mt-1">
                            Kelurahan Dipantau
                        </div>
                    </div>

                    <!-- Card 2: 1.247 Total Laporan -->
                    <div class="p-6 rounded-2xl bg-white border border-stone-200/90 shadow-sm hover:shadow-md transition-all">
                        <div class="text-2xl mb-3">📋</div>
                        <div class="text-3xl sm:text-4xl font-black text-amber-700 font-fraunces" style="font-family: 'Fraunces', Georgia, serif;">
                            {{ $stats['total_laporan'] ?? '1.247' }}
                        </div>
                        <div class="text-xs font-semibold text-stone-500 uppercase tracking-wider mt-1">
                            Total Laporan Masuk
                        </div>
                    </div>

                    <!-- Card 3: 1.107 Laporan Ditangani -->
                    <div class="p-6 rounded-2xl bg-white border border-stone-200/90 shadow-sm hover:shadow-md transition-all">
                        <div class="text-2xl mb-3">✅</div>
                        <div class="text-3xl sm:text-4xl font-black text-emerald-700 font-fraunces" style="font-family: 'Fraunces', Georgia, serif;">
                            {{ $stats['laporan_aktif'] ?? 0 }}
                        </div>
                        <div class="text-xs font-semibold text-stone-500 uppercase tracking-wider mt-1">
                            Laporan Aktif
                        </div>
                    </div>

                    <!-- Card 4: 2-4j Respons -->
                    <div class="p-6 rounded-2xl bg-white border border-stone-200/90 shadow-sm hover:shadow-md transition-all">
                        <div class="text-2xl mb-3">⚡</div>
                        <div class="text-3xl sm:text-4xl font-black text-rose-600 font-fraunces" style="font-family: 'Fraunces', Georgia, serif;">
                            {{ $stats['persentase_selesai'] ?? '0%' }}
                        </div>
                        <div class="text-xs font-semibold text-stone-500 uppercase tracking-wider mt-1">
                            Laporan Selesai
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
