<?php

use Illuminate\Support\Facades\Route;
use AdvancedNotifications\Http\Controllers\NotificationController;
use AdvancedNotifications\Http\Controllers\PreferenceController;
use AdvancedNotifications\Http\Controllers\TokenController;
use AdvancedNotifications\Http\Controllers\CampaignController;
use AdvancedNotifications\Http\Controllers\DashboardController;
use AdvancedNotifications\Http\Controllers\TopicController;
use AdvancedNotifications\Http\Controllers\AnalyticsController;
use AdvancedNotifications\Http\Controllers\SendNotificationController;

// Web routes if needed (e.g., for dashboard views)
// Currently empty as we focus on API and Blade components

Route::prefix('api')->group(function () {
    Route::post('/notifications/send', [NotificationController::class, 'send']);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    
    Route::post('/preferences', [PreferenceController::class, 'update']);
    Route::get('/preferences', [PreferenceController::class, 'show']);
    
    Route::apiResource('campaigns', CampaignController::class);
});

// Dashboard Routes
Route::group(['middleware' => ['web'], 'prefix' => 'dashboard', 'as' => 'advanced-notifications.'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    
    // Topics
    Route::resource('topics', TopicController::class)->names([
        'index' => 'topics.index',
        'store' => 'topics.store',
        'show' => 'topics.show',
        'destroy' => 'topics.destroy',
    ])->only(['index', 'store', 'show', 'destroy']);

    // Send Notification
    Route::get('/send', [SendNotificationController::class, 'create'])->name('send.create');
    Route::post('/send', [SendNotificationController::class, 'store'])->name('send.store');
    Route::get('/users/search', [SendNotificationController::class, 'searchUsers'])->name('users.search');

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
});

// Frontend Routes (Session Based) - Distinct prefix to avoid conflict
Route::group(['middleware' => ['web', 'auth:web'], 'prefix' => 'web-api/notifications'], function () {
    // Tokens
    Route::post('/register-token', [TokenController::class, 'register']);
    Route::post('/remove-token', [TokenController::class, 'remove']);
    
    // Topic Subscription
    Route::post('/topics/{id}/subscribe', [TopicController::class, 'subscribe']);
    Route::post('/topics/{id}/unsubscribe', [TopicController::class, 'unsubscribe']);
});
