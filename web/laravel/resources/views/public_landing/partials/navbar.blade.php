<header id="main-header" class="header-bg sticky top-0 z-50 bg-[#263e1c]/95 backdrop-blur-md border-b border-emerald-800/40 transition-all text-white" style="background-color: rgba(38, 62, 28, 0.96);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Brand Logo -->
            <a href="{{ route('public.home') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-[#ffd65a] text-[#263e1c] flex items-center justify-center font-black text-lg shadow-sm group-hover:scale-105 transition-transform" style="background-color: #ffd65a; color: #263e1c;">
                    PS
                </div>
                <div class="flex flex-col">
                    <span class="text-xl font-bold tracking-tight text-white leading-tight">
                        Pap<span class="text-[#ffd65a]" style="color: #ffd65a;">Sampah</span>
                    </span>
                    <span class="text-[10px] font-medium text-emerald-200/80 tracking-wider uppercase">Kec. Sumbersari</span>
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
            <div class="flex items-center gap-2.5 sm:gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-3.5 py-2 sm:px-4 sm:py-2.5 text-xs sm:text-sm font-bold rounded-xl bg-white/15 hover:bg-white/25 border border-white/20 text-white backdrop-blur-md shadow-sm transition-all hover:-translate-y-0.5">
                        <svg class="w-4 h-4 text-[#ffd65a]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Login</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-3.5 py-2 sm:px-4 sm:py-2.5 text-xs sm:text-sm font-bold rounded-xl bg-white/15 hover:bg-white/25 border border-white/20 text-white backdrop-blur-md shadow-sm hover:text-[#ffd65a] transition-all hover:-translate-y-0.5">
                        <svg class="w-4 h-4 text-[#ffd65a]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        <span>Login</span>
                    </a>
                @endauth

                <a href="#unduh" class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl bg-[#ffd65a] text-stone-950 hover:bg-[#f3c846] shadow-sm hover:shadow-md transition-all hover:-translate-y-0.5" style="background-color: #ffd65a; color: #0c0a09;">
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
        <div class="pt-2 flex flex-col gap-2.5">
            @auth
                <a href="{{ route('dashboard') }}" class="mobile-nav-link flex items-center justify-center gap-2 w-full px-4 py-3 text-base font-bold rounded-xl bg-white/15 text-white hover:bg-white/25 border border-white/20 shadow-sm transition-all">
                    <svg class="w-5 h-5 text-[#ffd65a]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Ke Dashboard Admin</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="mobile-nav-link flex items-center justify-center gap-2 w-full px-4 py-3 text-base font-bold rounded-xl bg-white/15 text-white hover:bg-white/25 border border-white/20 shadow-sm transition-all">
                    <svg class="w-5 h-5 text-[#ffd65a]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    <span>Login Petugas & Admin</span>
                </a>
            @endauth

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
