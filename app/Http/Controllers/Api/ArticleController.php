<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    // API untuk halaman List Artikel (Grid/Cards)
    public function index(Request $request): JsonResponse
    {
        // Mengambil nilai 'per_page' dari URL query, defaultnya 6 jika tidak ada
        $perPage = $request->query('per_page', 6);

        $articles = Article::where('is_active', true)
                        ->orderBy('sort_order', 'asc')
                        ->orderBy('published_date', 'desc')
                        ->paginate($perPage, [
                            'id', 'title', 'slug', 'category', 
                            'author_name', 'published_date', 'image_path', 
                            'is_active', 'sort_order'
                        ]);

        return response()->json([
            'success'    => true,
            'data'       => $articles->items(),
            'pagination' => [
                'total'        => $articles->total(),
                'per_page'     => $articles->perPage(),
                'current_page' => $articles->currentPage(),
                'last_page'    => $articles->lastPage(),
                'from'         => $articles->firstItem(),
                'to'           => $articles->lastItem()
            ]
        ], 200);
    }

    // API untuk halaman Detail Artikel
    public function show($slug): JsonResponse
    {
        $article = Article::where('slug', $slug)
                        ->where('is_active', true)
                        ->first();

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found'
            ], 404);
        }

        $article->image_url = $article->image_path ? asset('storage/' . $article->image_path) : null;

        // Mengambil artikel selanjutnya untuk fitur "Next Read" di desain Anda
        $nextArticle = Article::where('is_active', true)
                            ->where('id', '!=', $article->id)
                            ->orderBy('sort_order', 'asc')
                            ->first(['title', 'slug']);

        return response()->json([
            'success' => true,
            'data'    => [
                'article' => $article,
                'next_read' => $nextArticle
            ]
        ], 200);
    }
}