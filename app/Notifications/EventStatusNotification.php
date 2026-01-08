<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification sent to clients when their event is approved or rejected.
 */
class EventStatusNotification extends Notification
{
    use Queueable;

    public string $status;
    public string $eventName;

    public function __construct(string $status, string $eventName)
    {
        $this->status = $status;
        $this->eventName = $eventName;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification for storage.
     */
    public function toDatabase($notifiable): array
    {
        return [
            'event_name' => $this->eventName,
            'status'     => $this->status,
            'message'    => $this->status === 'approved'
                ? "Your event '{$this->eventName}' has been approved!"
                : "Your event '{$this->eventName}' has been rejected.",
        ];
    }
}