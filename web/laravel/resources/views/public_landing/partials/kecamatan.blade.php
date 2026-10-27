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
                Telusuri jumlah laporan aktif dan selesai di setiap kelurahan dalam wilayah {{ $district->name }}.
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

        <!-- Main Content Area: 2 Columns of Cards + Right Sticky Notice/Detail Box -->
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
                        <div class="kecamatan-card p-4 rounded-2xl bg-white border border-stone-200 shadow-2xs hover:shadow-md hover:border-[#3c5c2a] cursor-pointer transition-all flex flex-col justify-between"
                             data-name="{{ strtolower($item['name']) }}"
                             data-title="{{ $item['name'] }}"
                             data-total="{{ $item['total'] }}"
                             data-selesai="{{ $item['selesai'] }}"
                             data-aktif="{{ $item['aktif'] }}"
                             data-status="{{ $item['status'] }}"
                             data-percentage="{{ $percentage }}">

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
                    <h4 class="text-base font-bold text-stone-800">Kelurahan tidak ditemukan</h4>
                    <p class="text-stone-500 text-xs mt-1">Periksa kembali nama kelurahan di {{ $district->name }}.</p>
                </div>
            </div>

            <!-- Right Dynamic Info Box (4 Cols) -->
            <div class="lg:col-span-4 sticky top-28">
                <div id="default-notice-box" class="bg-white rounded-3xl p-6 border border-stone-200 shadow-sm text-center space-y-3">
                    <div class="w-14 h-14 rounded-2xl bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center mx-auto shadow-2xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <h4 class="font-bold text-stone-900 text-base">{{ count($villageCards) }} Kelurahan Terdata</h4>
                    <p class="text-xs text-stone-500 leading-relaxed">
                        Ringkasan laporan per kelurahan membantu melihat jumlah laporan yang masih ditangani dan yang telah selesai diverifikasi Admin Desa. <br><span class="text-amber-700 font-medium mt-1 block">💡 Klik salah satu kartu kelurahan di samping untuk melihat detail spesifik.</span>
                    </p>
                </div>

                <!-- Selected Detail Box (Hidden by default, shown on click) -->
                <div id="selected-detail-box" class="hidden bg-white rounded-3xl p-6 border border-[#3c5c2a]/30 shadow-md space-y-4 transition-all">
                    <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#3c5c2a] bg-[#3c5c2a]/10 px-2.5 py-1 rounded-full">Detail Kelurahan</span>
                            <h3 id="detail-name" class="font-black text-lg text-stone-900 mt-1">Nama Kelurahan</h3>
                        </div>
                        <button id="reset-selection" class="text-xs text-stone-400 hover:text-stone-700 underline cursor-pointer">Reset</button>
                    </div>

                    <div class="grid grid-cols-3 gap-2 text-center bg-stone-50 p-3 rounded-2xl border border-stone-100">
                        <div>
                            <div class="text-[11px] text-stone-500">Total</div>
                            <div id="detail-total" class="font-bold text-stone-900 text-base">0</div>
                        </div>
                        <div>
                            <div class="text-[11px] text-emerald-600">Selesai</div>
                            <div id="detail-selesai" class="font-bold text-emerald-700 text-base">0</div>
                        </div>
                        <div>
                            <div class="text-[11px] text-rose-600">Aktif</div>
                            <div id="detail-aktif" class="font-bold text-rose-700 text-base">0</div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-xs text-stone-600 mb-1">
                            <span>Penyelesaian</span>
                            <span id="detail-percentage-text" class="font-bold text-emerald-600">0%</span>
                        </div>
                        <div class="w-full bg-stone-100 h-2 rounded-full overflow-hidden">
                            <div id="detail-progress-bar" class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: 0%"></div>
                        </div>
                    </div>

                    <p class="text-xs text-stone-500 leading-relaxed pt-1 border-t border-stone-100">
                        Status beban laporan di wilayah ini tergolong <strong id="detail-status" class="text-stone-800">Sedang</strong> berdasarkan total laporan yang masuk.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- JavaScript untuk Handle Klik & Interaksi -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cards = document.querySelectorAll('.kecamatan-card');
        const defaultBox = document.getElementById('default-notice-box');
        const detailBox = document.getElementById('selected-detail-box');
        const resetBtn = document.getElementById('reset-selection');

        const elName = document.getElementById('detail-name');
        const elTotal = document.getElementById('detail-total');
        const elSelesai = document.getElementById('detail-selesai');
        const elAktif = document.getElementById('detail-aktif');
        const elStatus = document.getElementById('detail-status');
        const elPercentageText = document.getElementById('detail-percentage-text');
        const elProgressBar = document.getElementById('detail-progress-bar');

        cards.forEach(card => {
            card.addEventListener('click', function () {
                // Hapus border aktif dari semua card lain, lalu beri tanda di card yang diklik
                cards.forEach(c => c.classList.remove('ring-2', 'ring-[#3c5c2a]', 'border-transparent'));
                this.classList.add('ring-2', 'ring-[#3c5c2a]');

                // Ambil data dari atribut data-*
                const name = this.getAttribute('data-title');
                const total = this.getAttribute('data-total');
                const selesai = this.getAttribute('data-selesai');
                const aktif = this.getAttribute('data-aktif');
                const status = this.getAttribute('data-status');
                const percentage = this.getAttribute('data-percentage');

                // Masukkan ke dalam kotak detail kanan
                elName.textContent = name;
                elTotal.textContent = total;
                elSelesai.textContent = selesai;
                elAktif.textContent = aktif;
                elStatus.textContent = status;
                elPercentageText.textContent = percentage + '%';
                elProgressBar.style.width = percentage + '%';

                // Tampilkan kotak detail, sembunyikan kotak default
                defaultBox.classList.add('hidden');
                detailBox.classList.remove('hidden');
            });
        });

        // Tombol reset untuk kembali ke tampilan awal
        resetBtn.addEventListener('click', function () {
            cards.forEach(c => c.classList.remove('ring-2', 'ring-[#3c5c2a]'));
            detailBox.classList.add('hidden');
            defaultBox.classList.remove('hidden');
        });
    });
</script>
