<footer class="bg-stone-900 text-stone-300 pt-16 pb-12 border-t border-stone-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-12 pb-12 border-b border-stone-800">
            <!-- Brand & Info (5 cols) -->
            <div class="lg:col-span-5 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#ffd65a] text-[#263e1c] flex items-center justify-center font-black text-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xl font-bold tracking-tight text-white leading-tight">
                            <span class="text-[#ffd65a]">Pap</span>Sampah
                        </span>
                        <span class="text-[11px] font-medium text-stone-400 uppercase tracking-wider">{{ $district?->name ?? 'Kecamatan Sumbersari' }}</span>
                    </div>
                </div>
                <p class="text-stone-400 text-sm leading-relaxed max-w-sm">
                    Informasi publik untuk memantau laporan sampah liar dan proses penanganannya di {{ $district?->name ?? 'Kecamatan Sumbersari' }}, dari validasi hingga verifikasi hasil pembersihan.
                </p>
                <div class="flex items-center gap-3 pt-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-stone-800 text-emerald-400 text-xs font-semibold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Portal Publik Aktif
                    </span>
                </div>
            </div>

            <!-- Quick Navigation (3 cols) -->
            <div class="lg:col-span-3 space-y-4">
                <h4 class="text-white font-bold text-sm uppercase tracking-wider">Navigasi</h4>
                <ul class="space-y-2.5 text-sm text-stone-400">
                    <li>
                        <a href="#tentang" class="hover:text-white hover:underline transition-colors">Tentang Platform</a>
                    </li>
                    <li>
                        <a href="#peta" class="hover:text-white hover:underline transition-colors">Peta Heatmap</a>
                    </li>
                    <li>
                        <a href="#kecamatan" class="hover:text-white hover:underline transition-colors">Data Kecamatan</a>
                    </li>
                    <li>
                        <a href="#panduan" class="hover:text-white hover:underline transition-colors">Panduan Penggunaan</a>
                    </li>
                    <li>
                        <a href="#unduh" class="hover:text-white hover:underline transition-colors">Unduh Aplikasi Mobile</a>
                    </li>
                    <li>
                        <a href="https://kecamatansumbersari.jemberkab.go.id/" class="hover:text-white hover:underline transition-colors">Website Resmi</a>
                    </li>
                    @auth
                        <li>
                            <a href="{{ route('dashboard') }}" class="text-[#ffd65a] hover:underline transition-colors font-medium">Dashboard Admin</a>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('login') }}" class="text-[#ffd65a] hover:underline transition-colors font-medium">Portal Login Admin & Petugas</a>
                        </li>
                    @endauth
                </ul>
            </div>

            <!-- Official Contact (4 cols) -->
            <div class="lg:col-span-4 space-y-4">
                <h4 class="text-white font-bold text-sm uppercase tracking-wider">Kontak Dinas</h4>
                <div class="space-y-3 text-sm text-stone-400">
                    <div class="flex items-start gap-2.5">
                        <span class="w-5 h-5 text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </span>
                        <span>Dinas Lingkungan Hidup (DLH) Kabupaten Jember, Jawa Timur</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="w-5 h-5 text-emerald-400 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <a href="kec.sumbersari@jemberkab.go.id" class="hover:text-white transition-colors">kec.sumbersari@jemberkab.go.id</a>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="w-5 h-5 text-emerald-400 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </span>
                        <span>0821-3960-8707 / 0851-7718-00487</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Copyright -->
        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-stone-500">
            <div>
                © {{ date('Y') }} Pap Sampah — Pemerintah Kabupaten Jember. Hak cipta dilindungi.
            </div>
            <div class="flex items-center gap-1 text-stone-400">
                <span>Dibuat dengan</span>
                <span class="text-rose-500">❤️</span>
                <span>untuk lingkungan yang lebih bersih</span>
            </div>
        </div>
    </div>
</footer>
