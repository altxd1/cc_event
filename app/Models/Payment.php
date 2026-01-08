<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Payment model represents a transaction associated with an event booking.
 * It stores the payment amount, currency, status, and reference returned
 * by the payment gateway (e.g. Stripe). Payments belong to events and
 * users, enabling us to track who made the payment and for which event.
 */
class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';
    protected $primaryKey = 'payment_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'event_id',
        'user_id',
        'amount',
        'currency',
        'status',
        'payment_reference',
    ];

    /**
     * A payment belongs to an event.
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }

    /**
     * A payment belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}