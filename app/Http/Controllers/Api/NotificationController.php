<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification as DbNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the authenticated user's notifications.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $notifications = $user->notifications() // Assumes 'notifications' relationship exists on User model
                               ->latest() // Already ordered by created_at desc in relationship, but good for clarity
                               ->paginate(15);

        return response()->json($notifications);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, DbNotification $notification) // Route model binding
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden. This notification does not belong to you.'], 403);
        }

        if (is_null($notification->read_at)) {
            $notification->update(['read_at' => now()]);
        }

        return response()->json(['message' => 'Notification marked as read.', 'notification' => $notification]);
    }

    /**
     * Mark all unread notifications for the authenticated user as read.
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        $updatedCount = $user->notifications()->whereNull('read_at')->update(['read_at' => now()]);

        return response()->json(['message' => "Successfully marked {$updatedCount} notifications as read."]);
    }
}
