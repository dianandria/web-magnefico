<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Models\PortfolioCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class PortfolioController extends Controller
{
    /**
     * Get all categories for the filter tabs
     */
    public function getCategories(): JsonResponse
    {
        // Fetch categories with id, name, and slug
        $categories = PortfolioCategory::select('id', 'name', 'slug')->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get portfolios, optionally filtered by a category slug
     */
    public function getPortfolios(Request $request): JsonResponse
    {
        // Dapatkan filter dari query string
        $categorySlug = $request->query('category'); // cth: ?category=website
        $isFeatured = $request->query('is_featured'); // cth: ?is_featured=1 atau ?is_featured=true

        // Fetch portfolios and eager load the categories to prevent N+1 queries
        $portfolios = Portfolio::with('categories:id,name,slug')
            ->when($categorySlug, function ($query, $categorySlug) {
                // Filter portfolios that belong to the requested category slug
                return $query->whereHas('categories', function ($q) use ($categorySlug) {
                    $q->where('slug', $categorySlug);
                });
            })
            ->when(!is_null($isFeatured), function ($query) use ($isFeatured) {
                // Konversi nilai string 'true', 'false', '1', atau '0' menjadi boolean sesungguhnya
                $featuredStatus = filter_var($isFeatured, FILTER_VALIDATE_BOOLEAN);
                return $query->where('is_featured', $featuredStatus);
            })
            ->orderBy('sort_order') // Urutkan berdasarkan sort_order
            ->latest()
            ->get();

        // Map the data to format the image URLs correctly
        $formattedPortfolios = $portfolios->map(function ($portfolio) {
            return [
                'title' => $portfolio->title,
                'description' => $portfolio->description,
                'client_name' => $portfolio->client_name,
                'year' => $portfolio->year,
                // Generate full URL for the main image
                'main_image_url' => $portfolio->main_image ? asset('storage/' . $portfolio->main_image) : null,
                'categories' => $portfolio->categories->pluck('name'),
                'slug' => $portfolio->slug,
                'is_featured' => $portfolio->is_featured
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedPortfolios
        ]);
    }

    public function show($slug): JsonResponse
    {
        // Cari data berdasarkan slug, pastikan eager load relasi 'categories'
        $portfolio = Portfolio::with('categories')->where('slug', $slug)->first();

        // Jika data tidak ditemukan, kembalikan response 404
        if (!$portfolio) {
            return response()->json([
                'success' => false,
                'message' => 'Portfolio not found'
            ], 404);
        }

        // --- Logika Parsing Image Gallery ---
        $galleryUrls = [];
        $savedGallery = $portfolio->image_gallery;
        $galleryArray = is_string($savedGallery) ? json_decode($savedGallery, true) : $savedGallery;

        if (is_array($galleryArray)) {
            foreach ($galleryArray as $imagePath) {
                $galleryUrls[] = Storage::disk('public')->url($imagePath);
            }
        }

        // --- Logika Prev & Next Project ---
        $prevProject = Portfolio::where('id', '<', $portfolio->id)
            ->orderBy('id', 'desc')
            ->first(['title', 'slug']);

        $nextProject = Portfolio::where('id', '>', $portfolio->id)
            ->orderBy('id', 'asc')
            ->first(['title', 'slug']);

        // --- Logika Related Projects ---
        // Pastikan menambahkan with('categories') agar relasi ikut terbawa (mengurangi N+1 query problem)
        $relatedProjects = Portfolio::with('categories')
            ->where('id', '!=', $portfolio->id)
            ->inRandomOrder() 
            ->take(3)
            ->get()
            ->map(function ($item) {
                // Ambil nama kategori pertama jika ada, jika tidak default ke 'Exhibition' (atau sesuaikan)
                // Atau jika ingin menampilkan semua kategori dipisah koma, gunakan: $item->categories->pluck('name')->implode(', ')
                $categoryName = $item->categories->isNotEmpty() 
                    ? $item->categories->first()->name 
                    : 'Exhibition';

                return [
                    'title' => $item->title,
                    'slug' => $item->slug,
                    'category_name' => $categoryName, 
                    'main_image_url' => $item->main_image 
                        ? Storage::disk('public')->url($item->main_image) 
                        : null,
                ];
            });

        // --- Response Format ---
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $portfolio->id,
                'title' => $portfolio->title,
                'client_name' => $portfolio->client_name,
                'year' => $portfolio->year,
                'description' => $portfolio->description,
                'slug' => $portfolio->slug,
                'is_featured' => $portfolio->is_featured,
                'sort_order' => $portfolio->sort_order,
                
                'main_image_url' => $portfolio->main_image 
                    ? Storage::disk('public')->url($portfolio->main_image) 
                    : null,
                
                'image_gallery_urls' => $galleryUrls,
                
                // Pluck kategori untuk produk utama
                'categories' => $portfolio->categories->pluck('name'),
                
                // Data tambahan untuk UI
                'navigation' => [
                    'prev' => $prevProject,
                    'next' => $nextProject,
                ],
                'related_projects' => $relatedProjects,
            ]
        ], 200);
    }
}
