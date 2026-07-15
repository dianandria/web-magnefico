<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    /**
     * Menampilkan semua data setting.
     */
    public function index(): JsonResponse
    {
        // Ambil semua data setting
        $settings = Setting::select(['key', 'value', 'type'])->get();

        /* 
         * TIPS: Mengubah format menjadi Key-Value Pair menggunakan pluck()
         * Hasilnya akan seperti: 
         * { "site_name": "My Web", "logo": "logo.png" }
         */
        $formattedSettings = $settings->pluck('value_url', 'key');

        return response()->json([
            'success' => true,
            'data'    => $formattedSettings 
        ], 200);
    }
}