<?php
// app/Http/Controllers/EventController.php
namespace App\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventPlace;
use App\Models\FoodItem;
use App\Models\EventDesign;

class EventController extends Controller
{
    public function create()
    {
        // Get available items
        $places = EventPlace::where('is_available', 1)->get();
        $foodItems = FoodItem::where('is_available', 1)->get();
        $designs = EventDesign::where('is_available', 1)->get();

        // Get available packages (no availability flag for now). If the
        // packages table has not been created (e.g. migrations not run),
        // return an empty collection to avoid SQL errors.
        // Return a collection even if packages table is missing. If
        // the packages table has not been created (e.g. migrations
        // not run), return an empty collection to avoid errors in
        // Blade when calling ->isEmpty().
        if (\Illuminate\Support\Facades\Schema::hasTable('packages')) {
            $packages = \App\Models\Package::all();
        } else {
            $packages = collect();
        }
        return view('events.create', compact('places', 'foodItems', 'designs', 'packages'));
    }

    public function store(Request $request)
    {
        // Build validation rules. Only validate package_id against
        // packages table if that table exists. Otherwise, allow null.
        $rules = [
            'event_name' => 'required|string|max:150',
            'event_date' => 'required|date',
            'event_time' => 'required',
            'place_id' => 'required|exists:event_places,place_id',
            'food_id' => 'required|exists:food_items,food_id',
            'design_id' => 'required|exists:event_designs,design_id',
            'guests' => 'required|integer|min:10|max:1000',
            'special_requests' => 'nullable|string',
        ];
        if (\Illuminate\Support\Facades\Schema::hasTable('packages')) {
            $rules['package_id'] = 'nullable|exists:packages,package_id';
        } else {
            $rules['package_id'] = 'nullable';
        }

        // Validate the request
        $validated = $request->validate($rules);

        // Check if place is already booked
        $isAvailable = Event::isPlaceAvailable(
            $request->place_id,
            $request->event_date,
            $request->event_time
        );

        if (!$isAvailable) {
            return back()
                ->withInput()
                ->withErrors([
                    'place_id' => 'This venue is already booked for the selected date and time. Please choose a different date, time, or venue.'
                ]);
        }

        // Get prices
        $place = EventPlace::find($request->place_id);
        $food = FoodItem::find($request->food_id);
        $design = EventDesign::find($request->design_id);
        $package = null;
        // Only attempt to retrieve the package if the table exists and a
        // package_id was supplied. Otherwise, leave as null.
        if ($request->package_id && \Illuminate\Support\Facades\Schema::hasTable('packages')) {
            $package = \App\Models\Package::find($request->package_id);
        }

        $guests = $request->guests;

        // Calculate total price using dynamic pricing rules
        $totalPrice = \App\Models\Event::calculatePrice($place, $food, $design, $package, $guests, $request->event_date);

        // Get user_id from session
        $userId = session('user_id');

        // Create the event in an unpaid state. After payment, status will
        // be updated to pending approval. This allows the user to pay
        // immediately from their dashboard. We use 'unpaid' instead of
        // 'awaiting_payment' to avoid truncation issues on shorter status
        // columns in legacy databases.
        $event = Event::create([
            'user_id' => $userId,
            'event_name' => $request->event_name,
            'event_date' => $request->event_date,
            'event_time' => $request->event_time,
            'place_id' => $request->place_id,
            'food_id' => $request->food_id,
            'design_id' => $request->design_id,
            'package_id' => $request->package_id,
            'number_of_guests' => $guests,
            'special_requests' => $request->special_requests,
            'total_price' => $totalPrice,
            'status' => 'pending',
            'created_at' => now(),
        ]);

        return redirect()->route('events.payment.show', ['eventId' => $event->event_id])
            ->with('success', 'Event created successfully! Please proceed with payment to confirm your booking.');
    }

    /**
     * Cancel an event. Users can cancel an event subject to the cancellation
     * policy: full refund if cancelled 7 days or more before the event,
     * otherwise no refund. This endpoint updates the status and issues
     * a refund placeholder (actual refund integration would be handled
     * via the payment gateway).
     */
    public function cancel(Request $request, $id)
    {
        $event = Event::where('event_id', $id)
            ->where('user_id', session('user_id'))
            ->firstOrFail();

        // Cannot cancel if already rejected or cancelled
        if (in_array($event->status, ['cancelled', 'rejected', 'completed'])) {
            return back()->with('error', 'This event cannot be cancelled.');
        }

        $eventDate = \Carbon\Carbon::parse($event->event_date);
        $now = \Carbon\Carbon::now();
        $daysDiff = $now->diffInDays($eventDate, false);

        // Determine refund eligibility
        $refundEligible = $daysDiff >= 7;

        $event->status = 'cancelled';
        $event->save();

        // TODO: Integrate refund via payment gateway. For now, just log.
        if ($refundEligible) {
            // Ideally refund via payment gateway
            // Payment::where('event_id', $event->event_id)->update(['status' => 'refunded']);
        }

        return redirect()->route('dashboard')
            ->with('success', $refundEligible
                ? 'Event cancelled successfully. A full refund will be processed.'
                : 'Event cancelled successfully. Unfortunately, no refund is available as the cancellation is within 7 days of the event.');
    }

    public function show($id)
    {
        $event = Event::findOrFail($id);
        return view('events.show', compact('event'));
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'place_id' => 'required|exists:event_places,place_id',
            'event_date' => 'required|date',
            'event_time' => 'required',
        ]);

        $isAvailable = Event::isPlaceAvailable(
            $request->place_id,
            $request->event_date,
            $request->event_time
        );

        if (!$isAvailable) {
            $conflicts = Event::getConflicts(
                $request->place_id,
                $request->event_date,
                $request->event_time
            );

            return response()->json([
                'available' => false,
                'message' => 'Venue is already booked for this date and time.',
                'conflicts' => $conflicts->map(function($event) {
                    return [
                        'event_name' => $event->event_name,
                        'event_date' => $event->event_date->format('Y-m-d'),
                        'event_time' => $event->event_time,
                        'status' => $event->status,
                    ];
                }),
            ]);
        }

        return response()->json([
            'available' => true,
            'message' => 'Venue is available for booking.',
        ]);
    }
}