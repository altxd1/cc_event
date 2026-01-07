<?php
// app/Models/Event.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // Fixed import

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';
    protected $primaryKey = 'event_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    
    protected $fillable = [
        'user_id',
        'event_name',
        'event_date',
        'event_time',
        'place_id',
        'food_id',
        'design_id',
        'number_of_guests',
        'special_requests',
        'total_price',
        'status',
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_time' => 'string',
        'number_of_guests' => 'integer',
        'total_price' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    /**
     * Check if a place is available on a specific date/time
     */
    public static function isPlaceAvailable($placeId, $eventDate, $eventTime, $excludeEventId = null)
    {
        // Normalize time format
        if (strlen($eventTime) === 5) {
            $eventTime .= ':00';
        }

        $query = self::where('place_id', $placeId)
                    ->where('event_date', $eventDate)
                    ->where('event_time', $eventTime)
                    ->whereIn('status', ['approved', 'pending']);

        if ($excludeEventId) {
            $query->where('event_id', '!=', $excludeEventId);
        }

        return $query->count() === 0;
    }

    /**
     * Get monthly statistics
     */
    public static function getMonthlyStats($month = null, $year = null)
    {
        $month = $month ?? date('m');
        $year = $year ?? date('Y');
        
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        
        $events = self::whereBetween('event_date', [$startDate, $endDate])
                     ->whereIn('status', ['approved', 'pending'])
                     ->get();
        
        return [
            'total_events' => $events->count(),
            'total_guests' => $events->sum('number_of_guests'),
            'total_revenue' => $events->sum('total_price'),
            'unique_dates' => $events->pluck('event_date')->unique()->count(),
            'most_popular_day' => $events->groupBy('event_date')->map->count()->sortDesc()->keys()->first(),
        ];
    }

    /**
     * Get booked dates for a specific month/year
     */
    public static function getBookedDates($month = null, $year = null)
    {
        $month = $month ?? date('m');
        $year = $year ?? date('Y');
        
        return self::whereMonth('event_date', $month)
                   ->whereYear('event_date', $year)
                   ->whereIn('status', ['approved', 'pending'])
                   ->pluck('event_date')
                   ->map(function($date) {
                       return $date->format('Y-m-d');
                   })
                   ->unique()
                   ->toArray();
    }

    /**
     * Get conflicting events
     */
    public static function getConflicts($placeId, $eventDate, $eventTime, $excludeEventId = null)
    {
        // Normalize time format
        if (strlen($eventTime) === 5) {
            $eventTime .= ':00';
        }

        $query = self::where('place_id', $placeId)
                    ->where('event_date', $eventDate)
                    ->where('event_time', $eventTime)
                    ->whereIn('status', ['approved', 'pending']);

        if ($excludeEventId) {
            $query->where('event_id', '!=', $excludeEventId);
        }

        return $query->get();
    }
}