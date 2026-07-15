<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    /**
     * Menampilkan daftar semua client.
     */
    public function index(): JsonResponse
    {
        // Mengambil data client, hanya yang aktif, dan diurutkan berdasarkan sort_order
        $clients = Client::where('is_active', true)
                        ->orderBy('sort_order', 'asc')
                        ->get(['id', 'name', 'logo_path', 'is_active', 'sort_order']);

        return response()->json([
            'success' => true,
            'data'    => $clients
        ], 200);
    }
}