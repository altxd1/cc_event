<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar - BMW Events</title>
    
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --color-available: #d4edda;
            --color-partial: #fff3cd;
            --color-booked: #f8d7da;
            --color-today: #cfe2ff;
        }
        
        .calendar-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            padding: 20px;
        }
        
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background: #dee2e6;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .calendar-day-header {
            background: #f8f9fa;
            padding: 15px 10px;
            text-align: center;
            font-weight: bold;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
        }
        
        .calendar-day {
            background: white;
            min-height: 120px;
            padding: 10px;
            position: relative;
            transition: all 0.2s ease;
        }
        
        .calendar-day:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            z-index: 1;
        }
        
        .calendar-day.available {
            background-color: var(--color-available);
        }
        
        .calendar-day.partially-booked {
            background-color: var(--color-partial);
        }
        
        .calendar-day.fully-booked {
            background-color: var(--color-booked);
            cursor: not-allowed;
        }
        
        .calendar-day.today {
            border: 2px solid #0d6efd !important;
        }
        
        .calendar-day.other-month {
            background-color: #f8f9fa;
            opacity: 0.6;
        }
        
        .calendar-day.past {
            opacity: 0.7;
            background-color: #f8f9fa;
        }
        
        .day-number {
            font-size: 1.1em;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .day-number.today {
            color: #0d6efd;
        }
        
        .booking-indicator {
            position: absolute;
            bottom: 5px;
            right: 5px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }
        
        .booking-indicator.available {
            background-color: #28a745;
        }
        
        .booking-indicator.partial {
            background-color: #ffc107;
        }
        
        .booking-indicator.booked {
            background-color: #dc3545;
        }
        
        .event-count {
            font-size: 0.8em;
            color: #666;
            margin-top: 5px;
        }
        
        .event-list {
            margin-top: 5px;
            font-size: 0.75em;
        }
        
        .event-item {
            padding: 2px 5px;
            margin-bottom: 2px;
            background: rgba(0,0,0,0.05);
            border-radius: 3px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .legend {
            display: flex;
            gap: 20px;
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
        }
        
        .stats-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .stats-number {
            font-size: 2em;
            font-weight: bold;
            color: #0d6efd;
        }
        
        .booking-progress {
            height: 10px;
            background: #e9ecef;
            border-radius: 5px;
            overflow: hidden;
            margin: 10px 0;
        }
        
        .booking-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #28a745, #20c997);
        }
        
        @media (max-width: 768px) {
            .calendar-grid {
                grid-template-columns: repeat(1, 1fr);
            }
            
            .calendar-day-header {
                display: none;
            }
            
            .calendar-day {
                min-height: auto;
                padding: 15px;
                margin-bottom: 1px;
            }
        }
    </style>
</head>
<body>
<header>
    <div class="container header-container">
        <div class="logo">
            <i class="fas fa-glass-cheers"></i>
            <a href="/" style="color: white; text-decoration: none;">BMW Events</a>
        </div>
        <nav>
            <ul>
                <li><a href="/">Home</a></li>
                @if (function_exists('isAdmin') && isAdmin())
                    <!-- Admin navigation -->
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.events.index') }}">Manage Events</a></li>
                    <li><a href="{{ route('admin.calendar.index') }}" class="active">Calendar</a></li>
                @else
                    <!-- User navigation -->
                    <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('events.create') }}">Create Event</a></li>
                    <li><a href="{{ route('calendar.index') }}" class="active">Calendar</a></li>
                @endif
            </ul>
        </nav>
        <div class="auth-buttons">
            @if (function_exists('isLoggedIn') && isLoggedIn())
                <span style="color: white; margin-right: 1rem;">
                    {{ function_exists('isAdmin') && isAdmin() ? 'Admin Panel' : 'Welcome, '.(session('full_name') ?? 'User') }}
                </span>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary">Logout</button>
                </form>
            @else
                <a href="{{ route('login.form') }}" class="btn btn-primary">Login</a>
                <a href="{{ route('register.form') }}" class="btn btn-secondary">Register</a>
            @endif
        </div>
    </div>
</header>

