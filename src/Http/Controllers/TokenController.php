<?php

namespace AdvancedNotifications\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

use AdvancedNotifications\Models\DeviceToken;

class TokenController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'device_type' => 'nullable|string',
        ]);

        $request->user()->deviceTokens()->firstOrCreate(
            ['token' => $request->token],
            ['device_type' => $request->device_type]
        );

        return response()->json(['success' => true, 'message' => 'Token registered successfully']);
    }

    public function remove(Request $request)
    {
        $request->validate(['token' => 'required|string']);
        
        $request->user()->deviceTokens()->where('token', $request->token)->delete();
        
        return response()->json(['success' => true, 'message' => 'Token removed successfully']);
    }
}
