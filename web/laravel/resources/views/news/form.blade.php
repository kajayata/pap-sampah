@extends('layouts.app')

@section('title', ($article->exists ? 'Edit' : 'Tulis') . ' Artikel Berita - Pap Sampah')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-6 space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center gap-3">

        <a href="{{ route('news.index') }}"
           class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-slate-900 transition">
            <svg class="w-5 h-5"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M10 19l-7-7m0 0l7-7m-7 7h18" />
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

    {{-- PETUNJUK HAK AKSES --}}
    @if(auth()->user()->isSuperAdmin())

        <div class="rounded-2xl border border-indigo-200 bg-indigo-50 px-4 py-3">
            <div class="flex items-start gap-3">

                <svg class="w-5 h-5 text-indigo-600 shrink-0 mt-0.5"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M13 16h-1v-4h-1m1-8h.01M12 22a10 10 0 100-20 10 10 0 000-20z" />
                </svg>

                <div>
                    <p class="text-xs font-semibold text-indigo-900">
                        Super Admin Kecamatan
                    </p>

                    <p class="text-2xs text-indigo-700 mt-0.5">
                        Anda dapat membuat dan mengelola status Published, Draft, dan Archived.
                    </p>
                </div>

            </div>
        </div>

    @else

        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3">
            <div class="flex items-start gap-3">

                <svg class="w-5 h-5 text-emerald-700 shrink-0 mt-0.5"
                     fill="none"
                     viewBox="0 0 24 24">
                    <path stroke="currentColor"
                          stroke-width="2"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M13 16h-1v-4h-1m1-8h.01M12 22a10 10 0 100-20 10 10 0 000-20z" />
                </svg>

                <div>
                    <p class="text-xs font-semibold text-emerald-900">
                        Admin Kelurahan
                    </p>

                    <p class="text-2xs text-emerald-700 mt-0.5">
                        Berita yang dibuat dan dikelola akan berstatus Published.
                    </p>
                </div>

            </div>
        </div>

    @endif

    {{-- FORM --}}
    <form method="POST"
          action="{{ $article->exists
              ? route('news.update', $article->id)
              : route('news.store') }}"
          enctype="multipart/form-data"
          class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-6">

        @csrf

        @if($article->exists)
            @method('PUT')
        @endif

        <div class="space-y-6 text-xs">

            {{-- JUDUL ARTIKEL --}}
            <div>

                <label for="title"
                       class="block font-semibold text-slate-700 mb-1">
                    Judul Artikel
                    <span class="text-rose-500">*</span>
                </label>

                <input type="text"
                       name="title"
                       id="title"
                       value="{{ old('title', $article->title) }}"
                       minlength="5"
                       maxlength="150"
                       required
                       placeholder="Contoh: Gerakan Pilah Sampah dari Rumah di Kecamatan Sumbersari"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-700 focus:border-emerald-700 focus:outline-none">

                <div class="flex items-center justify-between mt-1">

                    <p class="text-2xs text-slate-400">
                        Judul harus 5–150 karakter.
                    </p>

                    <span id="title-counter"
                          class="text-2xs text-slate-400">
                        0/150
                    </span>

                </div>

                @error('title')
                    <span class="text-rose-600 text-2xs mt-1 block">
                        {{ $message }}
                    </span>
                @enderror

            </div>

            {{-- SLUG --}}
            <div>

                <label for="slug"
                       class="block font-semibold text-slate-700 mb-1">
                    URL Slug
                    <span class="text-slate-400 font-normal">
                        (Opsional)
                    </span>
                </label>

                <input type="text"
                       name="slug"
                       id="slug"
                       value="{{ old('slug', $article->slug) }}"
                       maxlength="255"
                       placeholder="Kosongkan untuk membuat slug otomatis dari judul"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-emerald-700 focus:border-emerald-700 focus:outline-none">

                <p class="text-2xs text-slate-400 mt-1">
                    Slug akan dinormalisasi otomatis menjadi format URL.
                </p>

                @error('slug')
                    <span class="text-rose-600 text-2xs mt-1 block">
                        {{ $message }}
                    </span>
                @enderror

            </div>

            {{-- STATUS --}}
            <div>

                <label for="status"
                       class="block font-semibold text-slate-700 mb-1">
                    Status Publikasi
                    <span class="text-rose-500">*</span>
                </label>

                @if(auth()->user()->isSuperAdmin())

                    <select name="status"
                            id="status"
                            required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs bg-white text-slate-800 focus:ring-2 focus:ring-emerald-700 focus:border-emerald-700 focus:outline-none">

                        <option value="PUBLISHED"
                            {{ old('status', $article->status) === 'PUBLISHED' ? 'selected' : '' }}>
                            Published (Tayang)
                        </option>

                        <option value="DRAFT"
                            {{ old('status', $article->status) === 'DRAFT' ? 'selected' : '' }}>
                            Draft (Konsep)
                        </option>

                        <option value="ARCHIVED"
                            {{ old('status', $article->status) === 'ARCHIVED' ? 'selected' : '' }}>
                            Archived (Arsip)
                        </option>

                    </select>

                    <div class="mt-2 text-2xs text-slate-500 space-y-1">
                        <p>
                            <span class="font-semibold text-emerald-700">
                                Published:
                            </span>
                            berita tampil kepada user setelah waktu publikasi tercapai.
                        </p>

                        <p>
                            <span class="font-semibold text-amber-700">
                                Draft:
                            </span>
                            hanya dapat dikelola oleh Super Admin.
                        </p>

                        <p>
                            <span class="font-semibold text-slate-700">
                                Archived:
                            </span>
                            hanya dapat dikelola oleh Super Admin.
                        </p>
                    </div>

                @else

                    <input type="hidden"
                           name="status"
                           value="PUBLISHED">

                    <div class="w-full px-3.5 py-2.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 text-xs font-semibold">
                        Published (Tayang)
                    </div>

                    <p class="text-2xs text-slate-400 mt-1">
                        Admin Kelurahan hanya dapat membuat dan mengelola berita Published.
                    </p>

                @endif

                @error('status')
                    <span class="text-rose-600 text-2xs mt-1 block">
                        {{ $message }}
                    </span>
                @enderror

            </div>

            {{-- THUMBNAIL --}}
            <div>

                <label for="thumbnail"
                       class="block font-semibold text-slate-700 mb-1">
                    Foto Sampul / Thumbnail
                    <span class="text-slate-400 font-normal">
                        (JPG, JPEG, PNG, WebP — Maks. 10 MB)
                    </span>
                </label>

                @if($article->thumbnail_url)

                    <div class="mb-3 p-3 rounded-xl border border-slate-200 bg-slate-50">

                        <div class="flex items-center gap-3">

                            <img src="{{ $article->thumbnail_url }}"
                                 alt="Sampul {{ $article->title }}"
                                 class="w-28 h-20 rounded-xl object-cover border border-slate-200">

                            <div>
                                <p class="text-xs font-semibold text-slate-700">
                                    Foto Saat Ini
                                </p>

                                <p class="text-2xs text-slate-500 mt-1">
                                    Pilih file baru untuk mengganti foto.
                                </p>
                            </div>

                        </div>

                    </div>

                @endif

                <input type="file"
                       name="thumbnail"
                       id="thumbnail"
                       accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                       class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-800 hover:file:bg-emerald-100">

                <p class="text-2xs text-slate-400 mt-1">
                    Format yang diperbolehkan: JPG, JPEG, PNG, dan WebP.
                    Ukuran maksimal 10 MB.
                </p>

                @error('thumbnail')
                    <span class="text-rose-600 text-2xs mt-1 block">
                        {{ $message }}
                    </span>
                @enderror

                {{-- PREVIEW FILE BARU --}}
                <div id="thumbnail-preview"
                     class="hidden mt-3">

                    <p class="text-2xs font-semibold text-slate-600 mb-2">
                        Preview:
                    </p>

                    <img id="thumbnail-preview-image"
                         src=""
                         alt="Preview thumbnail"
                         class="w-40 h-28 rounded-xl object-cover border border-slate-200">

                </div>

            </div>

            {{-- ISI ARTIKEL --}}
            <div>

                <label for="content"
                       class="block font-semibold text-slate-700 mb-1">
                    Isi Artikel Lengkap
                    <span class="text-rose-500">*</span>
                </label>

                <textarea name="content"
                          id="content"
                          rows="14"
                          required
                          placeholder="Tuliskan isi artikel edukasi lingkungan di sini..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-700 focus:border-emerald-700 focus:outline-none leading-relaxed">{{ old('content', $article->content) }}</textarea>

                <p class="text-2xs text-slate-400 mt-1">
                    Isi artikel wajib diisi.
                </p>

                @error('content')
                    <span class="text-rose-600 text-2xs mt-1 block">
                        {{ $message }}
                    </span>
                @enderror

            </div>

        </div>

        {{-- BUTTON --}}
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">

            <a href="{{ route('news.index') }}"
               class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                Batal
            </a>

            <button type="submit"
                    class="px-5 py-2.5 rounded-xl text-xs font-semibold bg-emerald-800 hover:bg-emerald-900 text-white transition shadow-xs">

                {{ $article->exists
                    ? 'Simpan Perubahan'
                    : 'Terbitkan Artikel' }}

            </button>

        </div>

    </form>