<div class="container py-4">
    <div class="calendar-container">
        <!-- Calendar Header -->
        <div class="calendar-header">
            <div>
                <h2 class="mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Event Calendar
                    @if($selectedPlace)
                        - {{ $selectedPlace->place_name }}
                    @endif
                </h2>
                <p class="text-muted mb-0">View venue availability and booked dates</p>
            </div>
            
            <div class="d-flex gap-2">
                <a href="{{ route('calendar.index', ['month' => $month - 1, 'year' => $year, 'place_id' => $selectedPlaceId]) }}" 
                   class="btn btn-outline-primary">
                    <i class="fas fa-chevron-left"></i>
                </a>
                
                <span class="btn btn-light">
                    {{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}
                </span>
                
                <a href="{{ route('calendar.index', ['month' => $month + 1, 'year' => $year, 'place_id' => $selectedPlaceId]) }}" 
                   class="btn btn-outline-primary">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-md-8">
                <form method="GET" action="{{ route('calendar.index') }}" class="row g-3">
                    <div class="col-md-6">
                        <label for="place_id" class="form-label">Filter by Venue</label>
                        <select name="place_id" id="place_id" class="form-select" onchange="this.form.submit()">
                            <option value="">All Venues</option>
                            @foreach($places as $place)
                                <option value="{{ $place->place_id }}" {{ $selectedPlaceId == $place->place_id ? 'selected' : '' }}>
                                    {{ $place->place_name }} (Capacity: {{ $place->capacity }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="month" class="form-label">Month</label>
                        <select name="month" id="month" class="form-select" onchange="this.form.submit()">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="year" class="form-label">Year</label>
                        <select name="year" id="year" class="form-select" onchange="this.form.submit()">
                            @for($y = date('Y') - 1; $y <= date('Y') + 2; $y++)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </form>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Booked Dates</small>
                            <div class="stats-number">
                                @php
                                    $bookedCount = count($booked_dates);
                                    $daysInMonth = $currentDate->daysInMonth;
                                @endphp
                                {{ $bookedCount }}/{{ $daysInMonth }}
                            </div>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">Availability</small>
                            <div class="stats-number">
                                @php
                                    $availabilityPercentage = $daysInMonth > 0 ? round(100 - ($bookedCount / $daysInMonth * 100)) : 100;
                                @endphp
                                {{ $availabilityPercentage }}%
                            </div>
                        </div>
                    </div>
                    <div class="booking-progress">
                        <div class="booking-progress-bar" style="width: {{ 100 - $availabilityPercentage }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Calendar -->
        <div class="calendar-grid">
            <!-- Day Headers -->
            <div class="calendar-day-header">Sun</div>
            <div class="calendar-day-header">Mon</div>
            <div class="calendar-day-header">Tue</div>
            <div class="calendar-day-header">Wed</div>
            <div class="calendar-day-header">Thu</div>
            <div class="calendar-day-header">Fri</div>
            <div class="calendar-day-header">Sat</div>
            
            <!-- Calendar Days -->
            @foreach($calendarDays as $day)
                @if(is_null($day))
                    <div class="calendar-day other-month"></div>
                @else
                    @php
                        $isBooked = in_array($day['formatted_date'], $booked_dates);
                        $isToday = $day['is_today'];
                        $isWeekend = $day['is_weekend'];
                        $eventCount = count($day['events']);
                        
                        // Determine booking level
                        if ($eventCount === 0) {
                            $bookingLevel = 'available';
                            $bookingIndicator = 'available';
                        } elseif ($eventCount <= 2) {
                            $bookingLevel = 'partially-booked';
                            $bookingIndicator = 'partial';
                        } else {
                            $bookingLevel = 'fully-booked';
                            $bookingIndicator = 'booked';
                        }
                        
                        $classes = ['calendar-day'];
                        if ($isBooked) {
                            $classes[] = 'fully-booked';
                        } else {
                            $classes[] = $bookingLevel;
                        }
                        if ($isToday) {
                            $classes[] = 'today';
                        }
                        if ($isWeekend) {
                            $classes[] = 'weekend';
                        }
                    @endphp
                    
                    <div class="{{ implode(' ', $classes) }}"
                         @if(!$isBooked && $eventCount < 3)
                            onclick="window.location='{{ route('events.create', ['date' => $day['formatted_date']]) }}'"
                            style="cursor: pointer;"
                         @else
                            style="cursor: not-allowed;"
                         @endif
                         title="{{ $isBooked ? 'Fully Booked' : ($eventCount > 0 ? $eventCount . ' event(s)' : 'Available') }}">
                        
                        <div class="day-number {{ $isToday ? 'today' : '' }}">
                            {{ $day['day'] }}
                            @if($isToday)
                                <small class="badge bg-primary">Today</small>
                            @endif
                        </div>
                        
                        @if($eventCount > 0)
                            <div class="event-count">
                                <small>
                                    <i class="fas fa-calendar-check"></i>
                                    {{ $eventCount }} event(s)
                                </small>
                            </div>
                            
                            <div class="event-list">
                                @foreach($day['events']->take(2) as $event)
                                    <div class="event-item" title="{{ $event->event_name }} ({{ $event->event_time }})">
                                        <i class="fas fa-circle" style="color: {{ $event->status == 'approved' ? '#28a745' : '#ffc107' }}; font-size: 0.6em;"></i>
                                        {{ \Illuminate\Support\Str::limit($event->event_name, 15) }}
                                    </div>
                                @endforeach
                                @if($eventCount > 2)
                                    <div class="event-item text-center">
                                        <small>+{{ $eventCount - 2 }} more</small>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="event-count">
                                <small class="text-success">
                                    <i class="fas fa-check-circle"></i> Available
                                </small>
                            </div>
                        @endif
                        
                        <div class="booking-indicator {{ $bookingIndicator }}"></div>
                    </div>
                @endif
            @endforeach
        </div>
        
        <!-- Legend -->
        <div class="legend">
            <div class="legend-item">
                <div class="legend-color" style="background-color: var(--color-available);"></div>
                <span>Available (0 events)</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: var(--color-partial);"></div>
                <span>Partial (1-2 events)</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: var(--color-booked);"></div>
                <span>Fully Booked (3+ events)</span>
            </div>
            <div class="legend-item">
                <div class="badge bg-primary">Today</div>
                <span>Current Day</span>
            </div>
        </div>
        
        <!-- Statistics -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="stats-card">
                    <h5><i class="fas fa-chart-bar me-2"></i> Monthly Statistics</h5>
                    <div class="row mt-3">
                        <div class="col-6">
                            <small class="text-muted">Total Events</small>
                            <div class="stats-number">{{ $monthlyStats['total_events'] ?? 0 }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Total Guests</small>
                            <div class="stats-number">{{ $monthlyStats['total_guests'] ?? 0 }}</div>
                        </div>
                        <div class="col-6 mt-3">
                            <small class="text-muted">Total Revenue</small>
                            <div class="stats-number">{{ \App\Helpers\CurrencyHelper::format($monthlyStats['total_revenue'] ?? 0) }}</div>
                        </div>
                        <div class="col-6 mt-3">
                            <small class="text-muted">Unique Dates</small>
                            <div class="stats-number">{{ $monthlyStats['unique_dates'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="stats-card">
                    <h5><i class="fas fa-fire me-2"></i> Popular Date</h5>
                    @if(isset($monthlyStats['most_popular_day']))
                        <div class="mt-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="stats-number">
                                        {{ \Carbon\Carbon::parse($monthlyStats['most_popular_day'])->format('F j, Y') }}
                                    </div>
                                    <small class="text-muted">Most events scheduled</small>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-2x text-muted"></i>
                            <p class="mt-2 mb-0">No events this month</p>
                        </div>
                    @endif
                </div>
                
                <!-- Quick Actions -->
                <div class="stats-card mt-3">
                    <h5><i class="fas fa-bolt me-2"></i> Quick Actions</h5>
                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ route('events.create') }}" class="btn btn-success">
                            <i class="fas fa-plus-circle me-2"></i> Book New Event
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                            <i class="fas fa-tachometer-alt me-2"></i> Go to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer style="margin-top: 2rem;">
    <div class="container">
        <div class="footer-content">
            <div class="logo">
                <i class="fas fa-glass-cheers"></i>
                <span>BMW Events</span>
            </div>
            <div>
                <p>&copy; 2026 BMW Events. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Tooltip initialization
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
</body>
</html>