<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Calendar - BMW Events</title>

    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --color-pending: #ffc107;
            --color-approved: #28a745;
            --color-rejected: #dc3545;
            --color-today: #cfe2ff;
            --color-weekend: #f8f9fa;
        }
        
        .admin-calendar-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            padding: 20px;
        }
        
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background: #dee2e6;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 20px;
        }
        
        .calendar-day-header {
            background: #343a40;
            color: white;
            padding: 15px 10px;
            text-align: center;
            font-weight: bold;
            border-bottom: 2px solid #495057;
        }
        
        .calendar-day {
            background: white;
            min-height: 150px;
            padding: 10px;
            position: relative;
            transition: all 0.2s ease;
        }
        
        .calendar-day:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1;
        }
        
        .calendar-day.today {
            border: 3px solid #0d6efd !important;
        }
        
        .calendar-day.weekend {
            background-color: var(--color-weekend);
        }
        
        .calendar-day.past {
            opacity: 0.7;
            background-color: #f8f9fa;
        }
        
        .day-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .day-number {
            font-size: 1.2em;
            font-weight: bold;
            color: #495057;
        }
        
        .day-number.today {
            color: #0d6efd;
        }
        
        .event-status-badges {
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
        }
        
        .status-badge {
            font-size: 0.7em;
            padding: 2px 6px;
            border-radius: 10px;
            color: white;
            font-weight: bold;
        }
        
        .status-pending { background-color: var(--color-pending); }
        .status-approved { background-color: var(--color-approved); }
        .status-rejected { background-color: var(--color-rejected); }
        
        .event-list {
            margin-top: 8px;
            max-height: 100px;
            overflow-y: auto;
        }
        
        .event-item {
            padding: 4px 6px;
            margin-bottom: 3px;
            background: rgba(0,0,0,0.03);
            border-radius: 4px;
            border-left: 3px solid #6c757d;
            font-size: 0.8em;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .event-item:hover {
            background: rgba(0,0,0,0.07);
            transform: translateX(2px);
        }
        
        .event-item.pending { border-left-color: var(--color-pending); }
        .event-item.approved { border-left-color: var(--color-approved); }
        .event-item.rejected { border-left-color: var(--color-rejected); }
        
        .event-time {
            font-weight: bold;
            color: #495057;
            margin-right: 5px;
        }
        
        .event-name {
            color: #6c757d;
        }
        
        .admin-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-top: 4px solid #6c757d;
        }
        
        .stat-card.pending { border-top-color: var(--color-pending); }
        .stat-card.approved { border-top-color: var(--color-approved); }
        .stat-card.rejected { border-top-color: var(--color-rejected); }
        .stat-card.revenue { border-top-color: #0d6efd; }
        
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            margin: 5px 0;
        }
        
        .stat-label {
            font-size: 0.9em;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .empty-day {
            color: #adb5bd;
            font-style: italic;
            font-size: 0.85em;
            text-align: center;
            padding: 10px;
        }
        
        .view-all-link {
            display: block;
            text-align: center;
            margin-top: 5px;
            font-size: 0.8em;
            color: #0d6efd;
            text-decoration: none;
        }
        
        .view-all-link:hover {
            text-decoration: underline;
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
            
            .admin-stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<header>
    <div class="container header-container">
        <div class="logo">
            <i class="fas fa-glass-cheers"></i>
            <a href="{{ url('/') }}" style="color: white; text-decoration: none;">BMW Events Admin</a>
        </div>

        <nav>
            <ul>
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.events.index') }}">Manage Events</a></li>
                <li><a href="{{ route('admin.calendar.index') }}" class="active">Calendar</a></li>
                <li><a href="{{ route('admin.items', ['type' => 'food']) }}">Manage Items</a></li>
            </ul>
        </nav>

        <div class="auth-buttons">
            <span style="color: white; margin-right: 1rem;">Admin Panel</span>
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-secondary">Logout</button>
            </form>
        </div>
    </div>
</header>

<div class="container">
    <div class="dashboard-container">
        <aside class="sidebar">
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="{{ route('admin.events.index') }}" class="{{ request()->routeIs('admin.events.*') && !request()->routeIs('admin.calendar.*') ? 'active' : '' }}"><i class="fas fa-calendar-alt"></i> Manage Events</a></li>
                    <li><a href="{{ route('admin.calendar.index') }}" class="{{ request()->routeIs('admin.calendar.*') ? 'active' : '' }}"><i class="fas fa-calendar"></i> Calendar View</a></li>
                    <li><a href="{{ route('admin.items', ['type' => 'food']) }}"><i class="fas fa-utensils"></i> Food Items</a></li>
                    <li><a href="{{ route('admin.items', ['type' => 'places']) }}"><i class="fas fa-map-marker-alt"></i> Event Places</a></li>
                    <li><a href="{{ route('admin.items', ['type' => 'designs']) }}"><i class="fas fa-palette"></i> Event Designs</a></li>
                </ul>
            </nav>
        </aside>

        <main class="dashboard-content">
            <div class="admin-calendar-container">
                <!-- Calendar Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-0">
                            <i class="fas fa-calendar-check me-2"></i>
                            Admin Calendar
                            @if($selectedPlace)
                                <span class="text-muted">- {{ $selectedPlace->place_name }}</span>
                            @endif
                        </h2>
                        <p class="text-muted mb-0">Overview of all events by status</p>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.calendar.index', ['month' => $currentDate->copy()->subMonth()->format('m'), 'year' => $currentDate->copy()->subMonth()->format('Y'), 'place_id' => $selectedPlaceId]) }}" 
                           class="btn btn-outline-primary">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        
                        <span class="btn btn-light">
                            {{ $currentDate->format('F Y') }}
                        </span>
                        
                        <a href="{{ route('admin.calendar.index', ['month' => $currentDate->copy()->addMonth()->format('m'), 'year' => $currentDate->copy()->addMonth()->format('Y'), 'place_id' => $selectedPlaceId]) }}" 
                           class="btn btn-outline-primary">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Filters -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <form method="GET" action="{{ route('admin.calendar.index') }}" class="row g-3">
                            <div class="col-md-6">
                                <label for="place_id" class="form-label">Filter by Venue</label>
                                <select name="place_id" id="place_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">All Venues</option>
                                    @foreach($places as $place)
                                        <option value="{{ $place->place_id }}" {{ $selectedPlaceId == $place->place_id ? 'selected' : '' }}>
                                            {{ $place->place_name }}
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
                </div>
                
                <!-- Admin Statistics -->
                <div class="admin-stats-grid">
                    <div class="stat-card pending">
                        <div class="stat-label">Pending Events</div>
                        <div class="stat-number">{{ $monthlyStats['pending_events'] ?? 0 }}</div>
                        <small>Awaiting approval</small>
                    </div>
                    
                    <div class="stat-card approved">
                        <div class="stat-label">Approved Events</div>
                        <div class="stat-number">{{ $monthlyStats['approved_events'] ?? 0 }}</div>
                        <small>Confirmed bookings</small>
                    </div>
                    
                    <div class="stat-card rejected">
                        <div class="stat-label">Rejected Events</div>
                        <div class="stat-number">{{ $monthlyStats['rejected_events'] ?? 0 }}</div>
                        <small>Declined requests</small>
                    </div>
                    
                    <div class="stat-card revenue">
                        <div class="stat-label">Monthly Revenue</div>
                        <div class="stat-number">${{ number_format($monthlyStats['total_revenue'] ?? 0, 2) }}</div>
                        <small>From approved events</small>
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
                                $classes = ['calendar-day'];
                                if ($day['is_today']) {
                                    $classes[] = 'today';
                                }
                                if ($day['is_weekend']) {
                                    $classes[] = 'weekend';
                                }
                                if ($day['is_past']) {
                                    $classes[] = 'past';
                                }
                            @endphp
                            
                            <div class="{{ implode(' ', $classes) }}">
                                <div class="day-header">
                                    <div class="day-number {{ $day['is_today'] ? 'today' : '' }}">
                                        {{ $day['day'] }}
                                    </div>
                                    
                                    <div class="event-status-badges">
                                        @if($day['pending_count'] > 0)
                                            <span class="status-badge status-pending" title="Pending events">{{ $day['pending_count'] }}</span>
                                        @endif
                                        @if($day['approved_count'] > 0)
                                            <span class="status-badge status-approved" title="Approved events">{{ $day['approved_count'] }}</span>
                                        @endif
                                        @if($day['rejected_count'] > 0)
                                            <span class="status-badge status-rejected" title="Rejected events">{{ $day['rejected_count'] }}</span>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($day['total_events'] > 0)
                                    <div class="event-list">
                                        @foreach($day['events']->take(3) as $event)
                                            <a href="{{ route('admin.events.show', $event->event_id) }}" 
                                               class="event-item {{ $event->status }}" 
                                               title="{{ $event->event_name }} - {{ $event->event_time }}">
                                                <span class="event-time">{{ \Carbon\Carbon::parse($event->event_time)->format('h:i A') }}</span>
                                                <span class="event-name">{{ \Illuminate\Support\Str::limit($event->event_name, 15) }}</span>
                                            </a>
                                        @endforeach
                                        @if($day['total_events'] > 3)
                                            <a href="{{ route('admin.calendar.day', $day['formatted_date']) }}" class="view-all-link">
                                                View all {{ $day['total_events'] }} events
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <div class="empty-day">
                                        <i class="fas fa-calendar-times"></i>
                                        <br>
                                        No events
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
                
                <!-- Legend -->
                <div class="d-flex flex-wrap gap-3 mt-4 p-3 bg-light rounded">
                    <div class="d-flex align-items-center gap-2">
                        <div class="status-badge status-pending">P</div>
                        <small>Pending Events</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="status-badge status-approved">A</div>
                        <small>Approved Events</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="status-badge status-rejected">R</div>
                        <small>Rejected Events</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="badge bg-primary">Today</div>
                        <small>Current Day</small>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    // Auto-refresh every 5 minutes to show real-time updates
    setTimeout(function() {
        window.location.reload();
    }, 300000); // 5 minutes
</script>
</body>
</html>