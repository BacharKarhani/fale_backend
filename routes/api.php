<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

use App\Http\Controllers\Api\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

use App\Http\Controllers\Api\Admin\HomePage\BannerController;

// Public routes
Route::get('/banners', [BannerController::class, 'index']);
Route::get('/banners/{banner}', [BannerController::class, 'show']);


use App\Http\Middleware\IsAdmin;

Route::middleware(['auth:sanctum', IsAdmin::class])->group(function () {
    Route::post('/banners', [BannerController::class, 'store']);
    Route::put('/banners/{banner}', [BannerController::class, 'update']);
    Route::delete('/banners/{banner}', [BannerController::class, 'destroy']);
});