</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ==========================================
    // COUNTER JUDUL
    // ==========================================

    const titleInput = document.getElementById('title');
    const titleCounter = document.getElementById('title-counter');

    function updateTitleCounter() {
        if (!titleInput || !titleCounter) {
            return;
        }

        const length = titleInput.value.length;

        titleCounter.textContent = `${length}/150`;

        if (length > 150) {
            titleCounter.classList.remove('text-slate-400');
            titleCounter.classList.add('text-rose-600');
        } else if (length < 5) {
            titleCounter.classList.remove(
                'text-slate-400',
                'text-emerald-600'
            );

            titleCounter.classList.add('text-amber-600');
        } else {
            titleCounter.classList.remove(
                'text-slate-400',
                'text-amber-600',
                'text-rose-600'
            );

            titleCounter.classList.add('text-emerald-600');
        }
    }

    if (titleInput) {
        updateTitleCounter();

        titleInput.addEventListener(
            'input',
            updateTitleCounter
        );
    }

    // ==========================================
    // PREVIEW THUMBNAIL
    // ==========================================

    const thumbnailInput =
        document.getElementById('thumbnail');

    const thumbnailPreview =
        document.getElementById('thumbnail-preview');

    const thumbnailPreviewImage =
        document.getElementById('thumbnail-preview-image');

    if (
        thumbnailInput
        && thumbnailPreview
        && thumbnailPreviewImage
    ) {
        thumbnailInput.addEventListener(
            'change',
            function () {

                const file = this.files[0];

                if (!file) {
                    thumbnailPreview.classList.add('hidden');
                    thumbnailPreviewImage.src = '';

                    return;
                }

                const allowedTypes = [
                    'image/jpeg',
                    'image/png',
                    'image/webp',
                ];

                if (!allowedTypes.includes(file.type)) {
                    alert(
                        'Format foto tidak diperbolehkan. Gunakan JPG, JPEG, PNG, atau WebP.'
                    );

                    this.value = '';

                    thumbnailPreview.classList.add('hidden');
                    thumbnailPreviewImage.src = '';

                    return;
                }

                const maxSize =
                    10 * 1024 * 1024;

                if (file.size > maxSize) {
                    alert(
                        'Ukuran foto terlalu besar. Maksimal 10 MB.'
                    );

                    this.value = '';

                    thumbnailPreview.classList.add('hidden');
                    thumbnailPreviewImage.src = '';

                    return;
                }

                const reader =
                    new FileReader();

                reader.onload = function (event) {
                    thumbnailPreviewImage.src =
                        event.target.result;

                    thumbnailPreview.classList.remove(
                        'hidden'
                    );
                };

                reader.readAsDataURL(file);
            }
        );
    }
});
</script>
@endsection
