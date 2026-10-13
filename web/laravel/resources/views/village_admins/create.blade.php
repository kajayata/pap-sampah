@extends('layouts.app')

@section('title', 'Tambah Akun Admin Kelurahan')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-text-primary">
                Tambah Akun Admin Kelurahan
            </h1>

            <p class="text-sm text-text-muted mt-1">
                Tambahkan akun pengelola kelurahan. Setiap kelurahan
                dapat memiliki maksimal 3 akun admin.
            </p>
        </div>

        {{-- Pesan error umum --}}
        @if($errors->has('form'))
            <div class="mb-5 rounded-xl bg-red-100 px-4 py-3 text-red-800">
                {{ $errors->first('form') }}
            </div>
        @endif

        {{-- Daftar error validasi --}}
        @if($errors->any() && !$errors->has('form'))
            <div class="mb-5 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-red-800">
                <p class="font-semibold mb-1">
                    Periksa kembali data yang dimasukkan.
                </p>
                <p class="text-sm">
                    Beberapa data belum valid. Lihat pesan di bawah kolom terkait.
                </p>
            </div>
        @endif

        @php
            $hasAvailableVillage = false;

            foreach ($villages as $village) {
                if (($accountCounts[$village->id] ?? 0) < 3) {
                    $hasAvailableVillage = true;
                    break;
                }
            }
        @endphp

        {{-- Peringatan kapasitas --}}
        @if(!$hasAvailableVillage)
            <div class="mb-5 rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-amber-800">
                <p class="font-semibold">
                    Semua kelurahan sudah mencapai batas akun.
                </p>
                <p class="text-sm mt-1">
                    Setiap kelurahan maksimal memiliki 3 akun admin.
                    Nonaktifkan akun tidak akan mengurangi hitungan akun.
                </p>
            </div>
        @endif

        <form
            action="{{ route('village-admins.store') }}"
            method="POST"
            class="space-y-5"
        >
            @csrf

            {{-- KELURAHAN --}}
            <div>
                <label
                    for="village_id"
                    class="block mb-2 font-medium"
                >
                    Kelurahan <span class="text-red-600">*</span>
                </label>

                <select
                    id="village_id"
                    name="village_id"
                    required
                    class="w-full rounded-xl border border-border px-4 py-3"
                >
                    <option value="">-- Pilih Kelurahan --</option>

                    @foreach($villages as $village)
                        @php
                            $count = $accountCounts[$village->id] ?? 0;
                            $isFull = $count >= 3;
                        @endphp

                        <option
                            value="{{ $village->id }}"
                            data-village-name="{{ $village->name }}"
                            data-account-count="{{ $count }}"
                            data-email-preview="{{ $emailPreviews[$village->id] ?? '' }}"
                            {{ old('village_id') == $village->id && !$isFull ? 'selected' : '' }}
                            {{ $isFull ? 'disabled' : '' }}
                        >
                            {{ $village->name }}
                            ({{ $count }}/3 akun{{ $isFull ? ' - Maksimum' : '' }})
                        </option>
                    @endforeach
                </select>

                <p class="mt-1 text-xs text-text-muted">
                    Kelurahan dengan 3 akun sudah penuh dan tidak dapat dipilih.
                </p>

                @error('village_id')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror

                <div
                    id="account-count-info"
                    class="hidden mt-2 rounded-lg bg-emerald-50 px-3 py-2 text-sm text-emerald-800"
                ></div>
            </div>

            {{-- NAMA PENGELOLA --}}
            <div>
                <label
                    for="name"
                    class="block mb-2 font-medium"
                >
                    Nama Pengelola <span class="text-red-600">*</span>
                </label>

                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    maxlength="100"
                    minlength="1"
                    required
                    autocomplete="name"
                    class="w-full rounded-xl border border-border px-4 py-3"
                    placeholder="Masukkan nama pengelola akun"
                >

                <p class="mt-1 text-xs text-text-muted">
                    Masukkan nama orang yang menggunakan atau mengelola akun.
                    Maksimal 100 karakter, hanya huruf dan spasi.
                </p>

                @error('name')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- EMAIL OTOMATIS --}}
            <div>
                <label
                    for="email_preview"
                    class="block mb-2 font-medium"
                >
                    Email Akun Otomatis
                </label>

                <input
                    id="email_preview"
                    type="email"
                    readonly
                    value=""
                    class="w-full rounded-xl border border-border bg-gray-100 text-gray-700 px-4 py-3"
                    placeholder="Pilih kelurahan untuk melihat email otomatis"
                    aria-describedby="email-info"
                >

                <p
                    id="email-info"
                    class="mt-1 text-xs text-text-muted"
                >
                    Email dibuat otomatis oleh sistem berdasarkan kelurahan
                    dan urutan akun. Email tidak perlu diketik manual.
                </p>
            </div>

            {{-- NOMOR TELEPON --}}
            <div>
                <label
                    for="phone"
                    class="block mb-2 font-medium"
                >
                    Nomor Telepon <span class="text-red-600">*</span>
                </label>

                <input
                    id="phone"
                    type="text"
                    name="phone"
                    value="{{ old('phone') }}"
                    minlength="12"
                    maxlength="15"
                    inputmode="numeric"
                    pattern="[0-9]{12,15}"
                    autocomplete="tel"
                    required
                    class="w-full rounded-xl border border-border px-4 py-3"
                    placeholder="Contoh: 081234567890"
                >

                <p class="mt-1 text-xs text-text-muted">
                    Nomor telepon hanya boleh berisi angka, minimal 12
                    dan maksimal 15 digit.
                </p>

                @error('phone')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- PASSWORD --}}
            <div>
                <label
                    for="password"
                    class="block mb-2 font-medium"
                >
                    Password <span class="text-red-600">*</span>
                </label>

                <div class="relative">
                    <input
                        id="password"
                        type="password"
                        name="password"
                        minlength="8"
                        autocomplete="new-password"
                        required
                        class="w-full rounded-xl border border-border px-4 py-3 pr-24"
                        placeholder="Minimal 8 karakter"
                    >

                    <button
                        type="button"
                        id="toggle-password"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-text-muted hover:text-text-primary"
                    >
                        Lihat
                    </button>
                </div>

                <p class="mt-1 text-xs text-text-muted">
                    Minimal 8 karakter dan tidak boleh mengandung spasi.
                </p>

                @error('password')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- KONFIRMASI PASSWORD --}}
            <div>
                <label
                    for="password_confirmation"
                    class="block mb-2 font-medium"
                >
                    Konfirmasi Password <span class="text-red-600">*</span>
                </label>

                <div class="relative">
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        minlength="8"
                        autocomplete="new-password"
                        required
                        class="w-full rounded-xl border border-border px-4 py-3 pr-24"
                        placeholder="Ulangi password"
                    >

                    <button
                        type="button"
                        id="toggle-confirmation"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-text-muted hover:text-text-primary"
                    >
                        Lihat
                    </button>
                </div>

                @error('password_confirmation')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- TOMBOL --}}
            <div class="flex gap-3 pt-2">
                <a
                    href="{{ route('village-admins.index') }}"
                    class="rounded-xl border border-border px-4 py-3"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    {{ !$hasAvailableVillage ? 'disabled' : '' }}
                    class="rounded-xl bg-accent-primary px-4 py-3 text-text-inverse hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Simpan Akun
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const villageSelect = document.getElementById('village_id');
    const nameInput = document.getElementById('name');
    const emailPreview = document.getElementById('email_preview');
    const accountCountInfo = document.getElementById('account-count-info');

    /*
     * Perbarui email dan jumlah akun ketika kelurahan dipilih.
     * Email hanya pratinjau; server tetap menghasilkan email final.
     */
    function updateVillageInfo() {
        const option = villageSelect.options[villageSelect.selectedIndex];

        if (!option || !option.value) {
            emailPreview.value = '';
            emailPreview.placeholder =
                'Pilih kelurahan untuk melihat email otomatis';

            accountCountInfo.textContent = '';
            accountCountInfo.classList.add('hidden');

            nameInput.placeholder = 'Masukkan nama pengelola akun';
            return;
        }

        const villageName = option.dataset.villageName || '';
        const count = Number(option.dataset.accountCount || 0);
        const email = option.dataset.emailPreview || '';

        emailPreview.value = email;
        nameInput.placeholder = 'Nama pengelola ' + villageName;

        accountCountInfo.textContent =
            'Kelurahan ' + villageName + ' memiliki ' +
            count + ' dari maksimal 3 akun admin.';

        accountCountInfo.classList.remove('hidden');

        if (count >= 3) {
            emailPreview.value = '';
            emailPreview.placeholder = 'Kapasitas akun kelurahan penuh';

            accountCountInfo.classList.remove(
                'bg-emerald-50',
                'text-emerald-800'
            );

            accountCountInfo.classList.add(
                'bg-red-50',
                'text-red-800'
            );
        } else {
            accountCountInfo.classList.remove(
                'bg-red-50',
                'text-red-800'
            );

            accountCountInfo.classList.add(
                'bg-emerald-50',
                'text-emerald-800'
            );
        }
    }

    villageSelect.addEventListener('change', updateVillageInfo);
    updateVillageInfo();

    /*
     * Tampilkan / sembunyikan password.
     */
    function setupPasswordToggle(inputId, buttonId) {
        const input = document.getElementById(inputId);
        const button = document.getElementById(buttonId);

        button.addEventListener('click', function () {
            const isHidden = input.type === 'password';

            input.type = isHidden ? 'text' : 'password';
            button.textContent = isHidden ? 'Sembunyikan' : 'Lihat';
            button.setAttribute(
                'aria-label',
                isHidden ? 'Sembunyikan password' : 'Tampilkan password'
            );
        });
    }

    setupPasswordToggle('password', 'toggle-password');
    setupPasswordToggle(
        'password_confirmation',
        'toggle-confirmation'
    );
});
</script>
@endsection
