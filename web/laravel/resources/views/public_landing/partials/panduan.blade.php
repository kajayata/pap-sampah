<section id="panduan" class="py-20 bg-[#fafaf5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Centered Header -->
        <div class="text-center max-w-2xl mx-auto mb-10">
            <span class="inline-block text-xs font-bold uppercase tracking-wider text-[#3c6426] bg-[#eef5e9] border border-[#3c6426]/20 px-3.5 py-1.5 rounded-full">
                Panduan Penggunaan
            </span>
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-black text-stone-900 tracking-tight mt-3 font-fraunces">
                Cara Pakai Aplikasi Mobile
            </h2>
            <p class="text-stone-600 text-sm sm:text-base mt-2">
                Pelaporan sampah liar dilakukan melalui aplikasi mobile SampahJember. Ikuti langkah-langkah mudah berikut.
            </p>
        </div>

        <!-- Yellow Alert Notice Box (Matching Screenshot) -->
        <div class="max-w-4xl mx-auto p-4 sm:p-5 rounded-2xl bg-[#fffdf0] border border-amber-200/90 flex items-start gap-4 mb-12 shadow-2xs">
            <span class="text-2xl shrink-0 mt-0.5">📱</span>
            <div class="space-y-1">
                <h4 class="font-bold text-sm sm:text-base text-amber-950">Pelaporan Hanya Melalui Aplikasi Mobile</h4>
                <p class="text-xs sm:text-sm text-stone-600 leading-relaxed">
                    Untuk melaporkan titik sampah liar, silakan unduh dan gunakan aplikasi mobile <strong class="text-stone-900 font-semibold">SampahJember</strong>. Website ini hanya menampilkan informasi dan statistik publik.
                </p>
            </div>
        </div>

        <!-- 6 Steps Grid (3x2) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
            @foreach($guideSteps as $step)
                <div class="p-6 rounded-2xl bg-white border border-stone-200 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <!-- Top: Icon on left, Step Number on right -->
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-2xl w-10 h-10 rounded-xl bg-stone-50 flex items-center justify-center border border-stone-200/70">
                                {{ $step['icon'] }}
                            </span>
                            <span class="text-2xl font-black text-stone-300 font-fraunces">
                                {{ $step['number'] }}
                            </span>
                        </div>
                        <h4 class="font-bold text-base text-stone-900 mb-2">
                            {{ $step['title'] }}
                        </h4>
                        <p class="text-stone-500 text-xs sm:text-sm leading-relaxed">
                            {{ $step['description'] }}
                        </p>
                    </div>

                    <!-- Bottom helper text matching Figma with green chevron asset -->
                    <div class="mt-6 pt-4 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-400 font-medium">
                        <span>Lanjut ke langkah berikutnya</span>
                        <img src="{{ asset('sampah-jember/icons/chevron-green.svg') }}" class="w-3.5 h-3.5" alt="Next">
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
