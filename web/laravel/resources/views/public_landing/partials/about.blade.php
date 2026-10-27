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
                        <strong class="text-stone-900 font-semibold">PapSampah</strong> membantu warga melaporkan sampah liar melalui aplikasi mobile, lalu menghubungkan laporan dengan proses validasi dan penanganan oleh Admin Desa serta petugas di {{ $district->name }}.
                    </p>
                    <p>
                        Website ini menyajikan peta, status, dan ringkasan laporan untuk publik. Setelah laporan dinyatakan valid, petugas melakukan pembersihan dan Admin Desa memverifikasi hasilnya.
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
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center mb-3 p-2">
                            <svg class="w-4 h-4 text-[#263e1c]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                        </div>
                        <div class="text-3xl sm:text-4xl font-black text-stone-900 font-fraunces" style="font-family: 'Fraunces', Georgia, serif;">
                            {{ $stats['kelurahan_dipantau'] ?? 0 }}
                        </div>
                        <div class="text-xs font-semibold text-stone-500 uppercase tracking-wider mt-1">
                            Kelurahan Dipantau
                        </div>
                    </div>

                    <!-- Card 2: 1.247 Total Laporan -->
                    <div class="p-6 rounded-2xl bg-white border border-stone-200/90 shadow-sm hover:shadow-md transition-all">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center mb-3 p-2">
                            <svg class="w-5 h-5 text-[#b83700]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                        </div>
                        <div class="text-3xl sm:text-4xl font-black text-amber-700 font-fraunces" style="font-family: 'Fraunces', Georgia, serif;">
                            {{ $stats['total_laporan'] ?? '1.247' }}
                        </div>
                        <div class="text-xs font-semibold text-stone-500 uppercase tracking-wider mt-1">
                            Total Laporan Masuk
                        </div>
                    </div>

                    <!-- Card 3: 1.107 Laporan Ditangani -->
                    <div class="p-6 rounded-2xl bg-white border border-stone-200/90 shadow-sm hover:shadow-md transition-all">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center mb-3 p-2">
                            <svg class="w-5 h-5 text-[#043e04]" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="text-3xl sm:text-4xl font-black text-emerald-700 font-fraunces" style="font-family: 'Fraunces', Georgia, serif;">
                            {{ $stats['laporan_aktif'] ?? 0 }}
                        </div>
                        <div class="text-xs font-semibold text-stone-500 uppercase tracking-wider mt-1">
                            Laporan Aktif
                        </div>
                    </div>

                    <!-- Card 4: 2-4j Respons -->
                    <div class="p-6 rounded-2xl bg-white border border-stone-200/90 shadow-sm hover:shadow-md transition-all">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center mb-3 p-2">
                            <svg class="w-5 h-5 text-[#970000]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
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
