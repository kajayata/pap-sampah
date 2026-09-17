@extends('layouts.app')

@section('title', 'Edit Petugas Kebersihan — Pap Sampah')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-8">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-xs font-medium text-text-muted mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-accent-primary transition-colors">Dashboard</a>
        <span>/</span>
        <a href="{{ route('petugas.index') }}" class="hover:text-accent-primary transition-colors">Petugas Kebersihan</a>
        <span>/</span>
        <span class="text-text-primary">Edit #{{ $worker->id }}</span>
    </div>

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="font-display text-3xl font-bold text-text-primary">Edit Data Petugas</h1>
        <p class="text-text-muted text-sm mt-1">
            Perbarui informasi personel petugas kebersihan <strong>{{ $worker->name }}</strong>.
        </p>
    </div>

    {{-- Form Card --}}
    <div class="bg-surface rounded-3xl border border-border-default p-6 sm:p-8 shadow-sm">
        <form method="POST" action="{{ route('petugas.update', $worker->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

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
                    <input type="text" id="name" name="name" value="{{ old('name', $worker->name) }}" required class="w-full pl-12 pr-4 py-3 bg-base border border-border-default rounded-2xl text-sm text-text-primary focus:outline-none focus:ring-2 focus:ring-accent-primary/20 focus:border-accent-primary transition-all @error('name') border-accent-danger @enderror">
                </div>
                @error('name')
                    <p class="text-xs text-accent-danger mt-1.5">{{ $message }}</p>
                @enderror
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
                    <input type="email" id="email" name="email" value="{{ old('email', $worker->email) }}" required class="w-full pl-12 pr-4 py-3 bg-base border border-border-default rounded-2xl text-sm text-text-primary focus:outline-none focus:ring-2 focus:ring-accent-primary/20 focus:border-accent-primary transition-all @error('email') border-accent-danger @enderror">
                </div>
                @error('email')
                    <p class="text-xs text-accent-danger mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Telepon --}}
            <div>
                <label for="phone" class="block text-sm font-semibold text-text-primary mb-2">Nomor Telepon / WhatsApp</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-text-muted/60">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/>
                        </svg>
                    </span>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $worker->phone) }}" placeholder="08xxxxxxxxxx" class="w-full pl-12 pr-4 py-3 bg-base border border-border-default rounded-2xl text-sm text-text-primary focus:outline-none focus:ring-2 focus:ring-accent-primary/20 focus:border-accent-primary transition-all @error('phone') border-accent-danger @enderror">
                </div>
                @error('phone')
                    <p class="text-xs text-accent-danger mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status Akun --}}
            <div>
                <label class="block text-sm font-semibold text-text-primary mb-2">Status Akun <span class="text-accent-danger">*</span></label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-3 p-3.5 bg-base border border-border-default rounded-2xl cursor-pointer hover:border-accent-primary transition-all">
                        <input type="radio" name="is_active" value="1" {{ old('is_active', $worker->is_active) ? 'checked' : '' }} class="w-4 h-4 text-accent-primary focus:ring-accent-primary">
                        <div>
                            <span class="block text-sm font-medium text-text-primary">Aktif</span>
                            <span class="block text-xs text-text-muted">Bisa menerima tugas</span>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3.5 bg-base border border-border-default rounded-2xl cursor-pointer hover:border-accent-danger transition-all">
                        <input type="radio" name="is_active" value="0" {{ !old('is_active', $worker->is_active) ? 'checked' : '' }} class="w-4 h-4 text-accent-danger focus:ring-accent-danger">
                        <div>
                            <span class="block text-sm font-medium text-text-primary">Nonaktif</span>
                            <span class="block text-xs text-text-muted">Dibekukan sementara</span>
                        </div>
                    </label>
                </div>
                @error('is_active')
                    <p class="text-xs text-accent-danger mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Wilayah Kelurahan --}}
            @if(Auth::user()->hasRole('super_admin_kecamatan'))
            <div>
                <label for="village_id" class="block text-sm font-semibold text-text-primary mb-2">Wilayah Kelurahan Operasional <span class="text-accent-danger">*</span></label>
                <select id="village_id" name="village_id" required class="w-full px-4 py-3 bg-base border border-border-default rounded-2xl text-sm text-text-primary focus:outline-none focus:ring-2 focus:ring-accent-primary/20 focus:border-accent-primary transition-all @error('village_id') border-accent-danger @enderror">
                    @foreach($villages as $vil)
                        <option value="{{ $vil->id }}" {{ old('village_id', $worker->village_id) == $vil->id ? 'selected' : '' }}>{{ $vil->name }}</option>
                    @endforeach
                </select>
                @error('village_id')
                    <p class="text-xs text-accent-danger mt-1.5">{{ $message }}</p>
                @enderror
            </div>
            @else
            <div>
                <label class="block text-sm font-semibold text-text-primary mb-2">Wilayah Kelurahan</label>
                <div class="px-4 py-3 bg-base border border-border-default rounded-2xl text-sm text-text-primary flex items-center justify-between">
                    <span class="font-medium">{{ $worker->village?->name }}</span>
                    <span class="text-xs text-text-muted bg-surface px-2.5 py-1 rounded-lg border border-border-default">Terkunci</span>
                </div>
            </div>
            @endif

            {{-- Reset Kata Sandi (Opsional) --}}
            <div class="pt-6 border-t border-border-default">
                <div class="mb-4">
                    <h3 class="text-sm font-bold text-text-primary">Ganti Kata Sandi (Opsional)</h3>
                    <p class="text-xs text-text-muted">Biarkan kosong jika tidak ingin mengubah kata sandi login petugas.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-medium text-text-muted mb-1.5">Kata Sandi Baru</label>
                        <input type="password" id="password" name="password" placeholder="Minimal 8 karakter" class="w-full px-4 py-2.5 bg-base border border-border-default rounded-2xl text-sm text-text-primary focus:outline-none focus:ring-2 focus:ring-accent-primary/20 focus:border-accent-primary transition-all @error('password') border-accent-danger @enderror">
                        @error('password')
                            <p class="text-xs text-accent-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-medium text-text-muted mb-1.5">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi kata sandi baru" class="w-full px-4 py-2.5 bg-base border border-border-default rounded-2xl text-sm text-text-primary focus:outline-none focus:ring-2 focus:ring-accent-primary/20 focus:border-accent-primary transition-all">
                    </div>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-border-default">
                <a href="{{ route('petugas.index') }}" class="px-5 py-2.5 bg-base hover:bg-border-default/10 border border-border-default text-text-primary font-medium text-sm rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-accent-primary text-white font-medium text-sm rounded-xl hover:-translate-y-0.5 shadow-sm hover:shadow transition-all duration-200">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
