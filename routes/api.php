<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PortfolioController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\ExpertiseController;
use App\Http\Controllers\Api\SocialController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Middleware\VerifyApiKey;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware([VerifyApiKey::class])->group(function () {
    
    // Endpoint Portfolio
    Route::get('/portfolio-categories', [PortfolioController::class, 'getCategories']);
    Route::get('/portfolios', [PortfolioController::class, 'getPortfolios']);
    Route::get('/portfolios/{slug}', [PortfolioController::class, 'show']);

    // Client
    Route::get('/clients', [ClientController::class, 'index']);

    // Rute untuk mendapatkan semua setting
    Route::get('/settings', [SettingController::class, 'index']);

    Route::post('/contact', [ContactMessageController::class, 'store']);

    // Expertise
    Route::get('/expertises', [ExpertiseController::class, 'index']);

    // Social Link
    Route::get('/socials', [SocialController::class, 'index']);
    
    // Article
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/{slug}', [ArticleController::class, 'show']);
});