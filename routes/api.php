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
use App\Http\Controllers\Api\Admin\HomePage\HomeVideoController;
use App\Http\Controllers\Api\Admin\Homepage\BlogController;
use App\Http\Controllers\Api\Admin\FAQ\FaqController;
use App\Http\Controllers\Api\Admin\Contact\ContactController;
use App\Http\Controllers\Api\Admin\Subscription\SubscriptionController;
use App\Http\Controllers\Api\Admin\Homepage\DayController;
use App\Http\Controllers\Api\Admin\Homepage\EventScheduleController;
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
Route::get('/sliding-texts/{id}', [SlidingTextController::class, 'show']);
Route::get('/buy-ticket', [BuyTicketContentController::class, 'index']);
Route::get('/event-content', [EventContentController::class, 'index']);
Route::get('/event-direction', [EventDirectionController::class, 'index']);
Route::get('/team-members', [TeamMemberController::class, 'index']);
Route::get('/team-members/{id}', [TeamMemberController::class, 'show']); // ✅ Add show route
Route::get('/blogs', [BlogController::class, 'index']);
Route::get('/blogs/{id}', [BlogController::class, 'show']);
Route::get('/faqs', [FaqController::class, 'index']);
Route::get('/contact-info', [ContactController::class, 'getContactInfo']);
Route::post('/contact-submit', [ContactController::class, 'saveContactForm']);
Route::post('/subscribe', [SubscriptionController::class, 'store']);
Route::get('/event-schedule', [EventScheduleController::class, 'index']);
Route::get('/event-schedule/{id}', [EventScheduleController::class, 'show']); // 🟢 Single day by ID
Route::get('/home-video', [HomeVideoController::class, 'index']); // 🟢 Get active home video



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
    Route::get('/buy-ticket/{id}', [BuyTicketContentController::class, 'show']);
    // 🔒 EventContent APIs
    Route::post('/event-content', [EventContentController::class, 'store']);
    Route::put('/event-content/{id}', [EventContentController::class, 'update']);
    Route::delete('/event-content/{id}', [EventContentController::class, 'destroy']);
    Route::get('event-content/{id}', [EventContentController::class, 'show']);

    // 🔒 EventDirection APIs
    Route::post('/event-direction', [EventDirectionController::class, 'store']);
    Route::put('/event-direction/{id}', [EventDirectionController::class, 'update']);
    Route::delete('/event-direction/{id}', [EventDirectionController::class, 'destroy']);
    Route::get('event-direction/{id}', [EventDirectionController::class, 'show']);

    // 🔒 Team Member APIs
    Route::post('/team-members', [TeamMemberController::class, 'store']);
    Route::put('/team-members/{id}', [TeamMemberController::class, 'update']);
    Route::delete('/team-members/{id}', [TeamMemberController::class, 'destroy']);
    // 🔒 Blog APIs
    Route::post('/blogs', [BlogController::class, 'store']);
    Route::put('/blogs/{id}', [BlogController::class, 'update']);
    Route::delete('/blogs/{id}', [BlogController::class, 'destroy']);
    // 🔒 FAQ APIs
    Route::post('/faqs', [FaqController::class, 'store']);     // Add
    Route::put('/faqs/{faq}', [FaqController::class, 'update']); // Edit
    Route::delete('/faqs/{faq}', [FaqController::class, 'destroy']); // Delete
    Route::get('/faqs/{faq}', [FaqController::class, 'show']);

    // 🔒 Contact APIs
    Route::get('/admin/received-emails', [ContactController::class, 'getAllReceivedEmails']);
    Route::delete('/admin/received-emails/{id}', [ContactController::class, 'deleteReceivedEmail']);
    Route::post('/admin/contact-info', [ContactController::class, 'createContactInfo']);
    Route::put('/admin/contact-info/{id}', [ContactController::class, 'updateContactInfo']);
    Route::delete('/admin/contact-info/{id}', [ContactController::class, 'deleteContactInfo']);
    Route::get('/admin/subscriptions', [SubscriptionController::class, 'index']);
    Route::get('/admin/contact-info', [ContactController::class, 'getAdminContactInfo']);

    // 🔒 Day APIs
// 🔒 Day APIs
    Route::get('/days', [DayController::class, 'index']);        // List all days
    Route::post('/days', [DayController::class, 'store']);       // Create day
    Route::match(['put', 'patch'], '/days/{id}', [DayController::class, 'update']); // ✅ Accept both PUT & PATCH
    Route::delete('/days/{id}', [DayController::class, 'destroy']); // Delete day
    Route::get('days/{id}', [DayController::class, 'show']);

    // 🔒 Home Video APIs
    Route::post('/home-video', [HomeVideoController::class, 'store']);        // Create home video
    Route::put('home-video/{id}', [HomeVideoController::class, 'update']);   // Update home video
    Route::patch('home-video/{id}/status', [HomeVideoController::class, 'updateStatus']); // Update status only
    Route::get('/home-video/{id}', [HomeVideoController::class, 'show']);     // Get home video by ID
    Route::delete('/home-video/{id}', [HomeVideoController::class, 'destroy']); // Delete home video

});




