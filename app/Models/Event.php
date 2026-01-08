<?php
// app/Models/Event.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // Fixed import
use App\Models\EventPlace;
use App\Models\FoodItem;
use App\Models\EventDesign;
use App\Models\User;

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
     * Get the user associated with the event.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the place (venue) where the event is held.
     */
    public function place()
    {
        return $this->belongsTo(EventPlace::class, 'place_id', 'place_id');
    }

    /**
     * Get the food package selected for the event.
     */
    public function food()
    {
        return $this->belongsTo(FoodItem::class, 'food_id', 'food_id');
    }

    /**
     * Get the design theme selected for the event.
     */
    public function design()
    {
        return $this->belongsTo(EventDesign::class, 'design_id', 'design_id');
    }

    /**
     * Get the package associated with this event.
     */
    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id', 'package_id');
    }

    /**
     * Get the guests for this event.
     */
    public function guests()
    {
        return $this->hasMany(Guest::class, 'event_id', 'event_id');
    }

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
    /**
     * Get monthly statistics for a given month/year. When a user ID is supplied,
     * the statistics will be scoped to that user's events only. Otherwise, all
     * events (approved and pending) are considered. This helps ensure that
     * regular users do not see aggregate revenue or guest counts across other
     * users' events while still allowing admins to see system-wide metrics.
     *
     * @param  string|null  $month  The numerical month (01-12) to query. Defaults to current month.
     * @param  string|null  $year   The four-digit year to query. Defaults to current year.
     * @param  int|null     $userId Optional user ID to scope the stats to. If null, all users' events are considered.
     * @return array
     */
    public static function getMonthlyStats($month = null, $year = null, $userId = null)
    {
        $month = $month ?? date('m');
        $year = $year ?? date('Y');

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $query = self::whereBetween('event_date', [$startDate, $endDate])
            ->whereIn('status', ['approved', 'pending']);

        // If a user ID is supplied, limit the query to that user's events. This is
        // important for regular users, who should only see statistics related
        // to their own bookings. Admins will pass a null $userId and thus see
        // overall statistics.
        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        $events = $query->get();

        return [
            'total_events'   => $events->count(),
            'total_guests'   => $events->sum('number_of_guests'),
            'total_revenue'  => $events->sum('total_price'),
            'unique_dates'   => $events->pluck('event_date')->unique()->count(),
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

    /**
     * Calculate the total price for an event based on the selected options
     * and dynamic pricing rules. Weekend surcharges and seasonal pricing
     * are applied automatically.
     *
     * @param  \App\Models\EventPlace|null  $place
     * @param  \App\Models\FoodItem|null    $food
     * @param  \App\Models\EventDesign|null $design
     * @param  \App\Models\Package|null     $package
     * @param  int                            $guests
     * @param  string                         $eventDate (Y-m-d format)
     * @return float
     */
    public static function calculatePrice($place, $food, $design, $package, int $guests, string $eventDate): float
    {
        $placePrice = $place->price ?? 0;
        $foodPricePerPerson = $food->price_per_person ?? 0;
        $designPrice = $design->price ?? 0;
        $packagePrice = $package->price ?? 0;

        $base = $placePrice + ($foodPricePerPerson * $guests) + $designPrice + $packagePrice;

        // Weekend surcharge (10% on Saturdays and Sundays)
        $date = Carbon::parse($eventDate);
        $isWeekend = $date->isWeekend();
        if ($isWeekend) {
            $base *= 1.10;
        }

        // Seasonal pricing (peak season June–August adds 20%)
        $month = $date->month;
        if (in_array($month, [6, 7, 8], true)) {
            $base *= 1.20;
        }

        return round($base, 2);
    }
}