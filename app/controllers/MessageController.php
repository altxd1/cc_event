<?php

namespace App\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

/**
 * Controller to handle sending and viewing in‑app messages between users
 * and administrators/event managers. Messages allow for direct
 * communication regarding specific events or general inquiries.
 */
class MessageController extends Controller
{
    private function isAdminOrManager(?User $user): bool
    {
        $role = $user ? strtolower((string) $user->user_type) : '';
        return in_array($role, ['admin', 'event_manager'], true);
    }

    /**
     * Display a listing of messages for the current user.
     */
    public function index()
    {
        $userId = session('user_id');
        $sender = User::find($userId);
        $isAdmin = $this->isAdminOrManager($sender);
        // Gracefully handle missing messages table. If the table has not
        // been created (migrations not run), return an empty collection
        // rather than triggering a QueryException.
        if (!Schema::hasTable('messages')) {
            $messages = collect();
        } else {
            $messages = Message::where('receiver_id', $userId)
                ->orderByDesc('created_at')
                ->get();
        }
        return view('messages.index', compact('messages', 'isAdmin'));
    }

    /**
     * Show the form for creating a new message.
     */
    public function create()
    {
        $sender = User::find(session('user_id'));
        $isAdmin = $this->isAdminOrManager($sender);
        // Determine available recipients based on sender's role:
        // - If the sender is an admin or event manager, list all regular users (clients)
        // - Otherwise (client), list all admins and event managers
        if ($isAdmin) {
            $recipients = User::where('user_type', 'user')->get();
        } else {
            $recipients = User::whereIn('user_type', ['admin', 'event_manager'])->get();
        }
        return view('messages.create', compact('recipients', 'isAdmin'));
    }

    /**
     * Store a newly created message in storage.
     */
    public function store(Request $request)
    {
        // If the messages table does not exist (fresh project with pending
        // migrations), fail gracefully.
        if (!Schema::hasTable('messages')) {
            return back()->withErrors(['messages' => 'Messages table is missing. Please run migrations first.']);
        }

        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,user_id',
            'subject'     => 'nullable|string|max:255',
            'body'        => 'required|string',
        ]);

        Message::create([
            'sender_id'   => session('user_id'),
            'receiver_id' => $validated['receiver_id'],
            'subject'     => $validated['subject'],
            'body'        => $validated['body'],
        ]);

        $sender = User::find(session('user_id'));
        $routeName = $this->isAdminOrManager($sender) ? 'admin.messages.index' : 'messages.index';

        return redirect()->route($routeName)->with('success', 'Message sent successfully!');
    }
}