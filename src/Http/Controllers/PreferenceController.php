<?php

namespace AdvancedNotifications\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use AdvancedNotifications\Services\PreferenceService;

class PreferenceController extends Controller
{
    protected $preferenceService;

    public function __construct(PreferenceService $preferenceService)
    {
        $this->preferenceService = $preferenceService;
    }

    public function index(Request $request)
    {
        // Return user preferences
        // Assuming relationship exists or query directly
        return \AdvancedNotifications\Models\UserNotificationPreference::where('user_id', $request->user()->id)->get();
    }

    public function update(Request $request)
    {
        $request->validate([
            'channel' => 'required|string',
            'is_enabled' => 'required|boolean',
            'quiet_hours_start' => 'nullable|date_format:H:i',
            'quiet_hours_end' => 'nullable|date_format:H:i',
        ]);

        $this->preferenceService->updatePreference(
            $request->user(),
            $request->channel,
            $request->is_enabled,
            $request->quiet_hours_start,
            $request->quiet_hours_end
        );

        return response()->json(['success' => true]);
    }
}
