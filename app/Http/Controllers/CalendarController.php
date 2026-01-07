<?php
// app/Http\Controllers/CalendarController.php
namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventPlace;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Display the calendar view
     */
    public function index(Request $request)
    {
        // Get the current month/year or from request
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        
        // Get selected place from request (if any)
        $selectedPlaceId = $request->input('place_id');
        $selectedPlace = null;
        
        // If a place is selected, get its data
        if ($selectedPlaceId) {
            $selectedPlace = EventPlace::find($selectedPlaceId);
        }
        
        // Set both variables for compatibility
        $selectedPlaceData = $selectedPlace;
        
        // Get available places
        $places = EventPlace::where('is_available', 1)->get();
        
        // Get events for the current month
        $events = Event::whereMonth('event_date', $month)
                      ->whereYear('event_date', $year)
                      ->whereIn('status', ['approved', 'pending'])
                      ->get();
        
        // Get booked dates for the calendar - using snake_case to match view
        $booked_dates = Event::getBookedDates($month, $year);
        
        // Get monthly statistics
        $monthlyStats = Event::getMonthlyStats($month, $year);
        
        // Create Carbon instance for the month
        $currentDate = Carbon::create($year, $month, 1);
        
        // Calculate calendar days
        $calendarDays = $this->generateCalendar($currentDate, $events, $booked_dates);
        
        // Pass all data to the view
        return view('calendar.index', compact(
            'places',
            'events',
            'booked_dates', // Changed to snake_case
            'monthlyStats',
            'currentDate',
            'calendarDays',
            'month',
            'year',
            'selectedPlaceId',
            'selectedPlace',
            'selectedPlaceData'
        ));
    }
    
    /**
     * Generate calendar array for the view
     */
    private function generateCalendar($date, $events, $booked_dates)
    {
        $calendar = [];
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        
        // Get the day of week for the first day (0 = Sunday, 1 = Monday, etc.)
        $firstDayOfWeek = $startOfMonth->dayOfWeek;
        
        // Add empty days for the first week if needed
        for ($i = 0; $i < $firstDayOfWeek; $i++) {
            $calendar[] = null;
        }
        
        // Add all days of the month
        $currentDay = $startOfMonth->copy();
        while ($currentDay <= $endOfMonth) {
            $dayEvents = $events->filter(function($event) use ($currentDay) {
                return $event->event_date->format('Y-m-d') === $currentDay->format('Y-m-d');
            });
            
            $isBooked = in_array($currentDay->format('Y-m-d'), $booked_dates);
            
            $calendar[] = [
                'date' => $currentDay->copy(),
                'formatted_date' => $currentDay->format('Y-m-d'),
                'day' => $currentDay->format('j'),
                'events' => $dayEvents,
                'is_booked' => $isBooked,
                'is_today' => $currentDay->isToday(),
                'is_weekend' => $currentDay->isWeekend(),
            ];
            
            $currentDay->addDay();
        }
        
        return $calendar;
    }
    
    /**
     * Store a new event
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'place_id' => 'required|exists:event_places,place_id',
            'event_name' => 'required|string|max:255',
            'event_date' => 'required|date',
            'event_time' => 'required|date_format:H:i',
            'number_of_guests' => 'required|integer|min:1',
            'user_id' => 'required|exists:users,id',
        ]);
        
        // Check if place is available
        if (!Event::isPlaceAvailable(
            $validated['place_id'],
            $validated['event_date'],
            $validated['event_time']
        )) {
            return back()->withErrors([
                'event_time' => 'The selected time slot is already booked for this venue.'
            ]);
        }
        
        // Create the event
        $event = Event::create([
            'user_id' => $validated['user_id'],
            'place_id' => $validated['place_id'],
            'event_name' => $validated['event_name'],
            'event_date' => $validated['event_date'],
            'event_time' => $validated['event_time'],
            'number_of_guests' => $validated['number_of_guests'],
            'status' => 'pending',
            'total_price' => 0,
        ]);
        
        return redirect()->route('calendar.index')
            ->with('success', 'Event booked successfully! It is now pending approval.');
    }
   
}
