@extends('layouts.app')

@section('title', 'Edit Akun Admin Kelurahan')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">

        {{-- HEADER --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-text-primary">
                Edit Akun Admin Kelurahan
            </h1>

            <p class="text-sm text-text-muted mt-1">
                Perbarui data pengelola akun kelurahan.
            </p>
        </div>

        {{-- PESAN ERROR UMUM --}}
        @if($errors->has('form'))
            <div class="mb-5 rounded-xl bg-red-100 px-4 py-3 text-red-800">
                {{ $errors->first('form') }}
            </div>
        @endif

        @if($errors->any() && !$errors->has('form'))
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                <p class="font-semibold">
                    Periksa kembali data yang dimasukkan.
                </p>
                <p class="text-sm mt-1">
                    Pastikan semua data sudah sesuai dengan ketentuan.
                </p>
            </div>
        @endif

        {{-- INFORMASI EMAIL --}}
        <div class="mb-5 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-blue-800">
            <p class="font-semibold text-sm">
                Informasi Email Akun
            </p>
            <p class="text-xs mt-1">
                Email dibuat otomatis oleh sistem berdasarkan kelurahan
                dan urutan akun. Email tidak dapat diubah secara manual.
            </p>
        </div>

        {{-- FORM EDIT --}}
        <form
            action="{{ route('village-admins.update', $user) }}"
            method="POST"
            class="space-y-5"
        >
            @csrf
            @method('PUT')

            {{-- KELURAHAN --}}
            <div>
                <label
                    for="village_id"
                    class="block mb-2 font-medium"
                >
                    Kelurahan
                    <span class="text-red-600">*</span>
                </label>

                <select
                    id="village_id"
                    name="village_id"
                    class="w-full rounded-xl border border-border px-4 py-3"
                    required
                >
                    @foreach($villages as $village)
                        <option
                            value="{{ $village->id }}"
                            data-village-name="{{ $village->name }}"
                            {{ old('village_id', $user->village_id) == $village->id ? 'selected' : '' }}
                        >
                            {{ $village->name }}
                        </option>
                    @endforeach
                </select>

                <p
                    id="village-info"
                    class="mt-2 text-xs text-text-muted"
                >
                    Email akan mengikuti kelurahan yang dipilih.
                </p>

                @error('village_id')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- NAMA PENGELOLA --}}
            <div>
                <label
                    for="name"
                    class="block mb-2 font-medium"
                >
                    Nama Pengelola
                    <span class="text-red-600">*</span>
                </label>

                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    maxlength="100"
                    required
                    autocomplete="name"
                    class="w-full rounded-xl border border-border px-4 py-3"
                    placeholder="Masukkan nama pengelola"
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
                    for="email"
                    class="block mb-2 font-medium"
                >
                    Email Akun
                </label>

                <input
                    id="email"
                    type="email"
                    value="{{ $user->email }}"
                    readonly
                    aria-readonly="true"
                    class="w-full rounded-xl border border-border bg-gray-100 text-gray-700 px-4 py-3 cursor-not-allowed"
                >

                <p class="mt-1 text-xs text-text-muted">
                    Email saat ini. Jika kelurahan diganti, sistem akan
                    menentukan email baru berdasarkan urutan akun kelurahan tujuan.
                </p>
            </div>

            {{-- NOMOR TELEPON --}}
            <div>
                <label
                    for="phone"
                    class="block mb-2 font-medium"
                >
                    Nomor Telepon
                    <span class="text-red-600">*</span>
                </label>

                <input
                    id="phone"
                    type="text"
                    name="phone"
                    value="{{ old('phone', $user->phone) }}"
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
                    Hanya angka, minimal 12 dan maksimal 15 digit.
                </p>

                @error('phone')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- PASSWORD BARU --}}
            <div>
                <label
                    for="password"
                    class="block mb-2 font-medium"
                >
                    Password Baru
                    <span class="text-text-muted font-normal">(Opsional)</span>
                </label>

                <div class="relative">
                    <input
                        id="password"
                        type="password"
                        name="password"
                        minlength="8"
                        autocomplete="new-password"
                        class="w-full rounded-xl border border-border px-4 py-3 pr-24"
                        placeholder="Kosongkan jika tidak ingin mengubah password"
                    >

                    <button
                        type="button"
                        id="toggle-password"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-text-muted hover:text-text-primary"
                        aria-label="Tampilkan password baru"
                    >
                        Lihat
                    </button>
                </div>

                <p class="mt-1 text-xs text-text-muted">
                    Minimal 8 karakter dan tidak boleh mengandung spasi.
                    Kosongkan jika password tidak diubah.
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
                    Konfirmasi Password Baru
                </label>

                <div class="relative">
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        minlength="8"
                        autocomplete="new-password"
                        class="w-full rounded-xl border border-border px-4 py-3 pr-24"
                        placeholder="Ulangi password baru"
                    >

                    <button
                        type="button"
                        id="toggle-confirmation"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-text-muted hover:text-text-primary"
                        aria-label="Tampilkan konfirmasi password"
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
                    class="rounded-xl border border-border px-4 py-3 hover:bg-gray-50"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="rounded-xl bg-accent-primary px-4 py-3 text-text-inverse hover:opacity-90"
                >
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const villageSelect = document.getElementById('village_id');
    const villageInfo = document.getElementById('village-info');
    const originalVillageId = @json((string) $user->village_id);

    /*
     * Informasi ketika pilihan kelurahan berubah.
     * Email final selalu dihitung oleh server.
     */
    function updateVillageInfo() {
        const selectedOption =
            villageSelect.options[villageSelect.selectedIndex];

        if (!selectedOption || !selectedOption.value) {
            villageInfo.textContent =
                'Pilih kelurahan untuk menentukan email akun.';
            return;
        }

        if (selectedOption.value === originalVillageId) {
            villageInfo.textContent =
                'Kelurahan tidak berubah. Email akun akan mengikuti urutan akun yang sekarang.';
        } else {
            villageInfo.textContent =
                'Kelurahan berubah menjadi ' +
                selectedOption.dataset.villageName +
                '. Email baru akan dibuat otomatis berdasarkan urutan akun pada kelurahan tujuan.';
        }
    }

    villageSelect.addEventListener('change', updateVillageInfo);
    updateVillageInfo();

    /*
     * Fitur tampilkan / sembunyikan password.
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
                isHidden
                    ? 'Sembunyikan password'
                    : 'Tampilkan password'
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
