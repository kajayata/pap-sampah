<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Services\ImageStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function __construct(
        protected ImageStorageService $imageStorageService
    ) {}

    public function index(Request $request): View
    {
        $query = News::with(['author.role', 'author.village']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', $search)
                  ->orWhere('content', 'ilike', $search);
            });
        }

        $news = $query->orderByDesc('created_at')->paginate(10)->withQueryString();

        return view('news.index', compact('news'));
    }

    public function create(): View
    {
        $article = new News([
            'status' => News::STATUS_PUBLISHED,
        ]);

        return view('news.form', compact('article'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:news,slug'],
            'content' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:10240'],
            'status' => ['required', 'in:DRAFT,PUBLISHED,ARCHIVED'],
        ]);

        $slug = $request->filled('slug')
            ? Str::slug($request->input('slug'))
            : Str::slug($request->input('title'));

        // Ensure unique slug
        $originalSlug = $slug;
        $counter = 1;
        while (News::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        $thumbnailKey = null;
        if ($request->hasFile('thumbnail')) {
            $uploadResult = $this->imageStorageService->compressAndStore(
                $request->file('thumbnail'),
                'news-thumbnails'
            );
            $thumbnailKey = $uploadResult['storage_key'];
        }

        $status = $request->input('status');
        $publishedAt = $status === News::STATUS_PUBLISHED ? now() : null;

        News::create([
            'title' => $request->input('title'),
            'slug' => $slug,
            'content' => $request->input('content'),
            'thumbnail_storage_key' => $thumbnailKey,
            'author_id' => $request->user()->id,
            'status' => $status,
            'published_at' => $publishedAt,
        ]);

        return redirect()->route('news.index')->with('success', 'Artikel berita edukasi berhasil dibuat.');
    }

    public function edit(Request $request, int $id): View
    {
        $article = News::findOrFail($id);
        $this->authorizeNewsAction($article, $request);

        return view('news.form', compact('article'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $article = News::findOrFail($id);
        $this->authorizeNewsAction($article, $request);

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', "unique:news,slug,{$id}"],
            'content' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:10240'],
            'status' => ['required', 'in:DRAFT,PUBLISHED,ARCHIVED'],
        ]);

        $slug = $request->filled('slug')
            ? Str::slug($request->input('slug'))
            : $article->slug;

        $thumbnailKey = $article->thumbnail_storage_key;
        if ($request->hasFile('thumbnail')) {
            $uploadResult = $this->imageStorageService->compressAndStore(
                $request->file('thumbnail'),
                'news-thumbnails'
            );
            $thumbnailKey = $uploadResult['storage_key'];
        }

        $status = $request->input('status');
        $publishedAt = $article->published_at;
        if ($status === News::STATUS_PUBLISHED && !$publishedAt) {
            $publishedAt = now();
        }

        $article->update([
            'title' => $request->input('title'),
            'slug' => $slug,
            'content' => $request->input('content'),
            'thumbnail_storage_key' => $thumbnailKey,
            'status' => $status,
            'published_at' => $publishedAt,
        ]);

        return redirect()->route('news.index')->with('success', 'Artikel berita berhasil diperbarui.');
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $article = News::findOrFail($id);
        $this->authorizeNewsAction($article, $request);

        $article->delete();

        return redirect()->route('news.index')->with('success', 'Artikel berita berhasil dihapus.');
    }

    /**
     * Enforce news authorization rules:
     * - Super Admin can manage all news.
     * - Village Admin cannot modify or delete articles owned by Super Admin (Kecamatan) or other villages.
     */
    protected function authorizeNewsAction(News $article, Request $request): void
    {
        $user = $request->user();
        if (!$user || !$user->canManageNews($article)) {
            $article->loadMissing('author.role');
            if ($article->author?->isSuperAdmin()) {
                abort(403, 'Admin Kelurahan tidak diizinkan mengubah atau menghapus berita milik Kecamatan.');
            }
            abort(403, 'Admin Kelurahan tidak memiliki izin untuk mengelola artikel berita milik kelurahan lain.');
        }
    }
}
