@extends('layouts.app')

@section('title', 'Tambah Petugas Kebersihan — Pap Sampah')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-8">

    <div class="flex items-center gap-3">
        <a href="{{ route('worker.index') }}"
           class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-slate-900 transition">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div class="mb-8">
            <h1 class="text-2xl font-bold font-serif text-slate-900">Tambah Petugas Kebersihan</h1>
            <p class="text-xs text-slate-500">
                Daftarkan akun petugas kebersihan desa baru. 
            </p>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="bg-surface rounded-3xl border border-border-default p-6 sm:p-8 shadow-sm">
        <form method="POST" action="{{ route('worker.store') }}" class="space-y-6">
            @csrf

            {{-- Nama Lengkap --}}
            <div>
                <label for="name" class="block text-sm font-semibold text-text-primary mb-2">Nama Lengkap Petugas <span class="text-accent-danger">*</span></label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-text-muted/60">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </span>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Contoh: Budi Santoso" class="w-full pl-12 pr-4 py-3 bg-base border border-border-default rounded-2xl text-sm text-text-primary placeholder:text-text-muted/50 focus:outline-none focus:ring-2 focus:ring-accent-primary/20 focus:border-accent-primary transition-all @error('name') border-accent-danger @enderror">
                </div>
                @error('name')
                    <p class="text-xs text-accent-danger mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Wilayah Kelurahan Operasional --}}
            <div>
                <label for="village_id" class="block text-sm font-semibold text-text-primary mb-2">Wilayah Kelurahan Operasional <span class="text-accent-danger">*</span></label>
                @if(Auth::user()->hasRole('admin_desa'))
                    <div class="px-4 py-3 bg-base border border-border-default rounded-2xl text-sm text-text-primary flex items-center justify-between">
                        <span class="font-medium" id="village_name_display" data-slug="{{ strtolower(Auth::user()->village?->name ?? '') }}">{{ Auth::user()->village?->name }}</span>
                        <span class="text-xs text-text-muted bg-surface px-2.5 py-1 rounded-lg border border-border-default">Sesuai Desa Admin</span>
                    </div>
                    <input type="hidden" name="village_id" id="village_id" value="{{ Auth::user()->village_id }}">
                @else
                    <select id="village_id" name="village_id" required class="w-full px-4 py-3 bg-base border border-border-default rounded-2xl text-sm text-text-primary focus:outline-none focus:ring-2 focus:ring-accent-primary/20 focus:border-accent-primary transition-all @error('village_id') border-accent-danger @enderror">
                        <option value="">-- Pilih Kelurahan --</option>
                        @foreach($villages as $vil)
                            <option value="{{ $vil->id }}" data-slug="{{ strtolower($vil->name) }}" {{ old('village_id') == $vil->id ? 'selected' : '' }}>{{ $vil->name }}</option>
                        @endforeach
                    </select>
                    @error('village_id')
                        <p class="text-xs text-accent-danger mt-1.5">{{ $message }}</p>
                    @enderror
                @endif
                <p class="text-xs text-text-muted mt-1.5">Sesuai aturan arsitektur, petugas kebersihan terikat dan hanya dapat menerima tugas pembersihan pada wilayah kelurahannya.</p>
            </div>

            {{-- Email --}}
            <div>
    <label for="email" class="block text-sm font-semibold text-text-primary mb-2">Alamat Email (Username Login) <span class="text-accent-danger">*</span></label>
    <div class="relative">
        <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-text-muted/60">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
            </svg>
        </span>
        <input type="email" id="email" name="email" value="{{ old('email') }}" readonly required placeholder="Memuat email otomatis..." class="w-full pl-12 pr-4 py-3 bg-slate-100 border border-border-default rounded-2xl text-sm text-text-primary font-medium focus:outline-none cursor-not-allowed @error('email') border-accent-danger @enderror">
    </div>
    <p class="text-xs text-text-muted mt-1.5">Alamat email dibuat otomatis oleh sistem berdasarkan wilayah kelurahan.</p>
    @error('email')
        <p class="text-xs text-accent-danger mt-1.5">{{ $message }}</p>
    @enderror
