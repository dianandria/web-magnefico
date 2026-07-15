<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ContactMessageController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'required|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'token'   => 'required|string', // Token CAPTCHA wajib ada
        ]);

        // Jika validasi gagal, kembalikan response dengan format yang seragam
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal, periksa kembali data Anda.',
                'errors'  => $validator->errors()
            ], 422);
        }

        // 2. Verifikasi Token reCAPTCHA ke Server Google
        $recaptchaSecret = env('RECAPTCHA_SECRET_KEY');
        
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => $recaptchaSecret,
            'response' => $request->token,
            'remoteip' => $request->ip(),
        ]);

        $recaptchaData = $response->json();

        // Jika CAPTCHA gagal divalidasi oleh Google
        if (empty($recaptchaData['success']) || !$recaptchaData['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi CAPTCHA gagal. Terdeteksi sebagai spam.'
            ], 400);
        }

        // 3. Simpan ke Database
        try {
            $contact = ContactMessage::create($request->only([
                'name', 'company', 'email', 'phone', 'subject', 'message'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Pesan berhasil dikirim.',
                'data'    => $contact
            ], 201); // 201 Created

        } catch (\Exception $e) {
            // Log error untuk debugging
            Log::error('Gagal menyimpan pesan kontak: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan internal pada server.'
            ], 500);
        }
    }
}
