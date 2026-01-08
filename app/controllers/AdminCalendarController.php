<?php
// app/Http/Controllers/AdminCalendarController.php
namespace App\Controllers;

use App\Models\Event;
use App\Models\EventPlace;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminCalendarController extends Controller
{
    /**
     * Display the admin calendar view
     */
    public function index(Request $request)
    {
        // Determine whether to show a month or year view
        // Default to month view. Admins can pass view=year in query string to see the full year.
        $view = $request->input('view', 'month');

        // Get the current month/year or from request
        $month = $request->input('month', date('m'));
        $year  = $request->input('year', date('Y'));

        // Get selected place from request (if any)
        $selectedPlaceId = $request->input('place_id');
        $selectedPlace   = null;

        if ($selectedPlaceId) {
            $selectedPlace = EventPlace::find($selectedPlaceId);
        }

        // Get all places for filter
        $places = EventPlace::where('is_available', 1)->get();

        if ($view === 'year') {
            // For the year view, gather all events in the given year (optionally filtered by place)
            $eventsQuery = Event::whereYear('event_date', $year)
                ->with(['user', 'place']);
            if ($selectedPlaceId) {
                $eventsQuery = $eventsQuery->where('place_id', $selectedPlaceId);
            }
            $eventsYear = $eventsQuery->get();

            $calendarMonths = [];
            $monthlyStats  = [];
            for ($m = 1; $m <= 12; $m++) {
                // Filter events for this month
                $monthEvents = $eventsYear->filter(function ($event) use ($m) {
                    return $event->event_date->format('n') == $m;
                });
                // Get booked dates for this month
                $bookedDates = $monthEvents->pluck('event_date')
                    ->map(function($date) {
                        return $date->format('Y-m-d');
                    })
                    ->unique()
                    ->toArray();
                // Generate calendar days using existing method
                $currentDate = Carbon::create($year, $m, 1);
                $calendarDays = $this->generateCalendar($currentDate, $monthEvents, $bookedDates);
                // Compute monthly stats for this month
                $monthlyStats[$m] = $this->getMonthlyStats($m, $year);
                // Save month data
                $calendarMonths[$m] = [
                    'month'      => $m,
                    'monthName'  => $currentDate->format('F'),
                    'currentDate'=> $currentDate,
                    'calendarDays' => $calendarDays,
                    'monthlyStats' => $monthlyStats[$m],
                ];
            }

            return view('admin.calendar.index', [
                'view'           => 'year',
                'calendarMonths' => $calendarMonths,
                'year'           => $year,
                'selectedPlaceId' => $selectedPlaceId,
                'selectedPlace'   => $selectedPlace,
                'places'          => $places,
            ]);
        }

        // Default to month view
        // Get events for the current month
        $eventsQuery = Event::whereMonth('event_date', $month)
            ->whereYear('event_date', $year)
            ->with(['user', 'place']);
        if ($selectedPlaceId) {
            $eventsQuery = $eventsQuery->where('place_id', $selectedPlaceId);
        }
        $events = $eventsQuery->get();

        // Get booked dates (all statuses for admin)
        $booked_dates = $events->pluck('event_date')
            ->map(function($date) {
                return $date->format('Y-m-d');
            })
            ->unique()
            ->toArray();

        // Get monthly statistics (all statuses)
        $monthlyStats = $this->getMonthlyStats($month, $year);

        // Create Carbon instance for the month
        $currentDate = Carbon::create($year, $month, 1);

        // Calculate calendar days
        $calendarDays = $this->generateCalendar($currentDate, $events, $booked_dates);

        return view('admin.calendar.index', [
            'view'            => 'month',
            'places'          => $places,
            'events'          => $events,
            'booked_dates'    => $booked_dates,
            'monthlyStats'    => $monthlyStats,
            'currentDate'     => $currentDate,
            'calendarDays'    => $calendarDays,
            'month'           => $month,
            'year'            => $year,
            'selectedPlaceId' => $selectedPlaceId,
            'selectedPlace'   => $selectedPlace,
        ]);
    }
    
    /**
     * calendar array for the view
     */
    private function generateCalendar($date, $events, $booked_dates)
    {
        $calendar = [];
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        
        // Get the day of week for the first day 
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
            
            // Group events by status for admin view
            $pendingCount = $dayEvents->where('status', 'pending')->count();
            $approvedCount = $dayEvents->where('status', 'approved')->count();
            $rejectedCount = $dayEvents->where('status', 'rejected')->count();
            
            $calendar[] = [
                'date' => $currentDay->copy(),
                'formatted_date' => $currentDay->format('Y-m-d'),
                'day' => $currentDay->format('j'),
                'events' => $dayEvents,
                'pending_count' => $pendingCount,
                'approved_count' => $approvedCount,
                'rejected_count' => $rejectedCount,
                'total_events' => $dayEvents->count(),
                'is_booked' => $isBooked,
                'is_today' => $currentDay->isToday(),
                'is_weekend' => $currentDay->isWeekend(),
                'is_past' => $currentDay->isPast(),
            ];
            
            $currentDay->addDay();
        }
        
        return $calendar;
    }
    
    /**
     * Get monthly statistics for admin
     */
    private function getMonthlyStats($month, $year)
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        
        $events = Event::whereBetween('event_date', [$startDate, $endDate])->get();
        
        return [
            'total_events' => $events->count(),
            'total_guests' => $events->sum('number_of_guests'),
            'total_revenue' => $events->where('status', 'approved')->sum('total_price'),
            'pending_events' => $events->where('status', 'pending')->count(),
            'approved_events' => $events->where('status', 'approved')->count(),
            'rejected_events' => $events->where('status', 'rejected')->count(),
            'unique_dates' => $events->pluck('event_date')->unique()->count(),
            'most_popular_day' => $events->groupBy('event_date')->map->count()->sortDesc()->keys()->first(),
        ];
    }
    
    /**
     * Show events for a specific day
     */
    public function showDay(Request $request, $date)
    {
        $selectedDate = Carbon::parse($date);
        
        // Get events for the selected date
        $events = Event::whereDate('event_date', $selectedDate)
                      ->with(['user', 'place', 'food', 'design'])
                      ->orderBy('event_time')
                      ->get();
        
        // Get places for reference
        $places = EventPlace::where('is_available', 1)->get();
        
        return view('admin.calendar.day', compact('events', 'selectedDate', 'places'));
    }
}