</div>

            {{-- Telepon --}}
            <div>
                <label for="phone" class="block text-sm font-semibold text-text-primary mb-2">Nomor Telepon / WhatsApp <span class="text-accent-danger">*</span></label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-text-muted/60">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/>
                        </svg>
                    </span>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required placeholder="08xxxxxxxxxx" class="w-full pl-12 pr-4 py-3 bg-base border border-border-default rounded-2xl text-sm text-text-primary placeholder:text-text-muted/50 focus:outline-none focus:ring-2 focus:ring-accent-primary/20 focus:border-accent-primary transition-all @error('phone') border-accent-danger @enderror">
                </div>
                @error('phone')
                    <p class="text-xs text-accent-danger mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password & Konfirmasi --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-border-default">
                <div>
                    <label for="password" class="block text-sm font-semibold text-text-primary mb-2">Kata Sandi Awal <span class="text-accent-danger">*</span></label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required 
                            placeholder="Minimal 8 karakter" 
                            class="w-full pl-4 pr-12 py-3 bg-slate-100 border border-border-default rounded-2xl text-sm text-text-primary focus:outline-none focus:ring-2 focus:ring-primary/20 @error('password') border-accent-danger @enderror">
                        
                        <!-- Tombol Toggle Mata -->
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-text-muted hover:text-text-primary focus:outline-none">
                            <!-- Icon Mata Tertutup (Eye Off) -->
                            <svg id="eyeOffIcon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a9.979 9.979 0 013.122-.463c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-6.115-3.48a3 3 0 104.243-4.243M3 3l18 18" />
                            </svg>
                            <!-- Icon Mata Terbuka (Eye) -->
                            <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                        <p class="text-xs text-text-muted mt-1.5">Otomatis diisi oleh sistem, klik ikon mata untuk melihat.</p>
                        @error('password')
                            <p class="text-xs text-accent-danger mt-1.5">{{ $message }}</p>
                        @enderror   
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-text-primary mb-2">Konfirmasi Kata Sandi <span class="text-accent-danger">*</span></label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi kata sandi" class="w-full px-4 py-3 bg-base border border-border-default rounded-2xl text-sm text-text-primary placeholder:text-text-muted/50 focus:outline-none focus:ring-2 focus:ring-accent-primary/20 focus:border-accent-primary transition-all">
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-border-default">
                <a href="{{ route('worker.index') }}" class="px-5 py-2.5 bg-base hover:bg-border-default/10 border border-border-default text-text-primary font-medium text-sm rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-accent-primary text-white font-medium text-sm rounded-xl hover:-translate-y-0.5 shadow-sm hover:shadow transition-all duration-200">
                    Daftarkan Petugas
                </button>
            </div>
        </form>
    </div>

</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const villageSelect = document.getElementById('village_id');
    const villageDisplay = document.getElementById('village_name_display');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    
    // Elemen Toggle Password
    const togglePasswordBtn = document.getElementById('togglePassword');
    const eyeIcon = document.getElementById('eyeIcon');
    const eyeOffIcon = document.getElementById('eyeOffIcon');

    // Logic 1: Toggle Show/Hide Password
    if (togglePasswordBtn) {
        togglePasswordBtn.addEventListener('click', function () {
            const isPassword = passwordInput.type === 'password';
            
            // Ubah type input password & confirmation secara bersamaan
            passwordInput.type = isPassword ? 'text' : 'password';
            if (passwordConfirmInput) {
                passwordConfirmInput.type = isPassword ? 'text' : 'password';
            }

            // Swapping Icon SVG
            eyeIcon.classList.toggle('hidden', isPassword);
            eyeOffIcon.classList.toggle('hidden', !isPassword);
        });
    }

    // Logic 2: Auto-generate Fields (Email & Password)
    function generateAutoFields() {
        let villageId = villageSelect ? villageSelect.value : (villageDisplay ? villageDisplay.dataset.id : '');
        let nameValue = nameInput ? nameInput.value : '';

        // Auto Password
        if (nameValue.trim() !== '') {
            let cleanName = nameValue.trim().toLowerCase().replace(/[^a-z0-9]/g, '');
            let firstName = cleanName.split(' ')[0] || 'petugas';
            let autoPassword = firstName + '123';

            if (autoPassword.length < 8) {
                autoPassword = firstName + '123456';
            }

            if (passwordInput) passwordInput.value = autoPassword;
            if (passwordConfirmInput) passwordConfirmInput.value = autoPassword;
        } else {
            if (passwordInput) passwordInput.value = '';
            if (passwordConfirmInput) passwordConfirmInput.value = '';
        }

        // Auto Email Fetch
        if (!villageId) return;

        emailInput.placeholder = 'Membuat email kombinasi otomatis...';

        fetch(`/api/get-next-worker-email?village_id=${villageId}&name=${encodeURIComponent(nameValue)}`)
            .then(response => response.json())
            .then(data => {
                if (data.email) {
                    emailInput.value = data.email;
                }
            })
            .catch(error => {
                console.error('Error fetching email:', error);
                emailInput.placeholder = 'Gagal memuat email otomatis';
            });
    }

    if (villageSelect && villageSelect.tagName === 'SELECT') {
        villageSelect.addEventListener('change', generateAutoFields);
    }

    if (nameInput) {
        nameInput.addEventListener('input', function () {
            clearTimeout(window.autoTimer);
            window.autoTimer = setTimeout(generateAutoFields, 300);
        });
    }

    generateAutoFields();
});
</script>
@endpush
@endsection