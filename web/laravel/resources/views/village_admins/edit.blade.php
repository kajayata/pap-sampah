
@extends('layouts.app')

@section('title', 'Edit Akun Admin Kelurahan')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-6 space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center gap-3">
        <a
            href="{{ route('village-admins.index') }}"
            class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-slate-900 transition"
            aria-label="Kembali ke daftar akun"
        >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M10 19l-7-7m0 0l7-7m-7 7h18"
                />
            </svg>
        </a>

        <div>
            <h1 class="text-2xl font-bold font-serif text-slate-900">
                Edit Akun Admin Kelurahan
            </h1>
            <p class="text-xs text-slate-500">
                Perbarui data pengelola akun kelurahan.
            </p>
        </div>
    </div>

    {{-- PESAN ERROR UMUM --}}
    @if($errors->has('form'))
        <div
            role="alert"
            class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm"
            aria-live="assertive"
        >
            {{ $errors->first('form') }}
        </div>
    @endif

    {{-- INFORMASI EMAIL --}}
    <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-sm">
        <h2 class="font-semibold">
            Informasi Email Akun
        </h2>
        <p class="mt-1">
            Email dibuat otomatis oleh sistem berdasarkan kelurahan dan urutan akun.
            Email tidak dapat diubah secara manual.
        </p>
    </div>

    {{-- FORM EDIT --}}
    <form
        id="editVillageAdminForm"
        action="{{ route('village-admins.update', $user) }}"
        method="POST"
        novalidate
        class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-6"
    >
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-xs">

            {{-- DROPDOWN KELURAHAN --}}
            <div class="md:col-span-2">
                <label
                    for="village_id"
                    class="block font-semibold text-slate-700 mb-1"
                >
                    Kelurahan <span class="text-rose-500">*</span>
                </label>

                <select
                    id="village_id"
                    name="village_id"
                    required
                    aria-describedby="village-help error-village_id"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs bg-white text-slate-800 focus:ring-2 focus:ring-emerald-700 focus:outline-none"
                >
                    <option value="">
                        Pilih Kelurahan
                    </option>

                    @foreach($villages as $village)
                        <option
                            value="{{ $village->id }}"
                            data-village-name="{{ $village->name }}"
                            {{ old('village_id', $user->village_id) == $village->id ? 'selected' : '' }}
                        >
                            Kelurahan {{ $village->name }}
                        </option>
                    @endforeach
                </select>

                <p id="village-help" class="mt-1 text-slate-500 text-2xs">
                    Pilih atau ubah kelurahan tempat pengelola bertugas.
                </p>

                <p
                    id="error-village_id"
                    class="mt-1 text-rose-600 text-2xs {{ $errors->has('village_id') ? '' : 'hidden' }}"
                    role="alert"
                    aria-live="polite"
                >@error('village_id'){{ $message }}@enderror</p>
            </div>

            {{-- NAMA PENGELOLA --}}
            <div class="md:col-span-2">
                <label
                    for="name"
                    class="block font-semibold text-slate-700 mb-1"
                >
                    Nama Pengelola <span class="text-rose-500">*</span>
                </label>

                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    maxlength="100"
                    autocomplete="name"
                    required
                    placeholder="Masukkan nama pengelola"
                    aria-describedby="name-help error-name"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-700 focus:outline-none"
                >

                <p
                    id="error-name"
                    class="mt-1 text-rose-600 text-2xs {{ $errors->has('name') ? '' : 'hidden' }}"
                    role="alert"
                    aria-live="polite"
                >@error('name'){{ $message }}@enderror</p>
            </div>

            {{-- EMAIL OTOMATIS --}}
            <div class="md:col-span-2">
                <label
                    for="email"
                    class="block font-semibold text-slate-700 mb-1"
                >
                    Email Akun
                </label>

                <input
                    id="email"
                    type="text"
                    value="{{ $user->email }}"
                    readonly
                    aria-describedby="email-help"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs text-slate-600 cursor-not-allowed"
                >

                <p id="email-help" class="mt-1 text-slate-500 text-2xs">
                    Email akan dibuat atau disesuaikan otomatis oleh sistem ketika perubahan disimpan.
                </p>
            </div>

            {{-- NOMOR TELEPON --}}
            <div class="md:col-span-2">
                <label
                    for="phone"
                    class="block font-semibold text-slate-700 mb-1"
                >
                    Nomor Telepon <span class="text-rose-500">*</span>
                </label>

                <input
                    id="phone"
                    type="text"
                    name="phone"
                    value="{{ old('phone', $user->phone) }}"
                    minlength="12"
                    maxlength="15"
                    inputmode="numeric"
                    autocomplete="tel"
                    required
                    placeholder="Contoh: 081234567890"
                    aria-describedby="phone-help error-phone"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-700 focus:outline-none"
                >

                <p
                    id="error-phone"
                    class="mt-1 text-rose-600 text-2xs {{ $errors->has('phone') ? '' : 'hidden' }}"
                    role="alert"
                    aria-live="polite"
                >@error('phone'){{ $message }}@enderror</p>
            </div>

            {{-- PASSWORD BARU --}}
            <div class="md:col-span-2">
                <label
                    for="password"
                    class="block font-semibold text-slate-700 mb-1"
                >
                    Password Baru
                    <span class="text-slate-500 font-normal">(Opsional)</span>
                </label>

                <div class="relative">
                    <input
                        id="password"
                        type="password"
                        name="password"
                        minlength="8"
                        autocomplete="new-password"
                        placeholder="Kosongkan jika tidak ingin mengganti password"
                        aria-describedby="password-help error-password"
                        class="w-full px-3.5 py-2.5 pr-20 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-700 focus:outline-none"
                    >

                    <button
                        type="button"
                        data-toggle-password="password"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-emerald-800 text-xs font-semibold hover:underline"
                        aria-label="Tampilkan password"
                        aria-controls="password"
                    >
                        Lihat
                    </button>
                </div>

                <p
                    id="error-password"
                    class="mt-1 text-rose-600 text-2xs {{ $errors->has('password') ? '' : 'hidden' }}"
                    role="alert"
                    aria-live="polite"
                >@error('password'){{ $message }}@enderror</p>
            </div>

            {{-- KONFIRMASI PASSWORD --}}
            <div class="md:col-span-2">
                <label
                    for="password_confirmation"
                    class="block font-semibold text-slate-700 mb-1"
                >
                    Konfirmasi Password Baru
                </label>

                <div class="relative">
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        autocomplete="new-password"
                        placeholder="Ulangi password baru"
                        aria-describedby="error-password_confirmation"
                        class="w-full px-3.5 py-2.5 pr-20 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-700 focus:outline-none"
                    >

                    <button
                        type="button"
                        data-toggle-password="password_confirmation"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-emerald-800 text-xs font-semibold hover:underline"
                        aria-label="Tampilkan konfirmasi password"
                        aria-controls="password_confirmation"
                    >
                        Lihat
                    </button>
                </div>

                <p
                    id="error-password_confirmation"
                    class="mt-1 text-rose-600 text-2xs {{ $errors->has('password_confirmation') ? '' : 'hidden' }}"
                    role="alert"
                    aria-live="polite"
                >@error('password_confirmation'){{ $message }}@enderror</p>
            </div>

        </div>

        {{-- TOMBOL --}}
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a
                href="{{ route('village-admins.index') }}"
                class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition"
            >
                Batal
            </a>

            <button
                type="submit"
                class="px-5 py-2.5 rounded-xl text-xs font-semibold bg-emerald-800 hover:bg-emerald-900 text-white transition shadow-xs"
            >
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('editVillageAdminForm');
    const villageSelect = document.getElementById('village_id');
    const villageEmailInfo = document.getElementById('village-email-info');
    const originalVillageId = @json((string) $user->village_id);

    const fieldIds = [
        'village_id',
        'name',
        'phone',
        'password',
        'password_confirmation'
    ];

    function setError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const error = document.getElementById('error-' + fieldId);

        if (!field || !error) return;

        error.textContent = message;
        error.classList.toggle('hidden', !message);

        field.classList.toggle('border-rose-500', Boolean(message));
        field.classList.toggle('border-slate-200', !message);

        if (message) {
            field.setAttribute('aria-invalid', 'true');
        } else {
            field.removeAttribute('aria-invalid');
        }
    }

    function validateField(fieldId) {
        const field = document.getElementById(fieldId);
        const rawValue = field.value;
        const value = rawValue.trim();
        const password = document.getElementById('password').value;
        const confirmation = document.getElementById('password_confirmation').value;

        let message = '';

        switch (fieldId) {
            case 'village_id':
                if (!value) {
                    message = 'Kelurahan wajib dipilih.';
                }
                break;

            case 'name':
                if (!value) {
                    message = 'Nama pengelola wajib diisi.';
                } else if (value.length > 100) {
                    message = 'Nama pengelola maksimal 100 karakter.';
                } else if (!/^[\p{L} ]+$/u.test(value)) {
                    message = 'Nama pengelola hanya boleh berisi huruf dan spasi.';
                }
                break;

            case 'phone':
                if (!value) {
                    message = 'Nomor telepon wajib diisi.';
                } else if (!/^[0-9]+$/.test(rawValue)) {
                    message = 'Nomor telepon hanya boleh berisi angka tanpa spasi.';
                } else if (rawValue.length < 12) {
                    message = 'Nomor telepon minimal 12 digit.';
                } else if (rawValue.length > 15) {
                    message = 'Nomor telepon maksimal 15 digit.';
                }
                break;

            case 'password':
                if (rawValue.length > 0 && rawValue.length < 8) {
                    message = 'Password baru minimal 8 karakter.';
                } else if (rawValue.length > 0 && /\s/.test(rawValue)) {
                    message = 'Password tidak boleh mengandung spasi.';
                }
                break;

            case 'password_confirmation':
                if (password.length > 0 && confirmation.length === 0) {
                    message = 'Konfirmasi password wajib diisi.';
                } else if (confirmation.length > 0 && password.length === 0) {
                    message = 'Isi password baru terlebih dahulu.';
                } else if (password.length > 0 && confirmation !== password) {
                    message = 'Konfirmasi password tidak sama dengan password baru.';
                }
                break;
        }

        setError(fieldId, message);
        return message === '';
    }

    function validateForm() {
        let isValid = true;

        fieldIds.forEach(function (fieldId) {
            if (!validateField(fieldId)) {
                isValid = false;
            }
        });

        return isValid;
    }

    // Validasi seluruh kolom sebelum form dikirim.
    form.addEventListener('submit', function (event) {
        form.dataset.submitted = 'true';

        if (!validateForm()) {
            event.preventDefault();

            const firstInvalid = fieldIds
                .map(function (fieldId) {
                    return document.getElementById(fieldId);
                })
                .find(function (field) {
                    return field.getAttribute('aria-invalid') === 'true';
                });

            if (firstInvalid) {
                firstInvalid.focus();
                firstInvalid.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        }
    });

    // Perbarui pesan ketika pengguna memperbaiki kolom.
    fieldIds.forEach(function (fieldId) {
        const field = document.getElementById(fieldId);

        field.addEventListener('blur', function () {
            validateField(fieldId);
        });

        field.addEventListener('input', function () {
            const error = document.getElementById('error-' + fieldId);

            if (
                form.dataset.submitted === 'true' ||
                !error.classList.contains('hidden')
            ) {
                validateField(fieldId);
            }

            if (fieldId === 'password') {
                const confirmationError =
                    document.getElementById('error-password_confirmation');

                if (
                    form.dataset.submitted === 'true' ||
                    !confirmationError.classList.contains('hidden')
                ) {
                    validateField('password_confirmation');
                }
            }
        });

        field.addEventListener('change', function () {
            const error = document.getElementById('error-' + fieldId);

            if (
                form.dataset.submitted === 'true' ||
                !error.classList.contains('hidden')
            ) {
                validateField(fieldId);
            }
        });
    });

    // Tampilkan atau sembunyikan password.
    document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
        button.addEventListener('click', function () {
            const field = document.getElementById(button.dataset.togglePassword);
            const isPassword = field.type === 'password';

            field.type = isPassword ? 'text' : 'password';
            button.textContent = isPassword ? 'Sembunyikan' : 'Lihat';
            button.setAttribute(
                'aria-label',
                isPassword ? 'Sembunyikan password' : 'Tampilkan password'
            );
        });
    });

    // Tampilkan informasi email sesuai pilihan kelurahan.
    function updateVillageInfo() {
        if (String(villageSelect.value) === originalVillageId) {
            villageEmailInfo.textContent =
                'Kelurahan tidak berubah. Email akun akan mengikuti urutan akun yang sekarang.';
        } else {
            villageEmailInfo.textContent =
                'Kelurahan berubah. Sistem akan membuat email otomatis sesuai kelurahan tujuan dan urutan akun berikutnya.';
        }

        const error = document.getElementById('error-village_id');

        if (
            form.dataset.submitted === 'true' ||
            !error.classList.contains('hidden')
        ) {
            validateField('village_id');
        }
    }

    villageSelect.addEventListener('change', updateVillageInfo);

    updateVillageInfo();
});
</script>
@endsection
