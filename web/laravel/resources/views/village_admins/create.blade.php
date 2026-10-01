@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-text-primary">
                Tambah Akun Admin Kelurahan
            </h1>

            <p class="text-sm text-text-muted mt-1">
                Tambahkan akun administrator untuk kelurahan.
            </p>
        </div>

        {{-- Error semua data kosong --}}
        @if($errors->has('form'))
            <div class="mb-5 rounded-xl bg-red-100 px-4 py-3 text-red-800">
                {{ $errors->first('form') }}
            </div>
        @endif

        <form
            action="{{ route('village-admins.store') }}"
            method="POST"
            class="space-y-5"
        >
            @csrf

            {{-- ===================================================== --}}
            {{-- KELURAHAN --}}
            {{-- ===================================================== --}}
            <div>
                <label
                    for="village_id"
                    class="block mb-2 font-medium"
                >
                    Kelurahan
                </label>

                <select
                    id="village_id"
                    name="village_id"
                    class="w-full rounded-xl border border-border px-4 py-3"
                >
                    <option value="">
                        -- Pilih Kelurahan --
                    </option>

                    @foreach($villages as $village)
                        <option
                            value="{{ $village->id }}"
                            data-village-name="{{ $village->name }}"
                            {{ old('village_id') == $village->id ? 'selected' : '' }}
                        >
                            {{ $village->name }}
                        </option>
                    @endforeach
                </select>

                @error('village_id')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            {{-- ===================================================== --}}
            {{-- NAMA --}}
            {{-- ===================================================== --}}
            <div>
                <label
                    for="name"
                    class="block mb-2 font-medium"
                >
                    Nama
                </label>

                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    maxlength="30"
                    class="w-full rounded-xl border border-border px-4 py-3"
                    placeholder="Contoh: Admin Kelurahan Sumbersari"
                >

                <p class="mt-1 text-xs text-text-muted">
                    Hanya boleh menggunakan huruf dan spasi, maksimal 30 karakter.
                </p>

                @error('name')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            {{-- ===================================================== --}}
            {{-- EMAIL --}}
            {{-- ===================================================== --}}
            <div>
                <label
                    for="email"
                    class="block mb-2 font-medium"
                >
                    Email
                </label>

                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    maxlength="30"
                    class="w-full rounded-xl border border-border px-4 py-3"
                    placeholder="Contoh: kel.sumbersari@papsampah.id"
                >

                <p class="mt-1 text-xs text-text-muted">
                    Format email harus mengikuti kelurahan yang dipilih.
                </p>

                @error('email')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            {{-- ===================================================== --}}
            {{-- NOMOR TELEPON --}}
            {{-- ===================================================== --}}
            <div>
                <label
                    for="phone"
                    class="block mb-2 font-medium"
                >
                    Nomor Telepon
                </label>

                <input
                    id="phone"
                    type="text"
                    name="phone"
                    value="{{ old('phone') }}"
                    maxlength="15"
                    inputmode="numeric"
                    class="w-full rounded-xl border border-border px-4 py-3"
                    placeholder="Contoh: 081234567890"
                >

                <p class="mt-1 text-xs text-text-muted">
                    Hanya boleh menggunakan angka, maksimal 15 digit.
                </p>

                @error('phone')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            {{-- ===================================================== --}}
            {{-- PASSWORD --}}
            {{-- ===================================================== --}}
            <div>
                <label
                    for="password"
                    class="block mb-2 font-medium"
                >
                    Password
                </label>

                <div class="relative">
                    <input
                        id="password"
                        type="password"
                        name="password"
                        minlength="8"
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


            {{-- ===================================================== --}}
            {{-- KONFIRMASI PASSWORD --}}
            {{-- ===================================================== --}}
            <div>
                <label
                    for="password_confirmation"
                    class="block mb-2 font-medium"
                >
                    Konfirmasi Password
                </label>

                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    minlength="8"
                    class="w-full rounded-xl border border-border px-4 py-3"
                    placeholder="Ulangi password"
                >

                @error('password_confirmation')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            {{-- ===================================================== --}}
            {{-- TOMBOL --}}
            {{-- ===================================================== --}}
            <div class="flex gap-3 pt-2">

                <a
                    href="{{ route('village-admins.index') }}"
                    class="rounded-xl border border-border px-4 py-3"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="rounded-xl bg-accent-primary px-4 py-3 text-text-inverse hover:opacity-90"
                >
                    Simpan
                </button>

            </div>

        </form>
    </div>
</div>


{{-- ========================================================= --}}
{{-- JAVASCRIPT --}}
{{-- ========================================================= --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Elemen form
    |--------------------------------------------------------------------------
    */
    const villageSelect = document.getElementById('village_id');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');

    /*
    |--------------------------------------------------------------------------
    | Mengubah placeholder berdasarkan kelurahan
    |--------------------------------------------------------------------------
    */
    function updatePlaceholder() {

        const selectedOption =
            villageSelect.options[villageSelect.selectedIndex];

        const villageName =
            selectedOption?.dataset.villageName || '';

        /*
        |--------------------------------------------------------------------------
        | Jika belum memilih kelurahan
        |--------------------------------------------------------------------------
        */
        if (!villageName) {

            nameInput.placeholder =
                'Contoh: Admin Kelurahan Sumbersari';

            emailInput.placeholder =
                'Contoh: kel.sumbersari@papsampah.id';

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Format nama email
        |
        | Contoh:
        | Sumbersari
        | menjadi:
        | sumbersari
        |--------------------------------------------------------------------------
        */
        const emailVillageName = villageName
            .toLowerCase()
            .replace(/\s+/g, '');

        /*
        |--------------------------------------------------------------------------
        | Placeholder Nama
        |--------------------------------------------------------------------------
        */
        nameInput.placeholder =
            'Admin Kelurahan ' + villageName;

        /*
        |--------------------------------------------------------------------------
        | Placeholder Email
        |--------------------------------------------------------------------------
        */
        emailInput.placeholder =
            'kel.' +
            emailVillageName +
            '@papsampah.id';
    }

    /*
    |--------------------------------------------------------------------------
    | Jalankan ketika kelurahan berubah
    |--------------------------------------------------------------------------
    */
    villageSelect.addEventListener(
        'change',
        updatePlaceholder
    );

    /*
    |--------------------------------------------------------------------------
    | Jalankan saat halaman pertama kali dibuka
    |--------------------------------------------------------------------------
    */
    updatePlaceholder();


    /*
    |--------------------------------------------------------------------------
    | Fitur Lihat / Sembunyikan Password
    |--------------------------------------------------------------------------
    */
    const passwordInput =
        document.getElementById('password');

    const togglePassword =
        document.getElementById('toggle-password');

    togglePassword.addEventListener('click', function () {

        /*
        |--------------------------------------------------------------------------
        | Jika password sedang tersembunyi
        |--------------------------------------------------------------------------
        */
        if (passwordInput.type === 'password') {

            passwordInput.type = 'text';

            togglePassword.textContent =
                'Sembunyikan';

        }

        /*
        |--------------------------------------------------------------------------
        | Jika password sedang terlihat
        |--------------------------------------------------------------------------
        */
        else {

            passwordInput.type = 'password';

            togglePassword.textContent =
                'Lihat';

        }

    });

});
</script>

@endsection
