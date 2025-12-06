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
        return response()->json(['success' => true]);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }

    public function destroy(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->delete();
        return response()->json(['success' => true]);
    }
}
