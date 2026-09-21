<section id="unduh" class="download-bg py-20 bg-gradient-to-b from-[#263e1c] to-[#1a2d13] text-white relative overflow-hidden" style="background: linear-gradient(180deg, #263e1c 0%, #1a2d13 100%);">
    <!-- Decorative background glow -->
    <div class="absolute top-0 right-1/4 w-96 h-96 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <!-- Left: Text and Store Buttons (7 Cols) -->
            <div class="lg:col-span-7 space-y-6">
                <span class="inline-block text-xs font-bold uppercase tracking-wider text-[#ffd65a] bg-white/10 border border-white/20 backdrop-blur-md px-3.5 py-1.5 rounded-full">
                    Unduh Sekarang — Gratis
                </span>

                <h2 class="text-3xl sm:text-4xl md:text-5xl font-black tracking-tight font-fraunces leading-tight" style="font-family: 'Fraunces', Georgia, serif;">
                    Jadilah Bagian dari <br class="hidden sm:block">
                    <span class="text-[#ffd65a]">Gerakan Bersih</span> Jember
                </h2>

                <p class="text-emerald-100/90 text-sm sm:text-base leading-relaxed max-w-xl font-normal">
                    Unduh aplikasi SampahJember sekarang dan mulai berkontribusi menjaga kebersihan lingkungan. Laporkan titik sampah liar dari mana saja, kapan saja.
                </p>

                <!-- Store Buttons using user uploaded assets -->
                <div class="flex flex-wrap items-center gap-4 pt-2">
                    <!-- App Store Button -->
                    <a href="#unduh" class="inline-flex items-center gap-3 px-5 py-3 rounded-2xl bg-black/80 hover:bg-[#ffd65a] border border-white/20 text-white shadow-lg hover:-translate-y-0.5 transition-all">
                        <img src="{{ asset('sampah-jember/icons/apple1.svg') }}" class="w-6 h-6" alt="Apple App Store">
                        <div class="text-left">
                            <div class="text-[9px] uppercase font-medium text-stone-300 leading-none">Unduh di</div>
                            <div class="text-sm font-bold leading-tight">App Store</div>
                        </div>
                    </a>

                    <!-- Google Play Button -->
                    <a href="#unduh" class="inline-flex items-center gap-3 px-5 py-3 rounded-2xl bg-black/80 hover:bg-[#ffd65a] border border-white/20 text-white shadow-lg hover:-translate-y-0.5 transition-all">
                        <img src="{{ asset('sampah-jember/icons/play.svg') }}" class="w-6 h-6" alt="Google Play Store">
                        <div class="text-left">
                            <div class="text-[9px] uppercase font-medium text-stone-300 leading-none">Tersedia di</div>
                            <div class="text-sm font-bold leading-tight">Google Play</div>
                        </div>
                    </a>
                </div>

                <!-- Features Badges List using user uploaded yellow seal badge -->
                <div class="flex flex-wrap gap-4 pt-3 text-xs font-semibold text-emerald-100">
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('sampah-jember/icons/badge-check.svg') }}" class="w-4 h-4" alt="Check">
                        <span>Gratis selamanya</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('sampah-jember/icons/badge-check.svg') }}" class="w-4 h-4" alt="Check">
                        <span>Android & iOS</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('sampah-jember/icons/badge-check.svg') }}" class="w-4 h-4" alt="Check">
                        <span>Tanpa login untuk cek info</span>
                    </div>
                </div>
            </div>

            <!-- Right: Smartphone Device UI Mockup (5 Cols) -->
            <div class="lg:col-span-5 flex justify-center lg:justify-end">
                <div class="w-68 sm:w-76 rounded-[36px] bg-stone-900 p-3.5 shadow-2xl border-4 border-stone-800 ring-1 ring-white/20">
                    <!-- Notch -->
                    <div class="w-20 h-3 bg-stone-800 rounded-full mx-auto mb-3"></div>

                    <!-- Screen Content -->
                    <div class="rounded-[24px] bg-[#fafaf5] p-4 text-stone-900 space-y-3 shadow-inner">
                        <!-- App Header -->
                        <div class="flex items-center justify-between pb-2.5 border-b border-stone-200">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-md bg-[#263e1c] text-[#ffd65a] font-black text-xs flex items-center justify-center">
                                    SJ
                                </div>
                                <span class="font-bold text-xs text-stone-900">SampahJember</span>
                            </div>
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        </div>

                        <!-- Location GPS Detected Card -->
                        <div class="p-3 rounded-xl bg-white border border-stone-200 shadow-2xs space-y-0.5">
                            <span class="text-[10px] font-bold text-[#3c5c2a]">📍 Lokasi Terdeteksi</span>
                            <div class="font-black text-xs text-stone-900">Sumbersari, Jember</div>
                        </div>

                        <!-- Map preview simulation -->
                        <div class="h-24 rounded-xl bg-stone-200/90 relative overflow-hidden flex items-center justify-center border border-stone-300/80">
                            <div class="absolute inset-0 bg-[radial-gradient(#3c5c2a_1px,transparent_1px)] [background-size:12px_12px] opacity-30"></div>
                            <span class="relative text-xl">📍</span>
                        </div>

                        <!-- Status Report Mock -->
                        <div class="p-2.5 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-[11px]">
                            <span class="font-bold text-emerald-900">Status Laporan</span>
                            <span class="px-2 py-0.5 rounded-full bg-emerald-200 text-emerald-800 text-[10px] font-bold">Sedang Ditangani</span>
                        </div>

                        <!-- Action Button in Mockup -->
                        <button type="button" class="w-full py-2.5 rounded-xl bg-[#ffd65a] text-stone-950 font-bold text-xs shadow-xs flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="7 10 12 15 17 10"/>
                                <line x1="12" y1="15" x2="12" y2="3"/>
                            </svg>
                            <span>Ambil Foto & Laporkan</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
