<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Message model represents an in‑app communication between two users.
 * Each message stores the sender, receiver, optional related event,
 * subject, and body. Messages can be marked as read, allowing the UI
 * to show unread counts and notifications.
 */
class Message extends Model
{
    use HasFactory;

    protected $table = 'messages';
    protected $primaryKey = 'message_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'event_id',
        'subject',
        'body',
        'read_at',
    ];

    protected $dates = [
        'read_at',
    ];

    /**
     * Sender relationship.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'user_id');
    }

    /**
     * Receiver relationship.
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id', 'user_id');
    }

    /**
     * Optional event relationship. Messages may be tied to a specific
     * event (for example, questions about a booking).
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }
}