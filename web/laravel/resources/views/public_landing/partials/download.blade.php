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
                    Gunakan aplikasi SampahJember untuk mengirim laporan dengan foto dan lokasi GPS, lalu pantau prosesnya saat Admin Desa memvalidasi laporan, petugas membersihkan lokasi, dan hasilnya diverifikasi.
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

                <!-- Features Badges List -->
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
            <div class="lg:col-span-5 flex justify-center lg:justify-end py-6">
                <!-- Frame Mockup HP -->
                <div class="relative w-full max-w-[310px] bg-[#1a1a1a] rounded-[48px] p-4 shadow-2xl border-4 border-[#333]">
                    <!-- Notch / Speaker HP -->
                    <div class="absolute top-4 left-1/2 -translate-x-1/2 w-32 h-5 bg-black rounded-full z-30"></div>

                    <!-- Layar HP -->
                    <div class="relative bg-[#f4f4f0] rounded-[36px] overflow-hidden pt-10 pb-6 px-4 flex flex-col gap-3.5 text-stone-800">

                        <!-- Header App -->
                        <div class="bg-[#3b5e2b] text-white px-3.5 py-2.5 rounded-2xl flex items-center justify-between shadow-md">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-[#ffd65a] text-stone-950 font-bold flex items-center justify-center text-xs shadow">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </div>
                                <span class="font-bold tracking-wide text-sm">PapSampah</span>
                            </div>
                            <!-- Badge Rating Masuk ke Header/Bagian Atas Layar HP -->
                            <div class="bg-white/15 backdrop-blur-xs px-2 py-1 rounded-xl flex items-center gap-1.5 border border-white/20">
                                <span class="text-xs font-black text-[#ffd65a]">4.8</span>
                                <div class="flex text-amber-400 text-[9px]">★</div>
                            </div>
                        </div>

                        <!-- Card Lokasi Terdeteksi -->
                        <div class="bg-white p-3 rounded-2xl shadow-sm border border-stone-200/70 flex items-start gap-2.5">
                            <div class="text-rose-600 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-[10px] font-bold text-stone-400 uppercase tracking-wider">Lokasi Terdeteksi</div>
                                <div class="text-xs font-bold text-stone-800">Kaliwates, Jember</div>
                            </div>
                        </div>

                        <!-- Card Peringatan / Titik Aktif -->
                        <div class="bg-[#fceeea] border border-[#f5c6bc] p-2.5 rounded-xl flex flex-col gap-0.5 text-xs shadow-2xs">
                            <div class="font-bold text-rose-700 flex items-center gap-1.5 text-[11px]">
                                <span>⚠️</span> 3 Titik Aktif Dekat Anda
                            </div>
                            <div class="text-stone-600 text-[10px] pl-4">Klik untuk melihat</div>
                        </div>

                        <!-- Bagian Peta / Mini Peta Area -->
                        <div class="relative bg-[#b2bcab] rounded-2xl h-32 flex flex-col items-center justify-center overflow-hidden border border-stone-300 shadow-inner">
                            <div class="w-10 h-10 bg-white/80 rounded-xl flex items-center justify-center shadow-md mb-1">
                                <svg class="w-6 h-6 text-[#3b5e2b]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                </svg>
                            </div>
                            <div class="absolute bottom-2 left-2.5 bg-white/90 backdrop-blur-xs px-2.5 py-0.5 rounded-lg text-[10px] font-medium text-stone-700 shadow-xs">
                                Mini peta area
                            </div>
                        </div>

                        <!-- Tombol Laporkan Sampah -->
                        <button class="w-full bg-[#3b5e2b] hover:bg-[#325124] text-white font-bold py-3 px-4 rounded-xl shadow-md transition-all flex items-center justify-center gap-1.5 text-xs">
                            <span>+ Laporkan Sampah</span>
                        </button>

                        <!-- Statistik Bawah (Grid 3 Kolom termasuk Total Pengguna/10K+) -->
                        <div class="grid grid-cols-3 gap-2">
                            <div class="bg-white p-2 rounded-xl border border-stone-200/70 shadow-xs text-center flex flex-col justify-center">
                                <span class="text-sm font-black text-amber-500 font-serif leading-tight" style="font-family: 'Fraunces', Georgia, serif;">10K+</span>
                                <span class="text-[9px] text-stone-500 font-semibold tracking-wide">Pengguna</span>
                            </div>
                            <div class="bg-white p-2 rounded-xl border border-stone-200/70 shadow-xs text-center flex flex-col justify-center">
                                <span class="text-sm font-black text-[#3b5e2b] font-serif leading-tight" style="font-family: 'Fraunces', Georgia, serif;">142</span>
                                <span class="text-[9px] text-stone-500 font-semibold tracking-wide">Laporan</span>
                            </div>
                            <div class="bg-white p-2 rounded-xl border border-stone-200/70 shadow-xs text-center flex flex-col justify-center">
                                <span class="text-sm font-black text-amber-600 font-serif leading-tight" style="font-family: 'Fraunces', Georgia, serif;">89%</span>
                                <span class="text-[9px] text-stone-500 font-semibold tracking-wide">Selesai</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
