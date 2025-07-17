<?php

use App\Http\Controllers\Api\Admin\Homepage\TeamMemberController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\HomePage\BannerController;
use App\Http\Controllers\Api\Admin\HomePage\ServiceController;
use App\Http\Controllers\Api\Admin\HomePage\BuyTicketContentController;
use App\Http\Controllers\Api\Admin\HomePage\SlidingTextController;
use App\Http\Controllers\Api\Admin\HomePage\EventContentController;
use App\Http\Controllers\Api\Admin\HomePage\EventDirectionController;
use App\Http\Controllers\Api\Admin\Homepage\BlogController;

use App\Http\Middleware\IsAdmin;

// 🟢 Public Auth APIs
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// 🟢 Public APIs
Route::get('/banners', [BannerController::class, 'index']);
Route::get('/banners/{banner}', [BannerController::class, 'show']);

Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{service}', [ServiceController::class, 'show']);

Route::get('/sliding-texts', [SlidingTextController::class, 'index']);
Route::get('/buy-ticket', [BuyTicketContentController::class, 'index']);
Route::get('/event-content', [EventContentController::class, 'index']);
Route::get('/event-direction', [EventDirectionController::class, 'index']);
Route::get('/team-members', [TeamMemberController::class, 'index']);
Route::get('/team-members/{id}', [TeamMemberController::class, 'show']); // ✅ Add show route
Route::get('/blogs', [BlogController::class, 'index']);
Route::get('/blogs/{id}', [BlogController::class, 'show']);

// 🔒 Protected APIs (Admins only)
Route::middleware(['auth:sanctum', IsAdmin::class])->group(function () {

    // 🔒 Banner APIs
    Route::post('/banners', [BannerController::class, 'store']);
    Route::put('/banners/{banner}', [BannerController::class, 'update']);
    Route::delete('/banners/{banner}', [BannerController::class, 'destroy']);

    // 🔒 Service APIs
    Route::post('/services', [ServiceController::class, 'store']);
    Route::put('/services/{service}', [ServiceController::class, 'update']);
    Route::delete('/services/{service}', [ServiceController::class, 'destroy']);

    // 🔒 Sliding Text APIs
    Route::post('/sliding-texts', [SlidingTextController::class, 'store']);
    Route::put('/sliding-texts/{sliding_text}', [SlidingTextController::class, 'update']);
    Route::delete('/sliding-texts/{sliding_text}', [SlidingTextController::class, 'destroy']);

    // 🔒 Buy Ticket Content APIs
    Route::post('/buy-ticket', [BuyTicketContentController::class, 'store']);
    Route::put('/buy-ticket/{id}', [BuyTicketContentController::class, 'update']);
    Route::delete('/buy-ticket/{id}', [BuyTicketContentController::class, 'destroy']);

    // 🔒 EventContent APIs
    Route::post('/event-content', [EventContentController::class, 'store']);
    Route::put('/event-content/{id}', [EventContentController::class, 'update']);
    Route::delete('/event-content/{id}', [EventContentController::class, 'destroy']);

    // 🔒 EventDirection APIs
    Route::post('/event-direction', [EventDirectionController::class, 'store']);
    Route::put('/event-direction/{id}', [EventDirectionController::class, 'update']);
    Route::delete('/event-direction/{id}', [EventDirectionController::class, 'destroy']);

    Route::post('/team-members', [TeamMemberController::class, 'store']);
    Route::put('/team-members/{id}', [TeamMemberController::class, 'update']);
    Route::delete('/team-members/{id}', [TeamMemberController::class, 'destroy']);

    Route::post('/blogs', [BlogController::class, 'store']);
    Route::put('/blogs/{id}', [BlogController::class, 'update']);
    Route::delete('/blogs/{id}', [BlogController::class, 'destroy']);
});