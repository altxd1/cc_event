<?php
// app/Http/Controllers/EventController.php
namespace App\Http\Controllers;

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

        return view('events.create', compact('places', 'foodItems', 'designs'));
    }

    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'event_name' => 'required|string|max:150',
            'event_date' => 'required|date',
            'event_time' => 'required',
            'place_id' => 'required|exists:event_places,place_id',
            'food_id' => 'required|exists:food_items,food_id',
            'design_id' => 'required|exists:event_designs,design_id',
            'guests' => 'required|integer|min:10|max:1000',
            'special_requests' => 'nullable|string',
        ]);

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

        $placePrice = $place->price ?? 0;
        $foodPricePerPerson = $food->price_per_person ?? 0;
        $designPrice = $design->price ?? 0;
        
        $guests = $request->guests;
        $totalPrice = $placePrice + ($foodPricePerPerson * $guests) + $designPrice;

        // Get user_id from session
        $userId = session('user_id');

        // Create the event
        $event = Event::create([
            'user_id' => $userId,
            'event_name' => $request->event_name,
            'event_date' => $request->event_date,
            'event_time' => $request->event_time,
            'place_id' => $request->place_id,
            'food_id' => $request->food_id,
            'design_id' => $request->design_id,
            'number_of_guests' => $guests,
            'special_requests' => $request->special_requests,
            'total_price' => $totalPrice,
            'status' => 'pending',
            'created_at' => now(),
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Event created successfully! It is now pending approval.');
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