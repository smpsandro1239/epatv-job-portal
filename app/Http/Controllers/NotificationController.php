<?php

namespace App\Http\Controllers; // Web Controller Namespace

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
        $notifications = $user->notifications() // Relationship defined in User model
                               ->latest() // Already ordered by created_at desc in relationship definition
                               ->paginate(20);

        $unreadCount = $user->notifications()->whereNull('read_at')->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, DbNotification $notification) // Route model binding
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403, 'Forbidden. This notification does not belong to you.');
        }

        if (is_null($notification->read_at)) {
            $notification->update(['read_at' => now()]);
        }

        // Check if data contains a job_id to redirect to job.show
        if (isset($notification->data['job_id'])) {
            // Assuming a 'jobs.show' route exists and accepts a job ID or slug.
            // If Job model is needed, ensure it's findable.
            // For simplicity, just checking for job_id.
            // A more robust solution would be to check if route('jobs.show', $notification->data['job_id']) is valid.
            // This also assumes 'jobs.show' is a public route not requiring special context.
            // If job might be deleted, add error handling.
            // return redirect()->route('jobs.show', $notification->data['job_id']);
        }

        return redirect()->route('notifications.index')->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all unread notifications for the authenticated user as read.
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        $updatedCount = $user->notifications()->whereNull('read_at')->update(['read_at' => now()]);

        return redirect()->route('notifications.index')->with('success', "Successfully marked {$updatedCount} notifications as read.");
    }
}
