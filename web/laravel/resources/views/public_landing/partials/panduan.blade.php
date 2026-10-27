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
                Kirim laporan melalui aplikasi mobile, kemudian ikuti proses validasi, penugasan petugas, pembersihan, dan verifikasi hasil oleh Admin Desa.
            </p>
        </div>

        <!-- Yellow Alert Notice Box -->
        <div class="max-w-4xl mx-auto p-4 sm:p-5 rounded-2xl bg-[#fffdf0] border border-amber-200/90 flex items-start gap-4 mb-12 shadow-2xs">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="space-y-1">
                <h4 class="font-bold text-sm sm:text-base text-amber-950">Pelaporan Hanya Melalui Aplikasi Mobile</h4>
                <p class="text-xs sm:text-sm text-stone-600 leading-relaxed">
                    Buat laporan sampah liar melalui aplikasi mobile <strong class="text-stone-900 font-semibold">PapSampah</strong> dengan foto dan lokasi. Website ini menyediakan peta, status, dan ringkasan informasi publik; penanganan dilakukan setelah laporan divalidasi Admin Desa.
                </p>
            </div>
        </div>

        <!-- 6 Steps Grid (3x2) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
            <!-- Langkah 1 -->
            <div class="p-6 rounded-2xl bg-white border border-stone-200 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xl w-10 h-10 rounded-xl bg-stone-50 flex items-center justify-center border border-stone-200/70 text-amber-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <span class="text-2xl font-black text-stone-300 font-fraunces">01</span>
                    </div>
                    <h4 class="font-bold text-base text-stone-900 mb-2">Unduh Aplikasi</h4>
                    <p class="text-stone-500 text-xs sm:text-sm leading-relaxed">
                        Unduh aplikasi PapSampah secara gratis melalui Google Play Store atau App Store.
                    </p>
                </div>
                <button onclick="openStepModal(1)" class="mt-6 pt-4 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-500 hover:text-[#3c6426] font-medium w-full cursor-pointer transition-colors">
                    <span>Buka panduan interaktif</span>
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>

            <!-- Langkah 2 -->
            <div class="p-6 rounded-2xl bg-white border border-stone-200 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xl w-10 h-10 rounded-xl bg-stone-50 flex items-center justify-center border border-stone-200/70 text-indigo-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </span>
                        <span class="text-2xl font-black text-stone-300 font-fraunces">02</span>
                    </div>
                    <h4 class="font-bold text-base text-stone-900 mb-2">Daftar & Masuk</h4>
                    <p class="text-stone-500 text-xs sm:text-sm leading-relaxed">
                        Buat akun masyarakat melalui aplikasi mobile, lalu masuk untuk mulai melapor.
                    </p>
                </div>
                <button onclick="openStepModal(2)" class="mt-6 pt-4 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-500 hover:text-[#3c6426] font-medium w-full cursor-pointer transition-colors">
                    <span>Buka panduan interaktif</span>
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>

            <!-- Langkah 3 -->
            <div class="p-6 rounded-2xl bg-white border border-stone-200 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xl w-10 h-10 rounded-xl bg-stone-50 flex items-center justify-center border border-stone-200/70 text-rose-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </span>
                        <span class="text-2xl font-black text-stone-300 font-fraunces">03</span>
                    </div>
                    <h4 class="font-bold text-base text-stone-900 mb-2">Ambil Lokasi</h4>
                    <p class="text-stone-500 text-xs sm:text-sm leading-relaxed">
                        Aktifkan GPS dan kirim laporan untuk lokasi di wilayah Kecamatan Sumbersari. Sistem menentukan kelurahan berdasarkan koordinat.
                    </p>
                </div>
                <button onclick="openStepModal(3)" class="mt-6 pt-4 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-500 hover:text-[#3c6426] font-medium w-full cursor-pointer transition-colors">
                    <span>Buka panduan interaktif</span>
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>

            <!-- Langkah 4 -->
            <div class="p-6 rounded-2xl bg-white border border-stone-200 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xl w-10 h-10 rounded-xl bg-stone-50 flex items-center justify-center border border-stone-200/70 text-amber-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </span>
                        <span class="text-2xl font-black text-stone-300 font-fraunces">04</span>
                    </div>
                    <h4 class="font-bold text-base text-stone-900 mb-2">Kirim Laporan</h4>
                    <p class="text-stone-500 text-xs sm:text-sm leading-relaxed">
                        Pilih kategori, sertakan foto kondisi sampah, lalu tambahkan deskripsi agar Admin Desa dapat memvalidasi laporan.
                    </p>
                </div>
                <button onclick="openStepModal(4)" class="mt-6 pt-4 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-500 hover:text-[#3c6426] font-medium w-full cursor-pointer transition-colors">
                    <span>Buka panduan interaktif</span>
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>

            <!-- Langkah 5 -->
            <div class="p-6 rounded-2xl bg-white border border-stone-200 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xl w-10 h-10 rounded-xl bg-stone-50 flex items-center justify-center border border-stone-200/70 text-emerald-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </span>
                        <span class="text-2xl font-black text-stone-300 font-fraunces">05</span>
                    </div>
                    <h4 class="font-bold text-base text-stone-900 mb-2">Pantau Penanganan</h4>
                    <p class="text-stone-500 text-xs sm:text-sm leading-relaxed">
                        Admin Desa memvalidasi laporan yang masuk. Jika valid, satu pekerjaan pembersihan ditugaskan kepada petugas di desa tersebut.
                    </p>
                </div>
                <button onclick="openStepModal(5)" class="mt-6 pt-4 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-500 hover:text-[#3c6426] font-medium w-full cursor-pointer transition-colors">
                    <span>Buka panduan interaktif</span>
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>

            <!-- Langkah 6 -->
            <div class="p-6 rounded-2xl bg-white border border-stone-200 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xl w-10 h-10 rounded-xl bg-stone-50 flex items-center justify-center border border-stone-200/70 text-teal-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </span>
                        <span class="text-2xl font-black text-stone-300 font-fraunces">06</span>
                    </div>
                    <h4 class="font-bold text-base text-stone-900 mb-2">Lihat Hasil</h4>
                    <p class="text-stone-500 text-xs sm:text-sm leading-relaxed">
                        Petugas membersihkan lokasi dan mengirim foto hasil. Admin Desa memverifikasi bukti; setelah valid, laporan berstatus selesai.
                    </p>
                </div>
                <button onclick="openStepModal(6)" class="mt-6 pt-4 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-500 hover:text-[#3c6426] font-medium w-full cursor-pointer transition-colors">
                    <span>Buka panduan interaktif</span>
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Modal Pop-up Panduan Interaktif -->
<div id="step-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs hidden p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-xl border border-stone-100 relative transform transition-all">
        <!-- Close Button -->
        <button onclick="closeStepModal()" class="absolute top-5 right-5 text-stone-400 hover:text-stone-700 p-2 rounded-full hover:bg-stone-100 transition-colors cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <!-- Modal Header / Badge -->
        <div class="flex items-center gap-3 mb-4">
            <span id="modal-step-badge" class="px-3 py-1 bg-amber-50 text-amber-800 border border-amber-200 text-xs font-bold rounded-full">
                Langkah 01 dari 06
            </span>
        </div>

        <!-- Modal Content Title & Desc -->
        <h3 id="modal-title" class="text-xl sm:text-2xl font-black text-stone-900 mb-3 font-fraunces">Judul Langkah</h3>
        <p id="modal-desc" class="text-stone-600 text-sm sm:text-base leading-relaxed mb-6">Deskripsi lengkap panduan akan muncul di sini.</p>

        <!-- Navigation Buttons -->
        <div class="flex items-center justify-between pt-4 border-t border-stone-100">
            <button onclick="prevStep()" id="btn-prev" class="px-4 py-2 rounded-xl border border-stone-200 text-xs font-bold text-stone-700 hover:bg-stone-50 transition-colors cursor-pointer flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Sebelumnya
            </button>
            <button onclick="nextStep()" id="btn-next" class="px-5 py-2 rounded-xl bg-[#3c6426] text-white text-xs font-bold hover:bg-[#2e4e1d] transition-colors cursor-pointer flex items-center gap-1.5 shadow-sm">
                Selanjutnya
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>
</div>

