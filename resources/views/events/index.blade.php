<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events - BMW Events Admin</title>

    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* Emergency override for this page */
        .btn {
            background-color: #e9ecef !important;
            color: #495057 !important;
            border: 2px solid #adb5bd !important;
            opacity: 1 !important;
            visibility: visible !important;
            margin: 5px !important;
            padding: 10px 20px !important;
            display: inline-block !important;
        }
        .btn-primary {
            background-color: #6a11cb !important;
            color: white !important;
            border-color: #6a11cb !important;
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
        <li><a href="/">Home</a></li>
        <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li><a href="{{ route('events.create') }}">Create Event</a></li>
        <li><a href="{{ route('calendar.index') }}">Calendar</a></li> <!-- Add this line -->
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
                    <li><a href="{{ route('admin.events.index') }}" class="active"><i class="fas fa-calendar-alt"></i> Manage Events</a></li>
                    <li><a href="{{ route('admin.items', ['type' => 'food']) }}"><i class="fas fa-utensils"></i> Food Items</a></li>
                    <li><a href="{{ route('admin.items', ['type' => 'places']) }}"><i class="fas fa-map-marker-alt"></i> Event Places</a></li>
                    <li><a href="{{ route('admin.items', ['type' => 'designs']) }}"><i class="fas fa-palette"></i> Event Designs</a></li>
                </ul>
            </nav>
        </aside>

        <main class="dashboard-content">
            <h2>Manage Events</h2>

            @if (session('message'))
                <div class="alert alert-success">{{ session('message') }}</div>
            @endif

            <div class="tab-container">
                <a href="{{ route('admin.events.index', ['status' => 'all']) }}"
                   class="tab-button {{ $status === 'all' ? 'tab-active' : 'tab-inactive' }}">
                    All Events ({{ $counts['all'] ?? 0 }})
                </a>

                <a href="{{ route('admin.events.index', ['status' => 'pending']) }}"
                   class="tab-button {{ $status === 'pending' ? 'tab-active' : 'tab-inactive' }}">
                    Pending ({{ $counts['pending'] ?? 0 }})
                </a>

                <a href="{{ route('admin.events.index', ['status' => 'approved']) }}"
                   class="tab-button {{ $status === 'approved' ? 'tab-active' : 'tab-inactive' }}">
                    Approved ({{ $counts['approved'] ?? 0 }})
                </a>

                <a href="{{ route('admin.events.index', ['status' => 'rejected']) }}"
                   class="tab-button {{ $status === 'rejected' ? 'tab-active' : 'tab-inactive' }}">
                    Rejected ({{ $counts['rejected'] ?? 0 }})
                </a>
            </div>

            @if ($events->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No events found for the selected filter.
                </div>
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
                            <th>Event ID</th>
                            <th>Event Name</th>
                            <th>Customer</th>
                            <th>Date & Time</th>
                            <th>Venue</th>
                            <th>Guests</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($events as $event)
                            <tr>
                                <td>#{{ str_pad($event->event_id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $event->event_name }}</td>
                                <td>
                                    <strong>{{ $event->full_name }}</strong><br>
                                    <small>{{ $event->email }}</small><br>
                                    @if (!empty($event->phone))
                                        <small>{{ $event->phone }}</small>
                                    @endif
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}<br>
                                    <small>{{ \Carbon\Carbon::parse($event->event_time)->format('h:i A') }}</small>
                                </td>
                                <td>{{ $event->place_name }}</td>
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
                                        {{ ucfirst($event->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div style="display:flex; gap:0.25rem; flex-wrap:wrap;">
                                        <a href="{{ route('admin.events.show', $event->event_id) }}"
                                           class="btn btn-primary" style="padding:0.25rem 0.5rem;">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        @if ($event->status === 'pending')
                                            <form method="POST" action="{{ route('admin.events.approve', $event->event_id) }}">
                                                @csrf
                                                <button class="btn btn-success" style="padding:0.25rem 0.5rem;"
                                                        onclick="return confirm('Approve this event?')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.events.reject', $event->event_id) }}">
                                                @csrf
                                                <button class="btn btn-danger" style="padding:0.25rem 0.5rem;"
                                                        onclick="return confirm('Reject this event?')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <form method="POST" action="{{ route('admin.events.delete', $event->event_id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger" style="padding:0.25rem 0.5rem;"
                                                    onclick="return confirm('Delete this event permanently?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </main>
    </div>
</div>
</body>
</html>
