<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminEventsController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');

        $allowed = ['all', 'pending', 'approved', 'rejected', 'completed'];
        if (!in_array($status, $allowed, true)) {
            $status = 'all';
        }

        // Counts for tabs
        $totalCount = (int) DB::table('events')->count();

        $byStatus = DB::table('events')
            ->select('status', DB::raw('COUNT(*) as c'))
            ->groupBy('status')
            ->pluck('c', 'status')
            ->toArray();

        $counts = [
            'all' => $totalCount,
            'pending' => (int) ($byStatus['pending'] ?? 0),
            'approved' => (int) ($byStatus['approved'] ?? 0),
            'rejected' => (int) ($byStatus['rejected'] ?? 0),
        ];

        $orderColumn = Schema::hasColumn('events', 'created_at') ? 'e.created_at' : 'e.event_id';

        $phoneSelect = Schema::hasColumn('users', 'phone')
            ? 'u.phone'
            : DB::raw('NULL as phone');

        $query = DB::table('events as e')
            ->join('users as u', 'e.user_id', '=', 'u.user_id')
            ->join('event_places as p', 'e.place_id', '=', 'p.place_id')
            ->join('food_items as f', 'e.food_id', '=', 'f.food_id')
            ->join('event_designs as d', 'e.design_id', '=', 'd.design_id')
            ->select([
                'e.event_id',
                'e.event_name',
                'e.event_date',
                'e.event_time',
                'e.number_of_guests',
                'e.total_price',
                'e.status',
                'u.full_name',
                'u.email',
                $phoneSelect,
                'p.place_name',
                'f.food_name',
                'd.design_name',
            ]);

        if ($status !== 'all') {
            $query->where('e.status', $status);
        }

        $events = $query->orderByDesc(DB::raw($orderColumn))->get();

        return view('admin.events.index', compact('events', 'status', 'counts'));
    }

    public function show(int $id)
    {
        $orderColumn = Schema::hasColumn('events', 'created_at') ? 'e.created_at' : 'e.event_id';

        $phoneSelect = Schema::hasColumn('users', 'phone')
            ? 'u.phone'
            : DB::raw('NULL as phone');

        $event = DB::table('events as e')
            ->join('users as u', 'e.user_id', '=', 'u.user_id')
            ->join('event_places as p', 'e.place_id', '=', 'p.place_id')
            ->join('food_items as f', 'e.food_id', '=', 'f.food_id')
            ->join('event_designs as d', 'e.design_id', '=', 'd.design_id')
            ->where('e.event_id', $id)
            ->select([
                'e.*',
                'u.full_name',
                'u.email',
                $phoneSelect,

                'p.place_name',
                'p.capacity as place_capacity',
                'p.price as place_price',

                'f.food_name',
                'f.price_per_person as food_price_per_person',

                'd.design_name',
                'd.price as design_price',
            ])
            ->orderByDesc(DB::raw($orderColumn))
            ->first();

        if (! $event) {
            abort(404);
        }

        return view('admin.events.show', compact('event'));
    }

    public function approve(int $id)
    {
        DB::table('events')->where('event_id', $id)->update(['status' => 'approved']);
        return back()->with('message', 'Event approved successfully!');
    }

    public function reject(int $id)
    {
        DB::table('events')->where('event_id', $id)->update(['status' => 'rejected']);
        return back()->with('message', 'Event rejected!');
    }

    public function destroy(int $id)
    {
        DB::table('events')->where('event_id', $id)->delete();
        return redirect()->route('admin.events.index')->with('message', 'Event deleted successfully!');
    }
}