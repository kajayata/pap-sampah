@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">

        <div class="mb-8">
            <h1 class="text-2xl font-bold text-text-primary">
                Edit Akun Admin Kelurahan
            </h1>

            <p class="text-sm text-text-muted mt-1">
                Perbarui data akun administrator kelurahan.
            </p>
        </div>

        @if($errors->has('form'))
            <div class="mb-5 rounded-xl bg-red-100 px-4 py-3 text-red-800">
                {{ $errors->first('form') }}
            </div>
        @endif

        <form
            action="{{ route('village-admins.update', $user) }}"
            method="POST"
            class="space-y-5"
        >
            @csrf
            @method('PUT')

            {{-- Kelurahan --}}
            <div>
                <label class="block mb-2 font-medium">
                    Kelurahan
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
                            @if(!$village->is_active)
                                (Nonaktif)
                            @endif
                        </option>
                    @endforeach
                </select>

                @error('village_id')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Nama --}}
            <div>
                <label class="block mb-2 font-medium">
                    Nama
                </label>

                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    maxlength="30"
                    class="w-full rounded-xl border border-border px-4 py-3"
                    placeholder="Admin Kelurahan"
                    required
                >

                @error('name')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block mb-2 font-medium">
                    Email
                </label>

                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    maxlength="30"
                    class="w-full rounded-xl border border-border px-4 py-3"
                    placeholder="kel.sumbersari@papsampah.id"
                    required
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

            {{-- Nomor Telepon --}}
            <div>
                <label class="block mb-2 font-medium">
                    Nomor Telepon
                </label>

                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone', $user->phone) }}"
                    maxlength="15"
                    inputmode="numeric"
                    class="w-full rounded-xl border border-border px-4 py-3"
                    placeholder="Contoh: 081234567890"
                    required
                >

                @error('phone')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Password Baru --}}
            <div>
                <label class="block mb-2 font-medium">
                    Password Baru
                </label>

                <input
                    type="password"
                    name="password"
                    minlength="8"
                    class="w-full rounded-xl border border-border px-4 py-3"
                    placeholder="Kosongkan jika tidak ingin mengubah password"
                >

                @error('password')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Konfirmasi Password --}}
            <div>
                <label class="block mb-2 font-medium">
                    Konfirmasi Password Baru
                </label>

                <input
                    type="password"
                    name="password_confirmation"
                    minlength="8"
                    class="w-full rounded-xl border border-border px-4 py-3"
                    placeholder="Ulangi password baru"
                >

                @error('password_confirmation')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Tombol --}}
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
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const villageSelect = document.getElementById('village_id');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');

    function updatePlaceholder() {

        const selectedOption =
            villageSelect.options[villageSelect.selectedIndex];

        const villageName =
            selectedOption?.dataset.villageName || '';

        if (!villageName) {
            nameInput.placeholder =
                'Contoh: Admin Kelurahan Sumbersari';

            emailInput.placeholder =
                'Contoh: kel.sumbersari@papsampah.id';

            return;
        }

        const emailVillageName = villageName
            .toLowerCase()
            .replace(/\s+/g, '');

        nameInput.placeholder =
            'Admin Kelurahan ' + villageName;

        emailInput.placeholder =
            'kel.' + emailVillageName + '@papsampah.id';
    }

    villageSelect.addEventListener(
        'change',
        updatePlaceholder
    );

    updatePlaceholder();

});
</script>
@endsection
