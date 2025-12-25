<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
            $totalUsers = (int) DB::table('users')->where('user_type', 'user')->count();
        }

        // Recent events (handle schema with/without created_at)
        $orderColumn = Schema::hasColumn('events', 'created_at') ? 'e.created_at' : 'e.event_id';

        $recentEvents = DB::table('events as e')
            ->join('users as u', 'e.user_id', '=', 'u.user_id')
            ->select('e.*', 'u.full_name', 'u.email')
            ->orderByDesc(DB::raw($orderColumn))
            ->limit(5)
            ->get();

        return view('admin', [
            'totalEvents' => $totalEvents,
            'pendingEvents' => $pendingEvents,
            'totalUsers' => $totalUsers,
            'revenue' => $revenue,
            'recentEvents' => $recentEvents,
        ]);
    }
}