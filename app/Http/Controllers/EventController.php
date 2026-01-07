<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EventController extends Controller
{
    public function create()
    {
        $places = DB::table('event_places')
            ->where('is_available', 1)
            ->get();

        $foodItems = DB::table('food_items')
            ->where('is_available', 1)
            ->get();

        $designs = DB::table('event_designs')
            ->where('is_available', 1)
            ->get();

        return view('events.create', compact('places', 'foodItems', 'designs'));
    }

    public function store(Request $request)
    {
        $userId = session('user_id');
        if (! $userId) {
            return redirect('/login');
        }

        // Validation replaces sanitize() + required checks
        $data = $request->validate([
            'event_name' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'event_time' => ['required'],
            'guests' => ['required', 'integer', 'min:10', 'max:1000'],
            'place_id' => ['required', 'integer'],
            'food_id' => ['required', 'integer'],
            'design_id' => ['required', 'integer'],
            'special_requests' => ['nullable', 'string', 'max:2000'],
        ]); 

        // Date must be today or future (your old rule was "future"; we keep "today or future")
        if (Carbon::parse($data['event_date'])->startOfDay()->lt(now()->startOfDay())) {
            return back()
                ->withErrors(['event_date' => 'Event date must be in the future!'])
                ->withInput();
        }

        // Fetch selected items (and ensure they are available)
        $place = DB::table('event_places')
            ->where('place_id', $data['place_id'])
            ->where('is_available', 1)
            ->first();

        $food = DB::table('food_items')
            ->where('food_id', $data['food_id'])
            ->where('is_available', 1)
            ->first();

        $design = DB::table('event_designs')
            ->where('design_id', $data['design_id'])
            ->where('is_available', 1)
            ->first();

        if (! $place || ! $food || ! $design) {
            return back()
                ->withErrors(['place_id' => 'Invalid selection. Please choose available options.'])
                ->withInput();
        }

        // Optional: capacity check (safe + useful)
        if (isset($place->capacity) && (int)$data['guests'] > (int)$place->capacity) {
            return back()
                ->withErrors(['guests' => 'Number of guests exceeds the venue capacity.'])
                ->withInput();
        }

        $placePrice = (float) ($place->price ?? 0);
        $foodPrice = (float) ($food->price_per_person ?? 0);
        $designPrice = (float) ($design->price ?? 0);

        $totalPrice = $placePrice + ($foodPrice * (int)$data['guests']) + $designPrice;

        // Build insert payload (handle schemas with/without status/created_at columns)
        $insert = [
            'user_id' => $userId,
            'event_name' => $data['event_name'],
            'event_date' => $data['event_date'],
            'event_time' => $data['event_time'],
            'place_id' => $data['place_id'],
            'food_id' => $data['food_id'],
            'design_id' => $data['design_id'],
            'number_of_guests' => $data['guests'],
            'special_requests' => $data['special_requests'] ?? '',
            'total_price' => $totalPrice,
        ];

        if (Schema::hasColumn('events', 'status')) {
            $insert['status'] = 'pending';
        }
        if (Schema::hasColumn('events', 'created_at')) {
            $insert['created_at'] = now();
        }
        if (Schema::hasColumn('events', 'updated_at')) {
            $insert['updated_at'] = now();
        }

        DB::table('events')->insert($insert); // https://laravel.com/docs/queries

        return redirect()->route('dashboard')
            ->with('success', 'Event created successfully! It is now pending approval.');
    }
    public function show(int $id)
{
    $userId = session('user_id');
    if (! $userId) {
        return redirect('/login');
    }

    $event = DB::table('events as e')
        ->join('event_places as p', 'e.place_id', '=', 'p.place_id')
        ->join('food_items as f', 'e.food_id', '=', 'f.food_id')
        ->join('event_designs as d', 'e.design_id', '=', 'd.design_id')
        ->where('e.event_id', $id)
        ->where('e.user_id', $userId) // IMPORTANT: user can only see their own events
        ->select('e.*', 'p.place_name', 'p.price as place_price', 'f.food_name', 'f.price_per_person', 'd.design_name', 'd.price as design_price')
        ->first();

    if (! $event) {
        abort(404);
    }

    return view('events.show', compact('event'));
}
}