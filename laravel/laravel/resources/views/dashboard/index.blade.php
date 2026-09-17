@extends('layouts.app')

@section('title', 'Dashboard — Pap Sampah')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-gray-500 text-sm mt-1">Selamat datang, {{ $user->name }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border p-6">
            <div class="text-sm text-gray-500 mb-1">Role</div>
            <div class="text-lg font-semibold text-gray-800">
                {{ ucfirst(str_replace('_', ' ', $user->role->name)) }}
            </div>
        </div>

        <div class="bg-white rounded-xl border p-6">
            <div class="text-sm text-gray-500 mb-1">Email</div>
            <div class="text-lg font-semibold text-gray-800">{{ $user->email }}</div>
        </div>

        <div class="bg-white rounded-xl border p-6">
            <div class="text-sm text-gray-500 mb-1">Desa</div>
            <div class="text-lg font-semibold text-gray-800">
                {{ $user->village?->name ?? '— Kecamatan' }}
            </div>
        </div>
    </div>

    @if($user->hasRole('super_admin_kecamatan'))
        <div class="bg-white rounded-xl border p-6">
            <h2 class="font-semibold text-gray-800 mb-3">Monitoring Kecamatan</h2>
            <p class="text-sm text-gray-500">Dashboard monitoring seluruh desa akan tersedia di sini.</p>
        </div>
    @endif

    @if($user->hasRole('admin_desa'))
        <div class="bg-white rounded-xl border p-6">
            <h2 class="font-semibold text-gray-800 mb-3">Admin Desa</h2>
            <p class="text-sm text-gray-500">Dashboard manajemen laporan desa akan tersedia di sini.</p>
        </div>
    @endif
</div>
@endsection
