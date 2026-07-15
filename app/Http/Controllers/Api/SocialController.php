<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SocialLink;
use Illuminate\Http\JsonResponse;

class SocialController extends Controller
{
    public function index(): JsonResponse
    {
        // Mengambil data client, hanya yang aktif, dan diurutkan berdasarkan sort_order
        $socials = SocialLink::where('is_active', true)
                        ->orderBy('sort_order', 'asc')
                        ->get(['id', 'platform_name', 'url', 'is_active', 'sort_order']);

        return response()->json([
            'success' => true,
            'data'    => $socials
        ], 200);
    }
}
