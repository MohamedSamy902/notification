<?php

use Illuminate\Support\Facades\Route;
use AdvancedNotifications\Http\Controllers\NotificationController;
use AdvancedNotifications\Http\Controllers\PreferenceController;
use AdvancedNotifications\Http\Controllers\TokenController;
use AdvancedNotifications\Http\Controllers\CampaignController;
use AdvancedNotifications\Http\Controllers\TopicController;

$middleware = config('advanced-notifications.routes.middleware', ['api', 'auth:sanctum,web']);

Route::group(['prefix' => 'api/notifications', 'middleware' => $middleware], function () {
    // Notifications
    Route::get('/', [NotificationController::class, 'index']);
    Route::get('/unread', [NotificationController::class, 'unread']);
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/{id}', [NotificationController::class, 'destroy']);

    // Preferences
    Route::get('/preferences', [PreferenceController::class, 'index']);
    Route::post('/preferences', [PreferenceController::class, 'update']);

    // Campaigns (Admin only - middleware to be added by user)
    Route::group(['prefix' => 'campaigns'], function () {
        Route::post('/create', [CampaignController::class, 'store']);
        Route::get('/', [CampaignController::class, 'index']);
    });
    // Topics Management (Admin)
    Route::get('/topics', [TopicController::class, 'index']);
    Route::post('/topics', [TopicController::class, 'store']);
    Route::get('/topics/{id}', [TopicController::class, 'show']);
    Route::delete('/topics/{id}', [TopicController::class, 'destroy']);
});
