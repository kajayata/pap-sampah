
@extends('layouts.app')

@section('title', 'Akun Admin Kelurahan')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-text-primary">
                Akun Admin Kelurahan
            </h1>

            <p class="text-sm text-text-muted mt-1">
                Kelola akun administrator untuk setiap kelurahan.
                Maksimal 3 akun untuk setiap kelurahan, termasuk akun nonaktif.
            </p>
        </div>

        <a
            href="{{ route('village-admins.create') }}"
            class="inline-flex justify-center px-4 py-2 rounded-xl bg-accent-primary text-text-inverse font-medium hover:opacity-90"
        >
            + Tambah Akun
        </a>
    </div>

    {{-- PESAN BERHASIL --}}
    @if(session('success'))
        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- PESAN ERROR --}}
    @if(session('error'))
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->has('form'))
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700">
            {{ $errors->first('form') }}
        </div>
    @endif

    {{-- TABEL AKUN --}}
    <div class="overflow-hidden rounded-xl border border-border-default bg-surface">
        <div class="overflow-x-auto">
            <table class="min-w-full">

                <thead class="border-b border-border-default">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold">
                            No
                        </th>

                        <th class="px-6 py-4 text-left text-sm font-semibold">
                            Nama Pengelola
                        </th>

                        <th class="px-6 py-4 text-left text-sm font-semibold">
                            Email
                        </th>

                        <th class="px-6 py-4 text-left text-sm font-semibold">
                            Kelurahan
                        </th>

                        <th class="px-6 py-4 text-left text-sm font-semibold">
                            Jumlah Akun
                        </th>

                        <th class="px-6 py-4 text-left text-sm font-semibold">
                            Telepon
                        </th>

                        <th class="px-6 py-4 text-left text-sm font-semibold">
                            Status
                        </th>

                        <th class="px-6 py-4 text-left text-sm font-semibold">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-border-default">

                    @forelse($admins as $admin)
                        <tr>
                            {{-- NOMOR --}}
                            <td class="px-6 py-4 text-sm">
                                {{ $loop->iteration }}
                            </td>

                            {{-- NAMA PENGELOLA --}}
                            <td class="px-6 py-4 text-sm font-medium">
                                {{ $admin->name }}
                            </td>

                            {{-- EMAIL --}}
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                {{ $admin->email }}
                            </td>

                            {{-- KELURAHAN --}}
                            <td class="px-6 py-4 text-sm">
                                {{ $admin->village?->name ?? '-' }}
                            </td>

                            {{-- JUMLAH AKUN DI KELURAHAN --}}
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                @php
                                    $jumlahAkun = $accountCounts[$admin->village_id] ?? 0;
                                @endphp

                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                    {{ $jumlahAkun >= 3
                                        ? 'bg-red-100 text-red-700'
                                        : 'bg-blue-100 text-blue-700' }}">
                                    {{ $jumlahAkun }}/3 akun
                                </span>
                            </td>

                            {{-- TELEPON --}}
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                {{ $admin->phone ?? '-' }}
                            </td>

                            {{-- STATUS --}}
                            <td class="px-6 py-4">
                                @if($admin->is_active)
                                    <span class="px-2.5 py-1 rounded-full text-xs bg-green-100 text-green-700">
                                        Aktif
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs bg-gray-100 text-gray-600">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            {{-- AKSI --}}
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap items-center gap-2">

                                    @if($admin->is_active)
                                        {{-- EDIT --}}
                                        <a
                                            href="{{ route('village-admins.edit', $admin) }}"
                                            class="px-3 py-1.5 rounded-lg border border-border-default text-sm hover:bg-gray-50"
                                        >
                                            Edit
                                        </a>

                                        {{-- NONAKTIFKAN --}}
                                        <form
                                            action="{{ route('village-admins.toggle-status', $admin) }}"
                                            method="POST"
                                            onsubmit="return confirm('Yakin ingin menonaktifkan akun ini?')"
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
                                    @else
                                        {{-- AKUN NONAKTIF --}}
                                        <span class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-500 text-sm">
                                            Tidak dapat diedit
                                        </span>

                                        {{-- AKTIFKAN --}}
                                        <form
                                            action="{{ route('village-admins.toggle-status', $admin) }}"
                                            method="POST"
                                            onsubmit="return confirm('Yakin ingin mengaktifkan akun ini?')"
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
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-text-muted">
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
