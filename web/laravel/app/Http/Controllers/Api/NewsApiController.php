<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsApiController extends Controller
{
    /**
     * Get published environmental news and educational articles.
     * Query params:
     * - search: string (optional)
     * - per_page: int (default: 10)
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->input('per_page', 10), 1), 50);

        $query = News::published()
            ->with('author:id,name');

        if ($request->filled('search')) {
            $search = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', $search)
                  ->orWhere('content', 'ilike', $search);
            });
        }

        $news = $query->orderByDesc('published_at')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => [
                'current_page' => $news->currentPage(),
                'last_page' => $news->lastPage(),
                'total' => $news->total(),
                'articles' => $news->items(),
            ],
        ]);
    }

    /**
     * Get detail of a specific news article by slug or id.
     */
    public function show(string $slugOrId): JsonResponse
    {
        $article = News::published()
            ->with('author:id,name')
            ->where(function ($q) use ($slugOrId) {
                if (is_numeric($slugOrId)) {
                    $q->where('id', (int) $slugOrId)->orWhere('slug', $slugOrId);
                } else {
                    $q->where('slug', $slugOrId);
                }
            })
            ->first();

        if (!$article) {
            return response()->json([
                'status' => 'error',
                'message' => 'Artikel berita tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $article,
        ]);
    }
}
