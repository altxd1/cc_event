<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BMW Events</title>

    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Inline styles for the mini calendar on the admin dashboard -->
    <style>
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
            border: 3px solid #cfe2ff !important;
        }
        .calendar-day.weekend {
            background-color: #f8f9fa;
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
            color: #cfe2ff;
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
        .status-pending { background-color: #ffc107; }
        .status-approved { background-color: #28a745; }
        .status-rejected { background-color: #dc3545; }
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
            display: block;
            text-decoration: none;
            color: inherit;
        }
        .event-item:hover {
            background: rgba(0,0,0,0.07);
            transform: translateX(2px);
        }
        .event-item.pending { border-left-color: #ffc107; }
        .event-item.approved { border-left-color: #28a745; }
        .event-item.rejected { border-left-color: #dc3545; }
        .event-time {
            font-weight: bold;
            color: #495057;
            margin-right: 5px;
        }
        .event-name {
            color: #6c757d;
        }
        .empty-day {
            color: #adb5bd;
            font-style: italic;
            font-size: 0.85em;
            text-align: center;
            padding: 10px;
        }
        .legend-container {
            display: flex;
            gap: 20px;
            margin-top: 15px;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.8em;
        }
    </style>
</head>
<body>
<header>
    <div class="container header-container">
        <div class="logo">
            <i class="fas fa-glass-cheers"></i>
            <a href="{{ url('/') }}" style="color: white; text-decoration: none;">BMW Events</a>
        </div>

        <nav>
            <ul>
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="{{ route('admin.events.index') }}">Manage Events</a></li>
                <li><a href="{{ route('admin.items', ['type' => 'food']) }}">Manage Items</a></li>
                <li><a href="{{ route('admin.messages.index') }}">Inbox</a></li>
            </ul>
        </nav>

        <div class="auth-buttons">
            <span style="color: white; margin-right: 1rem;">Admin: {{ session('full_name') }}</span>
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
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="active">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.events.index') }}" class="{{ request()->routeIs('admin.events.*') && !request()->routeIs('admin.calendar.*') ? 'active' : '' }}">
                            <i class="fas fa-calendar-alt"></i> Manage Events
                        </a>
                    </li>
                    <!-- Calendar button added to sidebar -->
                    <li>
                        <a href="{{ route('admin.calendar.index') }}" class="{{ request()->routeIs('admin.calendar.*') ? 'active' : '' }}">
                            <i class="fas fa-calendar"></i> Calendar
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.messages.index') }}" class="{{ request()->routeIs('admin.messages.index') ? 'active' : '' }}">
                            <i class="fas fa-inbox"></i> Inbox
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.messages.create') }}" class="{{ request()->routeIs('admin.messages.create') ? 'active' : '' }}">
                            <i class="fas fa-pen"></i> Compose
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.items', ['type' => 'food']) }}">
                            <i class="fas fa-utensils"></i> Food Items
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.items', ['type' => 'places']) }}">
                            <i class="fas fa-map-marker-alt"></i> Event Places
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.items', ['type' => 'designs']) }}">
                            <i class="fas fa-palette"></i> Event Designs
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="dashboard-content">
            <h2>Admin Dashboard</h2>
            <p>Welcome to the BMW Events administration panel</p>

            <div class="card-grid mt-3">
                <div class="card">
                    <div class="card-content">
                        <h3 class="card-title">Total Events</h3>
                        <p style="font-size: 2.5rem; font-weight: bold; color: #6a11cb;">
                            {{ $totalEvents }}
                        </p>
                        <p>All events created</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-content">
                        <h3 class="card-title">Pending Events</h3>
                        <p style="font-size: 2.5rem; font-weight: bold; color: #ffc107;">
                            {{ $pendingEvents }}
                        </p>
                        <p>Awaiting approval</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-content">
                        <h3 class="card-title">Total Users</h3>
                        <p style="font-size: 2.5rem; font-weight: bold; color: #28a745;">
                            {{ $totalUsers }}
                        </p>
                        <p>Registered users</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-content">
                        <h3 class="card-title">Revenue</h3>
                        <p style="font-size: 2.5rem; font-weight: bold; color: #dc3545;">
                            {{ \App\Helpers\CurrencyHelper::format($revenue) }}
                        </p>
                        <p>Total from approved events</p>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <h3>Recent Events</h3>

                @if ($recentEvents->isEmpty())
                    <div class="alert alert-info">No events found.</div>
                @else
                    @php
                        $status_colors = [
                            'pending' => '#ffc107',
                            'approved' => '#28a745',
                            'rejected' => '#dc3545',
                            'completed' => '#007bff',
                        ];
                    @endphp

                    <div class="table-container">
                        <table>
                            <thead>
                            <tr>
                                <th>Event Name</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Guests</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($recentEvents as $event)
                                <tr>
                                    <td>{{ $event->event_name }}</td>
                                    <td>
                                        {{ $event->full_name }}<br>
                                        <small>{{ $event->email }}</small>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}</td>
                                    <td>{{ $event->number_of_guests }}</td>
                                    <td>{{ \App\Helpers\CurrencyHelper::format($event->total_price) }}</td>
                                    <td>
                                        @php $color = $status_colors[$event->status] ?? '#6c757d'; @endphp
                                        <span style="
                                            background-color: {{ $color }};
                                            color: white;
                                            padding: 0.25rem 0.5rem;
                                            border-radius: 20px;
                                            font-size: 0.85rem;
                                            font-weight: bold;
                                        ">
                                            {{ ucfirst($event->status ?? 'unknown') }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.events.show', $event->event_id) }}"
                                           class="btn btn-primary" style="padding: 0.25rem 0.5rem;">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="mt-3">
                <h3>Quick Actions</h3>

                <div class="card-grid">
                    <div class="card">
                        <div class="card-content">
                            <h3 class="card-title">Approve Events</h3>
                            <p>Review and approve pending event requests</p>
                            <a href="{{ route('admin.events.index', ['status' => 'pending']) }}" class="btn btn-success">
                                <i class="fas fa-check-circle"></i> Go to Pending Events
                            </a>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-content">
                            <h3 class="card-title">Add New Food Item</h3>
                            <p>Add new menu options for events</p>
                        <a href="{{ route('admin.items.create', ['type' => 'food']) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Food Item
                        </a>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-content">
                            <h3 class="card-title">Manage Venues</h3>
                            <p>Add or update event venues</p>
                            <a href="{{ route('admin.items', ['type' => 'places']) }}" class="btn btn-primary">
                                <i class="fas fa-building"></i> Manage Venues
                            </a>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-content">
                            <h3 class="card-title">Messages</h3>
                            <p>Send messages to clients or read your inbox</p>
                            <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                                <a href="{{ route('admin.messages.create') }}" class="btn btn-primary">
                                    <i class="fas fa-pen"></i> Compose
                                </a>
                                <a href="{{ route('admin.messages.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-inbox"></i> Inbox
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mini calendar for current month -->
            <div class="mt-4">
                <h3>This Month's Calendar</h3>
                <div class="calendar-grid">
                    <!-- Day headers -->
                    @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $dayName)
                        <div class="calendar-day-header">{{ $dayName }}</div>
                    @endforeach
                    <!-- Calendar days -->
                    @foreach($calendarDays as $day)
                        @if (is_null($day))
                            <div class="calendar-day other-month"></div>
                        @else
                            @php
                                $classes = ['calendar-day'];
                                if ($day['is_today']) $classes[] = 'today';
                                if ($day['is_weekend']) $classes[] = 'weekend';
                                if ($day['is_past']) $classes[] = 'past';
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
                                            <a href="{{ route('admin.events.show', $event->event_id) }}" class="event-item {{ $event->status }}" title="{{ $event->event_name }} - {{ \Carbon\Carbon::parse($event->event_time)->format('h:i A') }}">
                                                <span class="event-time">{{ \Carbon\Carbon::parse($event->event_time)->format('h:i') }}</span>
                                                <span class="event-name">{{ \Illuminate\Support\Str::limit($event->event_name, 12) }}</span>
                                            </a>
                                        @endforeach
                                        @if($day['total_events'] > 3)
                                            <a href="{{ route('admin.calendar.day', $day['formatted_date']) }}" class="view-all-link">
                                                +{{ $day['total_events'] - 3 }} more
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <div class="empty-day">
                                        <i class="fas fa-calendar-times"></i>
                                        <br>No events
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
                <!-- Legend for calendar -->
                <div class="legend-container">
                    <div class="legend-item">
                        <span class="status-badge status-pending">P</span>
                        <small>Pending</small>
                    </div>
                    <div class="legend-item">
                        <span class="status-badge status-approved">A</span>
                        <small>Approved</small>
                    </div>
                    <div class="legend-item">
                        <span class="status-badge status-rejected">R</span>
                        <small>Rejected</small>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<footer>
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
</body>
</html>