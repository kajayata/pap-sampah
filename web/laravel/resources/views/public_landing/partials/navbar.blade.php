<header id="main-header" class="header-bg sticky top-0 z-50 bg-[#263e1c]/95 backdrop-blur-md border-b border-emerald-800/40 transition-all text-white" style="background-color: rgba(38, 62, 28, 0.96);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Brand Logo -->
            <a href="#" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-[#ffd65a] text-[#263e1c] flex items-center justify-center font-black text-lg shadow-sm group-hover:scale-105 transition-transform" style="background-color: #ffd65a; color: #263e1c;">
                    SJ
                </div>
                <div class="flex flex-col">
                    <span class="text-xl font-bold tracking-tight text-white leading-tight">
                        Jember<span class="text-[#ffd65a]" style="color: #ffd65a;">Sampah</span>
                    </span>
                    <span class="text-[10px] font-medium text-emerald-200/80 tracking-wider uppercase">Pemerintah Kab. Jember</span>
                </div>
            </a>

            <!-- Desktop Navigation Links -->
            <nav class="hidden md:flex items-center gap-1 lg:gap-3">
                <a href="#tentang" class="px-3.5 py-2 text-sm font-medium text-emerald-100 hover:text-[#ffd65a] hover:bg-white/10 rounded-xl transition-colors">
                    Tentang
                </a>
                <a href="#peta" class="px-3.5 py-2 text-sm font-medium text-emerald-100 hover:text-[#ffd65a] hover:bg-white/10 rounded-xl transition-colors">
                    Peta
                </a>
                <a href="#kecamatan" class="px-3.5 py-2 text-sm font-medium text-emerald-100 hover:text-[#ffd65a] hover:bg-white/10 rounded-xl transition-colors">
                    Kecamatan
                </a>
                <a href="#panduan" class="px-3.5 py-2 text-sm font-medium text-emerald-100 hover:text-[#ffd65a] hover:bg-white/10 rounded-xl transition-colors">
                    Panduan
                </a>
            </nav>

            <!-- CTA Button & Mobile Toggle -->
            <div class="flex items-center gap-3">
                <a href="#unduh" class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl bg-[#ffd65a] text-stone-950 hover:bg-[#f3c846] shadow-sm hover:shadow-md transition-all" style="background-color: #ffd65a; color: #0c0a09;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    <span>Unduh App</span>
                </a>

                <!-- Mobile menu toggle button -->
                <button id="mobile-menu-btn" type="button" class="md:hidden p-2.5 rounded-xl text-emerald-100 hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-[#ffd65a]" aria-label="Buka menu navigasi" aria-expanded="false">
                    <svg id="hamburger-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg id="close-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Drawer Menu -->
    <div id="mobile-menu" class="md:hidden hidden border-b border-emerald-800/60 bg-[#263e1c]/98 px-4 pt-3 pb-6 space-y-2 shadow-xl" style="background-color: rgba(38, 62, 28, 0.98);">
        <a href="#tentang" class="mobile-nav-link block px-4 py-2.5 text-base font-medium text-emerald-100 hover:bg-white/10 rounded-xl transition-colors">
            Tentang
        </a>
        <a href="#peta" class="mobile-nav-link block px-4 py-2.5 text-base font-medium text-emerald-100 hover:bg-white/10 rounded-xl transition-colors">
            Peta Persebaran
        </a>
        <a href="#kecamatan" class="mobile-nav-link block px-4 py-2.5 text-base font-medium text-emerald-100 hover:bg-white/10 rounded-xl transition-colors">
            Data Kecamatan
        </a>
        <a href="#panduan" class="mobile-nav-link block px-4 py-2.5 text-base font-medium text-emerald-100 hover:bg-white/10 rounded-xl transition-colors">
            Panduan Penggunaan
        </a>
        <div class="pt-2">
            <a href="#unduh" class="mobile-nav-link flex items-center justify-center gap-2 w-full px-4 py-3 text-base font-bold rounded-xl bg-[#ffd65a] text-stone-950 hover:bg-[#f3c846] shadow-sm transition-all" style="background-color: #ffd65a; color: #0c0a09;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                <span>Unduh Aplikasi Mobile</span>
            </a>
        </div>
    </div>
</header>
