<footer class="bg-stone-900 text-stone-300 pt-16 pb-12 border-t border-stone-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-12 pb-12 border-b border-stone-800">
            <!-- Brand & Info (5 cols) -->
            <div class="lg:col-span-5 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#5b7e3c] text-[#ffd65a] flex items-center justify-center font-black text-lg">
                        SJ
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xl font-bold tracking-tight text-white leading-tight">
                            <span class="text-[#ffd65a]">Sampah</span>Jember
                        </span>
                        <span class="text-[11px] font-medium text-stone-400 uppercase tracking-wider">{{ $district?->name ?? 'Kecamatan Sumbersari' }}</span>
                    </div>
                </div>
                <p class="text-stone-400 text-sm leading-relaxed max-w-sm">
                    Platform transparansi dan pemantauan titik sampah liar di {{ $district?->name ?? 'Kecamatan Sumbersari' }}. Wujudkan lingkungan yang asri, sehat, dan bebas sampah.
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
                        <span class="text-base">🏢</span>
                        <span>Dinas Lingkungan Hidup (DLH) Kabupaten Jember, Jawa Timur</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="text-base">📧</span>
                        <a href="mailto:dlh@jemberkab.go.id" class="hover:text-white transition-colors">dlh@jemberkab.go.id</a>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="text-base">📞</span>
                        <span>(0331) 123-4567</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Copyright -->
        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-stone-500">
            <div>
                © {{ date('Y') }} SampahJember — Pemerintah Kabupaten Jember. Hak cipta dilindungi.
            </div>
            <div class="flex items-center gap-1 text-stone-400">
                <span>Dibuat dengan</span>
                <span class="text-rose-500">❤️</span>
                <span>untuk lingkungan yang lebih bersih</span>
            </div>
        </div>
    </div>
</footer>
