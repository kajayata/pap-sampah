<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Services\ImageStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function __construct(
        protected ImageStorageService $imageStorageService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        abort_unless($user, 403);

        $isSuperAdmin = $user->isSuperAdmin();
        $isVillageAdmin = $user->isVillageAdmin();

        $query = News::with([
            'author.role',
            'author.village',
        ]);

        /*
         * Super Admin dapat melihat semua status.
         *
         * Admin Kelurahan, Petugas, dan User/Masyarakat
         * hanya dapat membaca berita Published yang
         * waktu tayangnya sudah tercapai.
         */
        if (!$isSuperAdmin) {
            $query
                ->where('status', News::STATUS_PUBLISHED)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now());
        }

        /*
         * Filter status hanya untuk Super Admin.
         */
        if ($isSuperAdmin && $request->filled('status')) {
            $status = $request->input('status');

            if (in_array($status, [
                News::STATUS_DRAFT,
                News::STATUS_PUBLISHED,
                News::STATUS_ARCHIVED,
            ], true)) {
                $query->where('status', $status);
            }
        }

        /*
         * Pencarian berdasarkan judul atau isi.
         */
        $search = Str::squish(
            $request->input('search', '')
        );

        $search = Str::limit(
            $search,
            100,
            ''
        );

        if ($search !== '') {
            $like = '%' . $search . '%';

            $query->where(function ($q) use ($like) {
                $q->where('title', 'ilike', $like)
                    ->orWhere('content', 'ilike', $like);
            });
        }

        $news = $query
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('news.index', compact('news'));
    }

    /**
     * Hanya Super Admin dan Admin Kelurahan
     * yang boleh membuka form tambah.
     */
    public function create(Request $request): View
    {
        $user = $request->user();

        abort_unless($user, 403);

        abort_unless(
            $user->isSuperAdmin() || $user->isVillageAdmin(),
            403,
            'Anda tidak memiliki izin untuk membuat berita.'
        );

        $article = new News([
            'status' => News::STATUS_PUBLISHED,
        ]);

        return view('news.form', compact('article'));
    }

    /**
     * Hanya Super Admin dan Admin Kelurahan
     * yang boleh menyimpan berita.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user, 403);

        $isSuperAdmin = $user->isSuperAdmin();
        $isVillageAdmin = $user->isVillageAdmin();

        abort_unless(
            $isSuperAdmin || $isVillageAdmin,
            403,
            'Anda tidak memiliki izin untuk membuat berita.'
        );

        $title = Str::squish(
            strip_tags($request->input('title', ''))
        );

        $slugInput = $request->filled('slug')
            ? Str::slug($request->input('slug'))
            : null;

        $request->merge([
            'title' => $title,
            'slug' => $slugInput,
        ]);

        $validated = $request->validate(
            [
                'title' => [
                    'required',
                    'string',
                    'min:5',
                    'max:150',
                    'regex:/[\pL\pN]/u',
                ],

                'slug' => [
                    'nullable',
                    'string',
                    'max:255',
                    'unique:news,slug',
                ],

                'content' => [
                    'required',
                    'string',
                ],

                'thumbnail' => [
                    'nullable',
                    'file',
                    'image',
                    'mimetypes:image/jpeg,image/png,image/webp',
                    'max:10240',
                ],

                'status' => [
                    'required',
                    Rule::in(
                        $isSuperAdmin
                            ? [
                                News::STATUS_DRAFT,
                                News::STATUS_PUBLISHED,
                                News::STATUS_ARCHIVED,
                            ]
                            : [
                                News::STATUS_PUBLISHED,
                            ]
                    ),
                ],
            ],
            [
                'title.required' => 'Judul artikel wajib diisi.',
                'title.min' => 'Judul artikel minimal 5 karakter.',
                'title.max' => 'Judul artikel maksimal 50 karakter.',
                'title.regex' => 'Judul artikel harus mengandung huruf atau angka.',

                'slug.unique' => 'Slug artikel sudah digunakan.',

                'content.required' => 'Isi artikel wajib diisi.',

                'thumbnail.file' => 'Thumbnail harus berupa file.',
                'thumbnail.image' => 'File thumbnail harus berupa foto.',
                'thumbnail.mimetypes' => 'Thumbnail hanya boleh JPG, JPEG, PNG, atau WebP.',
                'thumbnail.max' => 'Ukuran thumbnail maksimal 10 MB.',

                'status.required' => 'Status publikasi wajib dipilih.',
                'status.in' => 'Admin Kelurahan hanya dapat membuat berita Published.',
            ]
        );

        /*
         * Admin Kelurahan selalu Published.
         */
        $status = $isSuperAdmin
            ? $validated['status']
            : News::STATUS_PUBLISHED;

        $slug = $validated['slug']
            ?: Str::slug($validated['title']);

        $originalSlug = $slug;
        $counter = 1;

        while (News::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        $thumbnailKey = null;

        if ($request->hasFile('thumbnail')) {
            $uploadResult = $this->imageStorageService->processAndStore(
                $request->file('thumbnail'),
                'news-thumbnails'
            );

            $thumbnailKey = $uploadResult['storage_key'];
        }

        $publishedAt = $status === News::STATUS_PUBLISHED
            ? now()
            : null;

        News::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $validated['content'],
            'thumbnail_storage_key' => $thumbnailKey,
            'author_id' => $user->id,
            'status' => $status,
            'published_at' => $publishedAt,
        ]);

        return redirect()
            ->route('news.index')
            ->with(
                'success',
                'Artikel berita edukasi berhasil dibuat.'
            );
    }

    public function edit(
        Request $request,
        int $id
    ): View {
        $article = News::findOrFail($id);

        $this->authorizeNewsAction(
            $article,
            $request
        );

        return view(
            'news.form',
            compact('article')
        );
    }

    public function update(
        Request $request,
        int $id
    ): RedirectResponse {
        $article = News::findOrFail($id);

        $this->authorizeNewsAction(
            $article,
            $request
        );

        $user = $request->user();
        $isSuperAdmin = $user->isSuperAdmin();

        $title = Str::squish(
            strip_tags($request->input('title', ''))
        );

        $slugInput = $request->filled('slug')
            ? Str::slug($request->input('slug'))
            : null;

        $request->merge([
            'title' => $title,
            'slug' => $slugInput,
        ]);

        $validated = $request->validate(
            [
                'title' => [
                    'required',
                    'string',
                    'min:5',
                    'max:150',
                    'regex:/[\pL\pN]/u',
                ],

                'slug' => [
                    'nullable',
                    'string',
                    'max:255',
                    Rule::unique('news', 'slug')
                        ->ignore($article->id),
                ],

                'content' => [
                    'required',
                    'string',
                ],

                'thumbnail' => [
                    'nullable',
                    'file',
                    'image',
                    'mimetypes:image/jpeg,image/png,image/webp',
                    'max:10240',
                ],

                'status' => [
                    'required',
                    Rule::in(
                        $isSuperAdmin
                            ? [
                                News::STATUS_DRAFT,
                                News::STATUS_PUBLISHED,
                                News::STATUS_ARCHIVED,
                            ]
                            : [
                                News::STATUS_PUBLISHED,
                            ]
                    ),
                ],
            ],
            [
                'title.required' => 'Judul artikel wajib diisi.',
                'title.min' => 'Judul artikel minimal 5 karakter.',
                'title.max' => 'Judul artikel maksimal 50 karakter.',
                'title.regex' => 'Judul artikel harus mengandung huruf atau angka.',

                'slug.unique' => 'Slug artikel sudah digunakan.',

                'content.required' => 'Isi artikel wajib diisi.',

                'thumbnail.file' => 'Thumbnail harus berupa file.',
                'thumbnail.image' => 'File thumbnail harus berupa foto.',
                'thumbnail.mimetypes' => 'Thumbnail hanya boleh JPG, JPEG, PNG, atau WebP.',
                'thumbnail.max' => 'Ukuran thumbnail maksimal 10 MB.',

                'status.required' => 'Status publikasi wajib dipilih.',
                'status.in' => 'Admin Kelurahan hanya dapat menggunakan status Published.',
            ]
        );

        $status = $isSuperAdmin
            ? $validated['status']
            : News::STATUS_PUBLISHED;

        $slug = $validated['slug']
            ?: $article->slug;

        $thumbnailKey = $article->thumbnail_storage_key;

        if ($request->hasFile('thumbnail')) {
            $uploadResult = $this->imageStorageService->processAndStore(
                $request->file('thumbnail'),
                'news-thumbnails'
            );

            $thumbnailKey = $uploadResult['storage_key'];
        }

        if ($status === News::STATUS_PUBLISHED) {
            if (
                $article->status === News::STATUS_PUBLISHED
                && $article->published_at
            ) {
                $publishedAt = $article->published_at;
            } else {
                $publishedAt = now();
            }
        } else {
            $publishedAt = null;
        }

        $article->update([
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $validated['content'],
            'thumbnail_storage_key' => $thumbnailKey,
            'status' => $status,
            'published_at' => $publishedAt,
        ]);

        return redirect()
            ->route('news.index')
            ->with(
                'success',
                'Artikel berita berhasil diperbarui.'
            );
    }

    public function destroy(
        Request $request,
        int $id
    ): RedirectResponse {
        $article = News::findOrFail($id);

        $this->authorizeNewsAction(
            $article,
            $request
        );

        $article->delete();

        return redirect()
            ->route('news.index')
            ->with(
                'success',
                'Artikel berita berhasil dihapus.'
            );
    }

    /**
     * Aturan hak akses:
     *
     * Super Admin:
     * - CRUD semua status.
     *
     * Admin Kelurahan:
     * - CRUD Published yang menjadi kewenangannya.
     *
     * Petugas:
     * - Read only.
     *
     * User/Masyarakat:
     * - Read only.
     */
    protected function authorizeNewsAction(
        News $article,
        Request $request
    ): void {
        $user = $request->user();

        abort_unless($user, 403);

        /*
         * Super Admin bebas mengelola semua berita.
         */
        if ($user->isSuperAdmin()) {
            return;
        }

        /*
         * Semua role selain Admin Kelurahan
         * tidak boleh Edit / Delete.
         */
        if (!$user->isVillageAdmin()) {
            abort(
                403,
                'Anda hanya memiliki akses membaca berita.'
            );
        }

        /*
         * Admin Kelurahan hanya boleh mengelola Published.
         */
        if ($article->status !== News::STATUS_PUBLISHED) {
            abort(
                403,
                'Admin Kelurahan tidak diperbolehkan mengelola Draft atau Archived.'
            );
        }

        /*
         * Periksa kewenangan berita.
         */
        if (!$user->canManageNews($article)) {
            abort(
                403,
                'Admin Kelurahan tidak memiliki izin mengelola artikel ini.'
            );
        }
    }
}