<!-- JavaScript untuk Logika Modal Pop-up -->
<script>
    const stepsData = [
        {
            num: "01",
            title: "Unduh Aplikasi",
            desc: "Unduh aplikasi PapSampah secara gratis melalui Google Play Store atau App Store pada perangkat smartphone Anda untuk mulai mengakses sistem pelaporan."
        },
        {
            num: "02",
            title: "Daftar & Masuk",
            desc: "Buat akun masyarakat baru menggunakan data diri yang valid melalui aplikasi mobile, lalu masuk (login) ke dalam aplikasi untuk mulai membuat laporan."
        },
        {
            num: "03",
            title: "Ambil Lokasi",
            desc: "Aktifkan GPS pada perangkat Anda dan kirim laporan untuk lokasi di wilayah Kecamatan Sumbersari. Sistem secara otomatis mendeteksi kelurahan berdasarkan koordinat peta."
        },
        {
            num: "04",
            title: "Kirim Laporan",
            desc: "Pilih kategori sampah liar, lampirkan foto kondisi riil di lapangan, lalu tambahkan deskripsi tempat agar Admin Desa dapat melakukan validasi secara akurat."
        },
        {
            num: "05",
            title: "Pantau Penanganan",
            desc: "Admin Desa akan memverifikasi laporan yang masuk. Jika valid, tugas pembersihan akan diteruskan dan ditugaskan kepada petugas kebersihan di wilayah desa tersebut."
        },
        {
            num: "06",
            title: "Lihat Hasil",
            desc: "Petugas membersihkan lokasi dan mengunggah foto bukti penyelesaian. Admin Desa memeriksa ulang; setelah disetujui, status laporan akan berubah menjadi selesai."
        }
    ];

    let currentStepIndex = 0;

    function openStepModal(stepNumber) {
        currentStepIndex = stepNumber - 1;
        updateModalContent();
        document.getElementById('step-modal').classList.remove('hidden');
    }

    function closeStepModal() {
        document.getElementById('step-modal').classList.add('hidden');
    }

    function updateModalContent() {
        const step = stepsData[currentStepIndex];
        document.getElementById('modal-step-badge').textContent = `Langkah ${step.num} dari 06`;
        document.getElementById('modal-title').textContent = step.title;
        document.getElementById('modal-desc').textContent = step.desc;

        // Atur status disabled/aktif pada tombol navigasi
        const btnPrev = document.getElementById('btn-prev');
        const btnNext = document.getElementById('btn-next');

        if (currentStepIndex === 0) {
            btnPrev.style.opacity = "0.4";
            btnPrev.style.pointerEvents = "none";
        } else {
            btnPrev.style.opacity = "1";
            btnPrev.style.pointerEvents = "auto";
        }

        if (currentStepIndex === stepsData.length - 1) {
            btnNext.style.opacity = "0.4";
            btnNext.style.pointerEvents = "none";
        } else {
            btnNext.style.opacity = "1";
            btnNext.style.pointerEvents = "auto";
        }
    }

    function nextStep() {
        if (currentStepIndex < stepsData.length - 1) {
            currentStepIndex++;
            updateModalContent();
        }
    }

    function prevStep() {
        if (currentStepIndex > 0) {
            currentStepIndex--;
            updateModalContent();
        }
    }

    // Tutup modal jika user mengklik area gelap di luar kotak modal
    document.getElementById('step-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeStepModal();
        }
    });
</script>
