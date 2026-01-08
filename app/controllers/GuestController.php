<?php

namespace App\Controllers;

use App\Models\Event;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuestController extends Controller
{
    /**
     * Display a listing of the guests for a given event.
     */
    public function index(int $eventId)
    {
        $event = Event::with('guests')->findOrFail($eventId);
        // Check permissions: only event owner or admin can view guests
        $userId = session('user_id');
        $isAdmin = function_exists('isAdmin') && isAdmin();
        if (!$isAdmin && $event->user_id != $userId) {
            abort(403);
        }
        $guests = $event->guests;
        return view('events.guests', compact('event', 'guests'));
    }

    /**
     * Import guests from a CSV file.
     */
    public function import(Request $request, int $eventId)
    {
        $event = Event::findOrFail($eventId);
        $userId = session('user_id');
        $isAdmin = function_exists('isAdmin') && isAdmin();
        if (!$isAdmin && $event->user_id != $userId) {
            abort(403);
        }
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt'],
        ]);
        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        // Skip header row if present
        $header = fgetcsv($handle);
        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) >= 1) {
                [$name, $email] = array_pad($data, 2, null);
                Guest::create([
                    'event_id' => $eventId,
                    'name' => $name,
                    'email' => $email,
                ]);
            }
        }
        fclose($handle);
        return redirect()->route('events.guests', ['eventId' => $eventId])
            ->with('message', 'Guests imported successfully');
    }

    /**
     * Export guests to CSV.
     */
    public function export(int $eventId)
    {
        $event = Event::with('guests')->findOrFail($eventId);
        $userId = session('user_id');
        $isAdmin = function_exists('isAdmin') && isAdmin();
        if (!$isAdmin && $event->user_id != $userId) {
            abort(403);
        }
        $guests = $event->guests;
        $filename = 'event_guests_' . $eventId . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $callback = function () use ($guests) {
            $output = fopen('php://output', 'w');
            // Header row
            fputcsv($output, ['Name', 'Email']);
            foreach ($guests as $guest) {
                fputcsv($output, [$guest->name, $guest->email]);
            }
            fclose($output);
        };
        return response()->stream($callback, 200, $headers);
    }
}