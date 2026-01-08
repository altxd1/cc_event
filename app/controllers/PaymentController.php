<?php

namespace App\Controllers;

use App\Models\Event;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controller responsible for handling payments. For this prototype, the
 * payment processing is simulated and no real payment gateway is
 * contacted. In a production environment, this class should be
 * integrated with Stripe or another payment provider, ensuring that
 * amounts are charged in Moroccan Dirhams (MAD).
 */
class PaymentController extends Controller
{
    /**
     * Display the payment form for a given event. The event must belong
     * to the currently logged‑in user and still be pending payment.
     */
    public function show($eventId)
    {
        $event = Event::where('event_id', $eventId)
            ->where('user_id', session('user_id'))
            ->firstOrFail();

        return view('events.payment', compact('event'));
    }

    /**
     * Process a payment for an event. This method creates a Payment
     * record and, for the purposes of this demo, immediately marks it as
     * succeeded. In a real integration, you would contact the Stripe API
     * and only mark the payment as succeeded after a successful charge.
     */
    public function store(Request $request, $eventId)
    {
        $event = Event::where('event_id', $eventId)
            ->where('user_id', session('user_id'))
            ->firstOrFail();

        // Ensure the payments table exists; otherwise, we cannot process payments.
        if (! \Illuminate\Support\Facades\Schema::hasTable('payments')) {
            return back()->withErrors(['payment' => 'The payments table does not exist. Please run migrations to enable payment processing.']);
        }

        // In a production system, validate payment details here
        // For this prototype, we simulate success
        DB::beginTransaction();
        try {
            $payment = Payment::create([
                'event_id' => $event->event_id,
                'user_id'  => $event->user_id,
                'amount'   => $event->total_price,
                'currency' => 'MAD',
                'status'   => 'succeeded',
                'payment_reference' => 'SIMULATED-' . uniqid(),
            ]);

            // Mark the event as paid but still pending approval
            $event->status = 'pending';
            $event->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing failed: ' . $e->getMessage());
            return back()->withErrors(['payment' => 'Payment processing failed. Please try again.']);
        }

        return redirect()->route('dashboard')
            ->with('success', 'Payment completed successfully! Your event is now pending approval.');
    }
}