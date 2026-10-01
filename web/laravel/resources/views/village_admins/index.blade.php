@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-6">

    {{-- =========================================================
        HEADER
    ========================================================== --}}
    <div class="flex items-center justify-between mb-6">

        <div>
            <h1 class="text-2xl font-bold text-text-primary">
                Akun Admin Kelurahan
            </h1>

            <p class="text-sm text-text-muted mt-1">
                Kelola akun administrator untuk setiap kelurahan.
            </p>
        </div>

        {{-- Tombol Tambah Akun --}}
        <a
            href="{{ route('village-admins.create') }}"
            class="px-4 py-2 rounded-xl bg-accent-primary text-text-inverse font-medium hover:opacity-90"
        >
            + Tambah Akun
        </a>

    </div>


    {{-- =========================================================
        PESAN BERHASIL
    ========================================================== --}}
    @if(session('success'))

        <div
            class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-700"
        >
            {{ session('success') }}
        </div>

    @endif


    {{-- =========================================================
        PESAN ERROR SESSION
    ========================================================== --}}
    @if(session('error'))

        <div
            class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700"
        >
            {{ session('error') }}
        </div>

    @endif


    {{-- =========================================================
        PESAN ERROR FORM
    ========================================================== --}}
    @if($errors->has('form'))

        <div
            class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700"
        >
            {{ $errors->first('form') }}
        </div>

    @endif


    {{-- =========================================================
        TABEL AKUN ADMIN KELURAHAN
    ========================================================== --}}
    <div
        class="overflow-hidden rounded-xl border border-border-default bg-surface"
    >

        <div class="overflow-x-auto">

            <table class="min-w-full">

                {{-- =================================================
                    HEADER TABEL
                ================================================== --}}
                <thead class="border-b border-border-default">

                    <tr>

                        <th
                            class="px-6 py-4 text-left text-sm font-semibold"
                        >
                            No
                        </th>

                        <th
                            class="px-6 py-4 text-left text-sm font-semibold"
                        >
                            Nama
                        </th>

                        <th
                            class="px-6 py-4 text-left text-sm font-semibold"
                        >
                            Email
                        </th>

                        <th
                            class="px-6 py-4 text-left text-sm font-semibold"
                        >
                            Kelurahan
                        </th>

                        <th
                            class="px-6 py-4 text-left text-sm font-semibold"
                        >
                            Telepon
                        </th>

                        <th
                            class="px-6 py-4 text-left text-sm font-semibold"
                        >
                            Status
                        </th>

                        <th
                            class="px-6 py-4 text-left text-sm font-semibold"
                        >
                            Aksi
                        </th>

                    </tr>

                </thead>


                {{-- =================================================
                    ISI TABEL
                ================================================== --}}
                <tbody class="divide-y divide-border-default">

                    @forelse($admins as $admin)

                        <tr>

                            {{-- No --}}
                            <td class="px-6 py-4 text-sm">
                                {{ $loop->iteration }}
                            </td>


                            {{-- Nama --}}
                            <td class="px-6 py-4 text-sm font-medium">
                                {{ $admin->name }}
                            </td>


                            {{-- Email --}}
                            <td class="px-6 py-4 text-sm">
                                {{ $admin->email }}
                            </td>


                            {{-- Kelurahan --}}
                            <td class="px-6 py-4 text-sm">
                                {{ $admin->village?->name ?? '-' }}
                            </td>


                            {{-- Telepon --}}
                            <td class="px-6 py-4 text-sm">
                                {{ $admin->phone ?? '-' }}
                            </td>


                            {{-- =================================================
                                STATUS
                            ================================================== --}}
                            <td class="px-6 py-4">

                                @if($admin->is_active)

                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs bg-green-100 text-green-700"
                                    >
                                        Aktif
                                    </span>

                                @else

                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs bg-gray-100 text-gray-600"
                                    >
                                        Nonaktif
                                    </span>

                                @endif

                            </td>


                            {{-- =================================================
                                AKSI
                            ================================================== --}}
                            <td class="px-6 py-4">

                                <div class="flex items-center gap-2">

                                    {{-- =========================================
                                        AKUN AKTIF
                                    ========================================== --}}
                                    @if($admin->is_active)

                                        {{-- Edit --}}
                                        <a
                                            href="{{ route('village-admins.edit', $admin) }}"
                                            class="px-3 py-1.5 rounded-lg border border-border-default text-sm hover:bg-gray-50"
                                        >
                                            Edit
                                        </a>


                                        {{-- Nonaktifkan --}}
                                        <form
                                            action="{{ route('village-admins.toggle-status', $admin) }}"
                                            method="POST"
                                        >

                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="px-3 py-1.5 rounded-lg border border-red-200 text-sm text-red-600 hover:bg-red-50"
                                            >
                                                Nonaktifkan
                                            </button>

                                        </form>


                                    {{-- =========================================
                                        AKUN NONAKTIF
                                    ========================================== --}}
                                    @else

                                        {{-- Tidak dapat diedit --}}
                                        <span
                                            class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-500 text-sm"
                                        >
                                            Tidak dapat diedit
                                        </span>


                                        {{-- Aktifkan --}}
                                        <form
                                            action="{{ route('village-admins.toggle-status', $admin) }}"
                                            method="POST"
                                        >

                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="px-3 py-1.5 rounded-lg border border-green-200 text-sm text-green-600 hover:bg-green-50"
                                            >
                                                Aktifkan
                                            </button>

                                        </form>

                                    @endif

                                </div>

                            </td>

                        </tr>


                    {{-- =================================================
                        JIKA BELUM ADA DATA
                    ================================================== --}}
                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="px-6 py-8 text-center text-text-muted"
                            >
                                Belum ada akun admin kelurahan.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection
