<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expertise;
use Illuminate\Http\JsonResponse;

class ExpertiseController extends Controller
{
    public function index(): JsonResponse
    {
        // Mengambil data client, hanya yang aktif, dan diurutkan berdasarkan sort_order
        $clients = Expertise::where('is_active', true)
                        ->orderBy('sort_order', 'asc')
                        ->get(['id', 'title', 'image_path', 'description', 'is_active', 'sort_order']);

        return response()->json([
            'success' => true,
            'data'    => $clients
        ], 200);
    }
}
