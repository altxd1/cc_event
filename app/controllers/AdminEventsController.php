<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminEventsController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');

        // Allowed status filters. 'unpaid' represents events without a payment record.
        $allowed = ['all', 'pending', 'approved', 'rejected', 'completed', 'unpaid'];
        if (!in_array($status, $allowed, true)) {
            $status = 'all';
        }

        // Counts for tabs
        // Total events count
        $totalCount = (int) DB::table('events')->count();

        // Count events by status (excluding payment status)
        $byStatus = DB::table('events')
            ->select('status', DB::raw('COUNT(*) as c'))
            ->groupBy('status')
            ->pluck('c', 'status')
            ->toArray();

        // Count unpaid events (events without a payment record).
        // If the payments table doesn't exist, we treat the count as zero.
        $paymentsExists = \Illuminate\Support\Facades\Schema::hasTable('payments');
        if ($paymentsExists) {
            $unpaidCount = DB::table('events as e')
                ->leftJoin('payments as pay', 'e.event_id', '=', 'pay.event_id')
                ->whereNull('pay.payment_id')
                ->count();
        } else {
            $unpaidCount = 0;
        }

        $counts = [
            'all'     => $totalCount,
            'unpaid'  => $unpaidCount,
            'pending' => (int) ($byStatus['pending'] ?? 0),
            'approved' => (int) ($byStatus['approved'] ?? 0),
            'rejected' => (int) ($byStatus['rejected'] ?? 0),
            'completed' => (int) ($byStatus['completed'] ?? 0),
        ];

        $orderColumn = Schema::hasColumn('events', 'created_at') ? 'e.created_at' : 'e.event_id';

        $phoneSelect = Schema::hasColumn('users', 'phone')
            ? 'u.phone'
            : DB::raw('NULL as phone');

        $query = DB::table('events as e')
            ->join('users as u', 'e.user_id', '=', 'u.user_id')
            ->join('event_places as p', 'e.place_id', '=', 'p.place_id')
            ->join('food_items as f', 'e.food_id', '=', 'f.food_id')
            ->join('event_designs as d', 'e.design_id', '=', 'd.design_id');

        // Conditionally join payments and compute is_paid. If the payments table
        // doesn't exist, we still select a dummy column for is_paid.
        if ($paymentsExists) {
            $query = $query->leftJoin('payments as pay', 'e.event_id', '=', 'pay.event_id');
            $query = $query->select([
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
                \DB::raw('CASE WHEN pay.payment_id IS NULL THEN 0 ELSE 1 END as is_paid'),
            ]);
        } else {
            $query = $query->select([
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
                \DB::raw('0 as is_paid'),
            ]);
        }

        if ($status === 'unpaid') {
            // Filter events without payment only if the payments table exists.
            if ($paymentsExists) {
                $query->whereNull('pay.payment_id');
            }
        } elseif ($status !== 'all') {
            // Filter by event status
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
        // Approve event and notify client
        DB::table('events')->where('event_id', $id)->update(['status' => 'approved']);

        // Retrieve event and user for notification
        $event = \App\Models\Event::find($id);
        if ($event) {
            $user = \App\Models\User::find($event->user_id);
            if ($user) {
                $user->notify(new \App\Notifications\EventStatusNotification('approved', $event->event_name));
            }
        }
        return back()->with('message', 'Event approved successfully!');
    }

    public function reject(int $id)
    {
        // Reject event and notify client
        DB::table('events')->where('event_id', $id)->update(['status' => 'rejected']);
        // Notification
        $event = \App\Models\Event::find($id);
        if ($event) {
            $user = \App\Models\User::find($event->user_id);
            if ($user) {
                $user->notify(new \App\Notifications\EventStatusNotification('rejected', $event->event_name));
            }
        }
        return back()->with('message', 'Event rejected!');
    }

    public function destroy(int $id)
    {
        DB::table('events')->where('event_id', $id)->delete();
        return redirect()->route('admin.events.index')->with('message', 'Event deleted successfully!');
    }
}