<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

/**
 * Controller to display in‑app notifications to the user. Users can
 * view and mark notifications as read. Notifications are stored in
 * the database via the built-in notification system.
 */
class NotificationController extends Controller
{
    public function index()
    {
        $userId = session('user_id');
        // If the notifications table does not exist, return an empty
        // collection to avoid throwing an error. Otherwise, load the
        // user's notifications as usual.
        if (!Schema::hasTable('notifications')) {
            $notifications = collect();
        } else {
            $user = \App\Models\User::findOrFail($userId);
            $notifications = $user->notifications()->orderByDesc('created_at')->get();
        }
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $userId = session('user_id');
        // If the notifications table does not exist, there is nothing
        // to mark as read. Simply redirect back without action.
        if (!Schema::hasTable('notifications')) {
            return back();
        }
        $user = \App\Models\User::findOrFail($userId);
        $notification = $user->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();
        return back();
    }
}