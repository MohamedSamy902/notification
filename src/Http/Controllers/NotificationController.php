<?php

namespace AdvancedNotifications\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use AdvancedNotifications\Models\AdvancedNotification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->notifications()->paginate(20);
    }

    public function unread(Request $request)
    {
        return $request->user()->unreadNotifications()->get();
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        // Log Analytics: Read
        app(\AdvancedNotifications\Services\AnalyticsService::class)->log(
            $notification->id,
            'database', // Assuming database for manual mark as read
            'read',
            $request->user()->id
        );

        return response()->json(['success' => true]);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        // Ideally log for each, but for performance maybe just skip or log bulk?
        // Let's skip detailed logging for bulk mark-read for now to avoid flooding DB
        return response()->json(['success' => true]);
    }

    public function destroy(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->delete();
        return response()->json(['success' => true]);
    }

    public function trackDelivered(Request $request, $id)
    {
        // Log Analytics: Delivered
        app(\AdvancedNotifications\Services\AnalyticsService::class)->log(
            $id,
            'realtime', // Usually called from frontend for realtime/fcm
            'delivered',
            $request->user()->id
        );

        return response()->json(['success' => true]);
    }

    public function trackClicked(Request $request, $id)
    {
        // Log Analytics: Clicked
        app(\AdvancedNotifications\Services\AnalyticsService::class)->log(
            $id,
            'realtime',
            'clicked',
            $request->user()->id
        );

        // If notification exists, mark as read too
        $notification = $request->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['success' => true]);
    }
}
