@extends('layouts.app')

@section('title', 'Tambah Akun Admin Kelurahan')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">

        <div class="mb-8">
            <h1 class="text-2xl font-bold font-serif text-slate-900">
                Tambah Akun Admin Kelurahan
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Tambahkan akun administrator untuk kelurahan.
            </p>
        </div>

        @if($errors->has('form'))
            <div
                class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm"
                role="alert"
            >
                {{ $errors->first('form') }}
            </div>
        @endif

        <div class="mb-5 p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-sm">
            Setiap kelurahan maksimal memiliki 3 akun admin, termasuk akun nonaktif.
            Email akun dibuat otomatis berdasarkan kelurahan dan urutan akun.
        </div>

        @php
            $hasAvailableVillage = $villages->contains(
                fn ($village) => ($accountCounts[$village->id] ?? 0) < 3
            );
        @endphp

        @if(!$hasAvailableVillage)
            <div
                class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm"
                role="alert"
            >
                Semua kelurahan aktif sudah memiliki maksimal 3 akun admin
                atau belum tersedia kelurahan aktif.
            </div>
        @endif

        <form
            id="createVillageAdminForm"
            action="{{ route('village-admins.store') }}"
            method="POST"
            novalidate
            class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-6"
        >
            @csrf

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
                        <option
                            value=""
                            data-account-count="0"
                            data-email-preview=""
                            {{ old('village_id') ? '' : 'selected' }}
                        >
                            Pilih Kelurahan
                        </option>

                        @foreach($villages as $village)
                            @php
                                $jumlahAkun = $accountCounts[$village->id] ?? 0;
                            @endphp

                            <option
                                value="{{ $village->id }}"
                                data-village-name="{{ $village->name }}"
                                data-account-count="{{ $jumlahAkun }}"
                                data-email-preview="{{ $emailPreviews[$village->id] ?? '' }}"
                                {{ old('village_id') == $village->id ? 'selected' : '' }}
                                {{ $jumlahAkun >= 3 ? 'disabled' : '' }}
                            >
                                Kelurahan {{ $village->name }}
                                ({{ $jumlahAkun }}/3 akun{{ $jumlahAkun >= 3 ? ', penuh' : '' }})
                            </option>
                        @endforeach
                    </select>

                    <p id="village-help" class="mt-1 text-slate-500 text-2xs">
                        Pilih kelurahan yang masih memiliki kuota akun.
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
                        value="{{ old('name') }}"
                        maxlength="100"
                        required
                        autocomplete="name"
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
                        value=""
                        readonly
                        aria-describedby="email-help"
                        placeholder="Pilih kelurahan terlebih dahulu"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs text-slate-600 cursor-not-allowed"
                    >

                    <p id="email-help" class="mt-1 text-slate-500 text-2xs">
                        Email dibuat otomatis oleh sistem dan tidak dapat diubah manual.
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
                        value="{{ old('phone') }}"
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

                {{-- PASSWORD --}}
                <div class="md:col-span-2">
                    <label
                        for="password"
                        class="block font-semibold text-slate-700 mb-1"
                    >
                        Password <span class="text-rose-500">*</span>
                    </label>

                    <div class="relative">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            minlength="8"
                            autocomplete="new-password"
                            required
                            placeholder="Minimal 8 karakter"
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
                        Konfirmasi Password <span class="text-rose-500">*</span>
                    </label>

                    <div class="relative">
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            minlength="8"
                            autocomplete="new-password"
                            required
                            placeholder="Ulangi password"
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
                    id="submitButton"
                    type="submit"
                    {{ !$hasAvailableVillage ? 'disabled' : '' }}
                    class="px-5 py-2.5 rounded-xl text-xs font-semibold bg-emerald-800 hover:bg-emerald-900 text-white transition shadow-xs disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Simpan Akun
                </button>
            </div>

        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('createVillageAdminForm');
    const villageSelect = document.getElementById('village_id');
    const emailInput = document.getElementById('email');
    const submitButton = document.getElementById('submitButton');

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
            case 'village_id': {
                const option = villageSelect.options[villageSelect.selectedIndex];

                if (!value) {
                    message = 'Kelurahan wajib dipilih.';
                } else if (
                    option.disabled ||
                    Number(option.dataset.accountCount || 0) >= 3
                ) {
                    message = 'Kelurahan ini sudah memiliki maksimal 3 akun admin.';
                }
                break;
            }

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
                if (!rawValue) {
                    message = 'Password wajib diisi.';
                } else if (rawValue.length < 8) {
                    message = 'Password minimal 8 karakter.';
                } else if (/\s/.test(rawValue)) {
                    message = 'Password tidak boleh mengandung spasi.';
                }
                break;

            case 'password_confirmation':
                if (!confirmation) {
                    message = 'Konfirmasi password wajib diisi.';
                } else if (confirmation !== password) {
                    message = 'Konfirmasi password tidak sama dengan password.';
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
                const confirmError =
                    document.getElementById('error-password_confirmation');

                if (
                    form.dataset.submitted === 'true' ||
                    !confirmError.classList.contains('hidden')
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

    function updateVillageInfo() {
        const option = villageSelect.options[villageSelect.selectedIndex];

        emailInput.value = option?.dataset.emailPreview || '';
        emailInput.placeholder = option?.value
            ? 'Email otomatis untuk Kelurahan ' + (option.dataset.villageName || '')
            : 'Pilih kelurahan terlebih dahulu';

        const villageError = document.getElementById('error-village_id');

        if (
            form.dataset.submitted === 'true' ||
            !villageError.classList.contains('hidden')
        ) {
            validateField('village_id');
        }

        const available = Array.from(villageSelect.options).some(function (item) {
            return item.value !== '' &&
                !item.disabled &&
                Number(item.dataset.accountCount || 0) < 3;
        });

        submitButton.disabled = !available;
    }

    villageSelect.addEventListener('change', updateVillageInfo);

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

    updateVillageInfo();
});
</script>
@endsection
