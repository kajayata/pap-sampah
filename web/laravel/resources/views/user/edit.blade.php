@extends('layouts.app')

@section('title', 'Edit Data Warga — Pap Sampah')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-8">

    {{-- Header & Back Button --}}
    <div class="mb-8">
        <a href="{{ route('user.index') }}" class="inline-flex items-center gap-2 text-sm text-text-muted hover:text-text-primary transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"/>
                <polyline points="12 19 5 12 12 5"/>
            </svg>
            <span>Kembali ke Data Masyarakat</span>
        </a>
        <h1 class="font-display text-3xl font-bold text-text-primary">Edit Data Warga</h1>
        <p class="text-text-muted text-sm mt-1">Perbarui informasi akun masyarakat untuk <strong>{{ $user->name }}</strong></p>
    </div>

    {{-- Form Card --}}
    <div class="bg-surface rounded-2xl border border-border-default p-6 sm:p-8">
        <form method="POST" action="{{ route('user.update', $user->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Nama --}}
            <div>
                <label for="name" class="block text-sm font-medium text-text-primary mb-2">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-2.5 bg-base border @error('name') border-accent-danger @else border-border-default @enderror rounded-xl text-sm text-text-primary focus:outline-none focus:ring-2 focus:ring-accent-primary/20 focus:border-accent-primary transition-all">
                @error('name')
                    <p class="text-xs text-accent-danger mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-text-primary mb-2">Alamat Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2.5 bg-base border @error('email') border-accent-danger @else border-border-default @enderror rounded-xl text-sm text-text-primary focus:outline-none focus:ring-2 focus:ring-accent-primary/20 focus:border-accent-primary transition-all">
                @error('email')
                    <p class="text-xs text-accent-danger mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nomor Telepon --}}
            <div>
                <label for="phone" class="block text-sm font-medium text-text-primary mb-2">Nomor Telepon</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-4 py-2.5 bg-base border @error('phone') border-accent-danger @else border-border-default @enderror rounded-xl text-sm text-text-primary focus:outline-none focus:ring-2 focus:ring-accent-primary/20 focus:border-accent-primary transition-all" placeholder="Contoh: 081234567890">
                @error('phone')
                    <p class="text-xs text-accent-danger mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status Akun (Aktif / Terkunci) --}}
            <div>
                <label for="is_active" class="block text-sm font-medium text-text-primary mb-2">Status Akun</label>
                <select name="is_active" id="is_active" class="w-full px-4 py-2.5 bg-base border border-border-default rounded-xl text-sm text-text-primary focus:outline-none focus:ring-2 focus:ring-accent-primary/20 focus:border-accent-primary transition-all">
                    <option value="1" {{ old('is_active', $user->is_active ?? true) ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ !old('is_active', $user->is_active ?? true) ? 'selected' : '' }}>Terkunci / Nonaktif</option>
                </select>
            </div>

            {{-- Password Baru (Opsional) --}}
            <div class="pt-4 border-t border-border-default">
                <h3 class="text-sm font-semibold text-text-primary mb-1">Ubah Password</h3>
                <p class="text-xs text-text-muted mb-4">Kosongkan jika tidak ingin mengubah password akun ini.</p>

                <div class="space-y-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-text-primary mb-2">Password Baru</label>
                        <input type="password" name="password" id="password" class="w-full px-4 py-2.5 bg-base border @error('password') border-accent-danger @else border-border-default @enderror rounded-xl text-sm text-text-primary focus:outline-none focus:ring-2 focus:ring-accent-primary/20 focus:border-accent-primary transition-all">
                        @error('password')
                            <p class="text-xs text-accent-danger mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-text-primary mb-2">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="w-full px-4 py-2.5 bg-base border border-border-default rounded-xl text-sm text-text-primary focus:outline-none focus:ring-2 focus:ring-accent-primary/20 focus:border-accent-primary transition-all">
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-border-default">
                <a href="{{ route('user.index') }}" class="px-5 py-2.5 bg-base border border-border-default text-text-primary rounded-xl text-sm font-medium hover:bg-border-default/10 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-accent-primary text-white rounded-xl text-sm font-medium hover:-translate-y-0.5 shadow-sm hover:shadow transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection