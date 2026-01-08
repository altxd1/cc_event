<?php

namespace App\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Models\Event;

class AdminController extends Controller
{
    public function index()
    {
        // Stats
        $totalEvents = (int) DB::table('events')->count();

        $pendingEvents = 0;
        $revenue = 0.0;

        if (Schema::hasColumn('events', 'status')) {
            $pendingEvents = (int) DB::table('events')->where('status', 'pending')->count();

            $revenue = (float) (DB::table('events')
                ->where('status', 'approved')
                ->sum('total_price') ?? 0);
        }

        $totalUsers = 0;
        if (Schema::hasColumn('users', 'user_type')) {
            // Count only clients; event managers and admins are excluded from total users
            $totalUsers = (int) DB::table('users')->where('user_type', 'client')->count();
        }

        // Recent events (handle schema with/without created_at)
        $orderColumn = Schema::hasColumn('events', 'created_at') ? 'e.created_at' : 'e.event_id';

        $recentEvents = DB::table('events as e')
            ->join('users as u', 'e.user_id', '=', 'u.user_id')
            ->select('e.*', 'u.full_name', 'u.email')
            ->orderByDesc(DB::raw($orderColumn))
            ->limit(5)
            ->get();

        // Build a mini calendar for the current month to display in the dashboard
        $month = date('m');
        $year  = date('Y');
        $currentDate = Carbon::create($year, $month, 1);

        // Get events for the current month
        $eventsMonth = Event::whereMonth('event_date', $month)
            ->whereYear('event_date', $year)
            ->get();

        // Build an array of booked dates for quick lookup (not strictly required here but kept for consistency)
        $bookedDates = $eventsMonth->pluck('event_date')
            ->map(function($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })->unique()->toArray();

        // Generate calendar days (similar to AdminCalendarController)
        $calendarDays = [];
        $startOfMonth = $currentDate->copy()->startOfMonth();
        $endOfMonth   = $currentDate->copy()->endOfMonth();

        // Add empty cells for the days of week before the first of the month
        $firstDayOfWeek = $startOfMonth->dayOfWeek;
        for ($i = 0; $i < $firstDayOfWeek; $i++) {
            $calendarDays[] = null;
        }

        // Populate the days of the month with events and status counts
        $currentDay = $startOfMonth->copy();
        while ($currentDay <= $endOfMonth) {
            $dayEvents = $eventsMonth->filter(function($event) use ($currentDay) {
                return Carbon::parse($event->event_date)->isSameDay($currentDay);
            });
            $pendingCount  = $dayEvents->where('status', 'pending')->count();
            $approvedCount = $dayEvents->where('status', 'approved')->count();
            $rejectedCount = $dayEvents->where('status', 'rejected')->count();
            $calendarDays[] = [
                'date'          => $currentDay->copy(),
                'formatted_date'=> $currentDay->format('Y-m-d'),
                'day'           => $currentDay->format('j'),
                'events'        => $dayEvents,
                'pending_count' => $pendingCount,
                'approved_count'=> $approvedCount,
                'rejected_count'=> $rejectedCount,
                'total_events'  => $dayEvents->count(),
                'is_today'      => $currentDay->isToday(),
                'is_weekend'    => $currentDay->isWeekend(),
                'is_past'       => $currentDay->isPast(),
            ];
            $currentDay->addDay();
        }

        return view('admin', [
            'totalEvents'   => $totalEvents,
            'pendingEvents' => $pendingEvents,
            'totalUsers'    => $totalUsers,
            'revenue'       => $revenue,
            'recentEvents'  => $recentEvents,
            // Calendar variables
            'calendarDays'  => $calendarDays,
            'currentDate'   => $currentDate,
        ]);
    }
}