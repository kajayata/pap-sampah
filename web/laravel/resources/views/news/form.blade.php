@extends('layouts.app')

@section('title', ($article->exists ? 'Edit' : 'Tulis') . ' Artikel Berita - Pap Sampah')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-6 space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('news.index') }}"
           class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-slate-900 transition">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold font-serif text-slate-900">
                {{ $article->exists ? 'Edit Artikel Berita' : 'Tulis Artikel Berita Baru' }}
            </h1>
            <p class="text-xs text-slate-500">
                Tulis artikel edukasi pengelolaan lingkungan atau pengumuman resmi kebersihan.
            </p>
        </div>
    </div>

    <form method="POST"
          action="{{ $article->exists ? route('news.update', $article->id) : route('news.store') }}"
          enctype="multipart/form-data"
          class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-6">
        @csrf
        @if($article->exists)
            @method('PUT')
        @endif

        <div class="space-y-5 text-xs">
            <!-- Judul Berita -->
            <div>
                <label for="title" class="block font-semibold text-slate-700 mb-1">
                    Judul Artikel <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       name="title"
                       id="title"
                       value="{{ old('title', $article->title) }}"
                       required
                       placeholder="Contoh: Gerakan Pilah Sampah dari Rumah di Kecamatan Sumbersari"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-700 focus:outline-none">
                @error('title') <span class="text-rose-600 text-2xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Custom Slug & Status Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label for="slug" class="block font-semibold text-slate-700 mb-1">
                        URL Slug (Opsional)
                    </label>
                    <input type="text"
                           name="slug"
                           id="slug"
                           value="{{ old('slug', $article->slug) }}"
                           placeholder="Kosongkan untuk membuat slug otomatis dari judul"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-emerald-700 focus:outline-none">
                    @error('slug') <span class="text-rose-600 text-2xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="status" class="block font-semibold text-slate-700 mb-1">
                        Status Publikasi <span class="text-rose-500">*</span>
                    </label>
                    <select name="status" id="status" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs bg-white text-slate-800 focus:ring-2 focus:ring-emerald-700 focus:outline-none">
                        <option value="PUBLISHED" {{ old('status', $article->status) === 'PUBLISHED' ? 'selected' : '' }}>Published (Tayang)</option>
                        <option value="DRAFT" {{ old('status', $article->status) === 'DRAFT' ? 'selected' : '' }}>Draft (Konsep)</option>
                        <option value="ARCHIVED" {{ old('status', $article->status) === 'ARCHIVED' ? 'selected' : '' }}>Archived (Arsip)</option>
                    </select>
                    @error('status') <span class="text-rose-600 text-2xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Thumbnail Upload -->
            <div>
                <label for="thumbnail" class="block font-semibold text-slate-700 mb-1">
                    Foto Sampul / Thumbnail (Maks 10MB)
                </label>
                @if($article->thumbnail_url)
                    <div class="mb-3 flex items-center gap-3">
                        <img src="{{ $article->thumbnail_url }}" alt="Sampul Saat Ini" class="w-24 h-16 rounded-xl object-cover border border-slate-200">
                        <span class="text-2xs text-slate-500">Unggah file baru jika ingin mengganti sampul di atas.</span>
                    </div>
                @endif
                <input type="file"
                       name="thumbnail"
                       id="thumbnail"
                       accept="image/jpeg,image/png,image/webp"
                       class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-800 hover:file:bg-emerald-100">
                @error('thumbnail') <span class="text-rose-600 text-2xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Konten Artikel -->
            <div>
                <label for="content" class="block font-semibold text-slate-700 mb-1">
                    Isi Artikel Lengkap <span class="text-rose-500">*</span>
                </label>
                <textarea name="content"
                          id="content"
                          rows="12"
                          required
                          placeholder="Tuliskan isi artikel edukasi lingkungan di sini..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-700 focus:outline-none leading-relaxed">{{ old('content', $article->content) }}</textarea>
                @error('content') <span class="text-rose-600 text-2xs mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('news.index') }}"
               class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                Batal
            </a>
            <button type="submit"
                    class="px-5 py-2.5 rounded-xl text-xs font-semibold bg-emerald-800 hover:bg-emerald-900 text-white transition shadow-xs">
                {{ $article->exists ? 'Simpan Perubahan' : 'Terbitkan Artikel' }}
            </button>
        </div>
    </form>
</div>
@endsection
