<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get all unread notifications for the authenticated user
     */
    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->with('report:id,match_name,report_date')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications);
    }

    /**
     * Mark notifications as read.
     * If an 'id' is provided, marks that specific notification.
     * Otherwise, marks all notifications for the authenticated user.
     */
    public function markAsRead(Request $request)
    {
        $query = Notification::where('user_id', auth()->id())
            ->whereNull('read_at');

        if ($request->has('id')) {
            $query->where('id', $request->input('id'));
        }

        $query->update(['read_at' => now()]);

        return response()->json(['message' => 'Notifications marked as read successfully.']);
    }
}
