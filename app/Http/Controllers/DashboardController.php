<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // You already store user_id in session at login
        $userId = session('user_id');

        if (! $userId) {
            return redirect('/login');
        }

        $events = DB::table('events as e')
            ->join('event_places as p', 'e.place_id', '=', 'p.place_id')
            ->join('food_items as f', 'e.food_id', '=', 'f.food_id')
            ->join('event_designs as d', 'e.design_id', '=', 'd.design_id')
            ->where('e.user_id', '=', $userId)
            ->select('e.*', 'p.place_name', 'f.food_name', 'd.design_name')
            ->orderByDesc('e.created_at')   // if your table doesn't have created_at, change to event_id
            ->get();

        $events = collect($events);

        $totalEvents = $events->count();

        $upcomingEvents = $events->filter(function ($event) {
            return $event->status === 'approved'
                && Carbon::parse($event->event_date)->startOfDay()->greaterThanOrEqualTo(now()->startOfDay());
        })->count();

        $pendingEvents = $events->where('status', 'pending')->count();

        return view('dashboard', compact('events', 'totalEvents', 'upcomingEvents', 'pendingEvents'));
    }
}