<section id="kecamatan" class="py-20 bg-[#fafaf5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Centered Section Header -->
        <div class="text-center max-w-2xl mx-auto mb-10">
            <span class="inline-block text-xs font-bold uppercase tracking-wider text-amber-800 bg-amber-50 border border-amber-200 px-3.5 py-1.5 rounded-full">
                Cari Kelurahan
            </span>
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-black text-stone-900 tracking-tight mt-3 font-fraunces">
                Data Per Kelurahan
            </h2>
            <p class="text-stone-600 text-sm sm:text-base mt-2">
                Cari dan lihat informasi laporan sampah pada tiap kelurahan di {{ $district->name }}.
            </p>

            <!-- Centered Search Bar -->
            <div class="mt-6 max-w-md mx-auto relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-stone-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="search"
                       id="cari-kecamatan"
                       class="cari-nama-kecamatan w-full pl-11 pr-4 py-3 rounded-2xl bg-white border border-stone-200 focus:border-[#3c5c2a] focus:ring-4 focus:ring-[#3c5c2a]/10 outline-none text-sm text-stone-900 placeholder:text-stone-400 shadow-2xs transition-all"
                       placeholder="Cari nama kelurahan..."
                       aria-label="Cari nama kelurahan">
            </div>
        </div>

        <!-- Main Content Area: 2 Columns of Cards + Right Sticky Notice Box -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- 2-Column Cards Grid (8 Cols) -->
            <div class="lg:col-span-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" id="kecamatan-cards-grid">
                    @foreach($villageCards as $item)
                        @php
                            $percentage = $item['total'] > 0 ? round(($item['selesai'] / $item['total']) * 100) : 0;
                            $statusBadge = match($item['status']) {
                                'Tinggi' => 'bg-rose-100 text-rose-800 border-rose-200',
                                'Sedang' => 'bg-amber-100 text-amber-800 border-amber-200',
                                default  => 'bg-yellow-100/70 text-yellow-800 border-yellow-200',
                            };
                        @endphp
                        <div class="kecamatan-card p-4 rounded-2xl bg-white border border-stone-200 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between"
                             data-name="{{ strtolower($item['name']) }}">
                            <!-- Top: Name & Status Pill -->
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <h4 class="font-bold text-sm text-stone-900">{{ $item['name'] }}</h4>
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold border {{ $statusBadge }}">
                                    {{ $item['status'] }}
                                </span>
                            </div>

                            <!-- Middle: Counts -->
                            <div class="flex items-center justify-between text-xs text-stone-600 pb-2">
                                <div><strong class="text-stone-900 font-bold">{{ $item['total'] }}</strong> lap.</div>
                                <div><strong class="text-emerald-700 font-bold">{{ $item['selesai'] }}</strong> selesai</div>
                                <div><strong class="text-rose-700 font-bold">{{ $item['aktif'] }}</strong> aktif</div>
                            </div>

                            <!-- Bottom: Thin Progress Bar -->
                            <div class="w-full bg-stone-100 h-1.5 rounded-full overflow-hidden mt-1">
                                <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Empty State -->
                <div id="no-results" class="hidden text-center py-12 bg-white rounded-2xl border border-stone-200">
                    <span class="text-3xl block mb-2">🔍</span>
                    <h4 class="text-base font-bold text-stone-800">Kecamatan tidak ditemukan</h4>
                    <p class="text-stone-500 text-xs mt-1">Silakan ketikkan nama kecamatan lain di Kab. Jember.</p>
                </div>
            </div>

            <!-- Right Notice Box (4 Cols) -->
            <div class="lg:col-span-4 sticky top-28">
                <div class="bg-white rounded-3xl p-6 border border-stone-200 shadow-sm text-center space-y-3">
                    <div class="w-14 h-14 rounded-2xl bg-amber-50 border border-amber-200 text-2xl flex items-center justify-center mx-auto shadow-2xs">
                        📦
                    </div>
                    <h4 class="font-bold text-stone-900 text-base">{{ count($villageCards) }} Kelurahan Terdata</h4>
                    <p class="text-xs text-stone-500 leading-relaxed">
                        Data pemantauan sampah mencakup seluruh kelurahan di {{ $district->name }} secara transparan dan terverifikasi petugas desa.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
